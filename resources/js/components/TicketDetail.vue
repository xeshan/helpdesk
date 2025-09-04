<template>
    <div class="card" v-if="ticket">
        <h2 style="margin-top:0">{{ ticket.subject }}</h2>
        <p style="white-space:pre-wrap">{{ ticket.body }}</p>
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top:1rem;">
            <div>
                <label>Status</label>
                <select class="select" v-model="ticket.status" @change="save({ status: ticket.status })">
                    <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
                </select>
            </div>
        <div>
        <label>Category (override)</label>
        <select class="select" v-model="ticket.category" @change="save({ category: ticket.category })">
            <option :value="null">—</option>
            <option v-for="c in categories" :key="c" :value="c">{{ c }}</option>
        </select>
    </div>
    <div>
        <label>Internal Note</label>
        <textarea class="textarea" v-model="ticket.note" @blur="save({ note: ticket.note })"></textarea>
    </div>
    <div>
        <label>AI Explanation</label>
        <div class="card" style="min-height:60px">{{ ticket.explanation || '—' }}</div>
        <div style="margin-top:.5rem">Confidence: <strong>{{ ticket.confidence ?? '—' }}</strong></div>
    </div>
    </div>
    <div style="display:flex; gap:.5rem; margin-top:1rem;">
        <button class="button" @click="classify" :disabled="loading">{{ loading ? 'Classifying…' : 'Run Classification' }}</button>
        <router-link class="button button--ghost" to="/tickets">Back</router-link>
    </div>
    </div>
</template>

<script>
import api from '../api'
export default {
    name: 'TicketDetail',
    props: ['id'],
    data() {
        return {
            ticket: null,
            statuses: ['open','in_progress','resolved','closed'],
            categories: ['billing','technical','account','sales','general'],
            loading: false,
        }
    },
    methods: {
        async load() { const { data } = await api.getTicket(this.$route.params.id); this.ticket = data },
        async save(payload) {
            const { data } = await api.updateTicket(this.ticket.id, payload)
            this.ticket = data
        },
        async classify() {
            this.loading = true
            await api.classifyTicket(this.ticket.id)
            const start = Date.now()
            const poll = async () => {
                const { data } = await api.getTicket(this.ticket.id)
                this.ticket = data
                const done = !!data.explanation && data.confidence !== null
                if (done || Date.now() - start > 8000) {
                    this.loading = false
                } else setTimeout(poll, 800)
            }
            poll()
        },
    },
    async mounted() { await this.load() },
}
</script>
