<template>
    <section class="grid" style="display:grid; gap:1rem; grid-template-columns: repeat(3, 1fr);">
        <div class="card">
            <h3>Tickets</h3>
            <div style="font-size:2rem; font-weight:700;">{{ totals.total }}</div>
            <div class="muted">Total</div>
        </div>
        <div class="card">
            <h3>Per Status</h3>
            <ul>
                <li v-for="(v,k) in totals.perStatus" :key="k">{{ k }}: <strong>{{ v }}</strong></li>
            </ul>
        </div>
        <div class="card">
            <h3>Per Category</h3>
            <ul>
                <li v-for="(v,k) in totals.perCategory" :key="k">{{ k }}: <strong>{{ v }}</strong></li>
            </ul>
        </div>
        <div class="card">
            <h3>Category Distribution</h3>
            <canvas id="chart" height="400"></canvas>
        </div>
    </section>
</template>

<script>
import api from '../api'
import { Chart } from 'chart.js/auto'

export default {
    name: 'Dashboard',
    data() { return { totals: { perStatus: {}, perCategory: {}, total: 0 }, chart: null } },
    methods: {
        async load() {
            const { data } = await api.getStats()
            this.totals = data
            this.draw()
        },
        draw() {
            const ctx = document.getElementById('chart')
            if (this.chart) this.chart.destroy()
                const labels = Object.keys(this.totals.perCategory)
                const values = Object.values(this.totals.perCategory)
                this.chart = new Chart(ctx, {
                type: 'pie',
                data: { labels, datasets: [{ data: values }] },
                options: { responsive: false, maintainAspectRatio : false }
            })
        }
    },
    async mounted() { await this.load() },
}
</script>
