import axios from 'axios'

const API = axios.create({ baseURL: import.meta.env.VITE_API_BASE || 'http://127.0.0.1:8000/api' })

export default {
  listTickets(params) { return API.get('/tickets', { params }) },
  createTicket(payload) { return API.post('/tickets', payload) },
  getTicket(id) { return API.get(`/tickets/${id}`) },
  updateTicket(id, payload) { return API.patch(`/tickets/${id}`, payload) },
  classifyTicket(id) { return API.post(`/tickets/${id}/classify`) },
  getStats() { return API.get('/stats') },
}
