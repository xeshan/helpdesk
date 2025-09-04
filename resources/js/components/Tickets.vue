<template>
    <section class="ticket-list">
        <div class="ticket-list__toolbar">
            <input v-model="q" class="input" placeholder="Search subject or body..." />
            <select v-model="filterStatus" class="select">
                <option value="">All Statuses</option>
                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
            </select>
            <select v-model="filterCategory" class="select">
                <option value="">All Categories</option>
                <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
            </select>
            <div style="display:flex; gap:.5rem;">
                <button class="button" @click="openNew = true">New Ticket</button>
                <button class="button button--ghost" @click="exportCsv">Export CSV</button>
            </div>
        </div>
        <div class="card">
            <table class="ticket-list__table">
                <thead>
                    <tr>
                        <th class="ticket-list__th">Subject</th>
                        <th class="ticket-list__th">Status</th>
                        <th class="ticket-list__th">Category</th>
                        <th class="ticket-list__th">Conf.</th>
                        <th class="ticket-list__th">Note</th>
                        <th class="ticket-list__th">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="t in paginated" :key="t.id" class="ticket-list__row ticket-list__row--hover">
                        <td class="ticket-list__td">
                            <router-link :to="`/tickets/${t.id}`">{{ t.subject }}</router-link>
                            <span v-if="t.explanation" class="info" :title="t.explanation"> ⓘ </span>
                        </td>
                        <td class="ticket-list__td">
                            <select class="select" v-model="t.status" @change="update(t.id, { status: t.status })">
                                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                            </select>
                        </td>
                        <td class="ticket-list__td">
                            <select class="select" v-model="t.category" @change="update(t.id, { category: t.category })">
                                <option :value="null">—</option>
                                <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
                            </select>
                        </td>
                        <td class="ticket-list__td">{{ t.confidence ?? '—' }}</td>
                        <td class="ticket-list__td">
                            <span v-if="t.note" class="badge">note</span>
                        </td>
                        <td class="ticket-list__td">
                            <button class="button" @click="classify(t)" :disabled="t._loading">
                                <span v-if="!t._loading">Classify</span>
                                <span v-else>Classifying...</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="pagination">
                <button class="button button--ghost" @click="page = Math.max(1, page-1)">Prev</button>
                <span>Page {{ page }} / {{ totalPages }}</span>
                <button class="button button--ghost" @click="page = Math.min(totalPages, page+1)">Next</button>
            </div>
        </div>

        <div v-if="openNew" class="modal" @click.self="openNew = false">
            <div class="modal__body">
                <h3>New Ticket</h3>
                <form @submit.prevent="create">
                    <label>Subject</label>
                        <input v-model.trim="form.subject" class="input" required maxlength="255" />
                    <label>Body</label>
                    <textarea v-model.trim="form.body" class="textarea" required></textarea>
                    <div style="display:flex; gap:.5rem; justify-content:flex-end; margin-top:.75rem;">
                        <button type="button" class="button button--ghost" @click="openNew=false">Cancel</button>
                        <button class="button" :disabled="submitting">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</template>

<script>
import api from '../api'

export default {
    name: 'Tickets',
    data() {
        return {
            items: [],
            q: '',
            filterStatus: '',
            filterCategory: '',
            statuses: ['open','in_progress','resolved','closed'],
            categories: ['billing','technical','account','sales','general'],
            page: 1,
            perPage: 10,
            openNew: false,
            form: { subject: '', body: '' },
            submitting: false,
        }
    },
    computed: {
        filtered() {
            const q = this.q.toLowerCase()
            return this.items.filter(t => {
                const matchesQ = !q || t.subject.toLowerCase().includes(q) || t.body.toLowerCase().includes(q)
                const matchesStatus = !this.filterStatus || t.status === this.filterStatus
                const matchesCat = !this.filterCategory || t.category === this.filterCategory
                return matchesQ && matchesStatus && matchesCat
            })
        },
        totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.perPage)) },
        paginated() {
            const start = (this.page - 1) * this.perPage
            return this.filtered.slice(start, start + this.perPage)
        },
    },
    methods: {
        async load() {
            const { data } = await api.listTickets({ per_page: 100 })
            this.items = data.data.map(t => ({ ...t, _loading: false }))
        },
        async update(id, payload) {
            const { data } = await api.updateTicket(id, payload)
            const idx = this.items.findIndex(x => x.id === id)
            if (idx !== -1) this.items[idx] = { ...this.items[idx], ...data }
        },
        async classify(t) {
            try {
                t._loading = true
                await api.classifyTicket(t.id)
                // Poll for a short time to fetch updated classification
                const start = Date.now()
                const poll = async () => {
                const { data } = await api.getTicket(t.id)
                const idx = this.items.findIndex(x => x.id === t.id)
                if (idx !== -1) this.items[idx] = { ...this.items[idx], ...data, _loading: true }
                const done = !!data.explanation && data.confidence !== null
                if (done || Date.now() - start > 8000) {
                    t._loading = false
                } else {
                    setTimeout(poll, 800)
                }
            }
            poll()
            } catch (e) {
                t._loading = false
                console.error('Classification error:', e.response ? e.response.data : e)
                alert('Failed to queue classification')
            }
        },
        async create() {
            this.submitting = true
            try {
                const { data } = await api.createTicket(this.form)
                this.items.unshift({ ...data, _loading: false })
                this.form = { subject: '', body: '' }
                this.openNew = false
            } finally {
                this.submitting = false
            }
        },
        exportCsv() {
            const header = ['id','subject','status','category','confidence']
            const rows = this.filtered.map(t => [t.id, JSON.stringify(t.subject), t.status, t.category ?? '', t.confidence ?? ''])
            const csv = [header.join(','), ...rows.map(r => r.join(','))].join('\n')
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' })
            const url = URL.createObjectURL(blob)
            const a = document.createElement('a')
            a.href = url
            a.download = 'tickets.csv'
            a.click()
            URL.revokeObjectURL(url)
        },
    },
    async mounted() { await this.load() },
}
</script>
