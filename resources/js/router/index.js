import { createRouter, createWebHistory } from 'vue-router';
import App from '../components/App.vue';
import Tickets from '../components/Tickets.vue';
import TicketDetail from '../components/TicketDetail.vue';
import Dashboard from '../components/Dashboard.vue';
// import '../css/app.css';

const routes = [
  { path: '/', redirect: '/dashboard' },
  { path: '/tickets', component: Tickets },
  { path: '/tickets/:id', component: TicketDetail, props: true },
  { path: '/dashboard', component: Dashboard },
]

const router = createRouter({ history: createWebHistory(), routes })

export default router