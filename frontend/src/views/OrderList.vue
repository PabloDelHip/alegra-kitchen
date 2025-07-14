<script setup>
import { ref, watch, onMounted } from 'vue'
import axios from '../axios'

const orders = ref([])
const meta = ref({})
const openOrders = ref([])
const getUTCDate = (date) => {
  const userDate = new Date(date)
  return new Date(Date.UTC(
    userDate.getFullYear(),
    userDate.getMonth(),
    userDate.getDate()
  )).toISOString().slice(0, 10) // YYYY-MM-DD
}
const selectedDate = ref(getUTCDate(new Date()))
const currentPage = ref(1)
const perPage = 10
const loading = ref(false)

const parseMissingIngredients = (jsonString) => {
  try {
    return JSON.parse(jsonString)
  } catch {
    return []
  }
}

const getDishSummary = (dishes) => {
  const ready = dishes.filter(d => d.status === 'ready').length
  const failed = dishes.filter(d => d.status === 'failed').length
  return { ready, failed }
}

const statusConfig = {
  preparing: { color: 'border-primary', label: 'Preparando', badge: 'badge bg-primary' },
  ready: { color: 'border-success', label: 'Listo', badge: 'badge bg-success' },
  waiting: { color: 'border-secondary', label: 'Esperando', badge: 'badge bg-secondary' },
  partial: { color: 'border-warning', label: 'Parcial', badge: 'badge bg-warning' },
  failed: { color: 'border-danger', label: 'Fallido', badge: 'badge bg-danger' }
}

const toggleOrder = (orderId) => {
  if (openOrders.value.includes(orderId)) {
    openOrders.value = openOrders.value.filter(id => id !== orderId)
  } else {
    openOrders.value.push(orderId)
  }
}

function formatDate(datetime) {
  const date = new Date(datetime)
  const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }
  return date.toLocaleString('es-MX', options)
}

const loadOrders = async () => {
  loading.value = true
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/orders/grouped`, {
      params: {
        date: selectedDate.value,
        page: currentPage.value,
        per_page: perPage
      }
    })
    orders.value = response.data.data
    meta.value = {
      current_page: response.data.current_page,
      last_page: response.data.last_page,
      total: response.data.total
    }
  } catch (error) {
    console.error('Error al cargar órdenes:', error)
  } finally {
    loading.value = false
  }
}

const nextPage = () => {
  if (meta.value.current_page < meta.value.last_page) {
    currentPage.value++
    loadOrders()
  }
}

const prevPage = () => {
  if (meta.value.current_page > 1) {
    currentPage.value--
    loadOrders()
  }
}

watch(selectedDate, () => {
  currentPage.value = 1
  loadOrders()
})

onMounted(loadOrders)

const currentDate = new Date().toLocaleDateString('es-MX', {
  year: 'numeric',
  month: '2-digit',
  day: '2-digit'
})

const getDisplayName = (name) => {
  const names = {
    tomato: 'Tomate',
    lemon: 'Limón',
    potato: 'Papa',
    rice: 'Arroz',
    ketchup: 'Catsup',
    lettuce: 'Lechuga',
    onion: 'Cebolla',
    cheese: 'Queso',
    meat: 'Carne',
    chicken: 'Pollo',
  }
  return names[name] || name.charAt(0).toUpperCase() + name.slice(1)
}

</script>

<template>
  <div class="card mt-4">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="card-title fw-bold">
      🍽 Historial de ordenes <span class="text-muted">({{ meta.total || 0 }})</span>
    </h5>
    <small class="text-muted">Hoy: {{ currentDate }}</small>
  </div>
  <button class="btn btn-lg btn-primary" @click="loadOrders" :disabled="loading">
    <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status"></span>
    Actualizar
  </button>
</div>

      <div class="d-flex align-items-center mb-3">
        <label class="form-label me-2 mb-0 fw-semibold">Filtrar por fecha:</label>
        <input type="date" v-model="selectedDate" class="form-control form-control-sm w-auto">
      </div>

      <div v-if="loading" class="text-center text-muted my-3">
        <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Cargando...
      </div>

      <div v-else>
        <div v-if="orders.length > 0">
          <div v-for="order in orders" :key="order.id" :class="['order-card border', statusConfig[order.status]?.color, 'mb-3']">
            <div class="d-flex justify-content-between align-items-center p-3 rounded bg-white shadow-sm cursor-pointer" @click="toggleOrder(order.id)">
              <div class="d-flex align-items-center">
                <span class="me-2">›</span>
                <span class="fw-semibold">#{{ order.order_code }}</span>
                <span :class="statusConfig[order.status]?.badge + ' ms-2'">
                  {{ statusConfig[order.status]?.label }}
                </span>
              </div>
              <small class="text-muted">
                Creado: {{ formatDate(order.created_at) }}
                <span class="fw-bold ms-2">{{ order.elapsed }}</span>
                <span v-if="getDishSummary(order.dishes).ready != 0" class="ms-3 text-success">✔ {{ getDishSummary(order.dishes).ready }} listo(s)</span>
                <span v-if="getDishSummary(order.dishes).failed != 0" class="ms-2 text-danger">✖ {{ getDishSummary(order.dishes).failed }} fallido(s)</span>
              </small>
            </div>

            <div v-if="openOrders.includes(order.id)" class="bg-light p-3 border-top">
              <p class="mb-2 fw-semibold text-muted">Platillos:</p>
              <div
  v-for="dish in order.dishes"
  :key="dish.id"
  class="d-flex flex-column bg-white border rounded p-2 mb-2 shadow-sm"
>
  <div class="d-flex justify-content-between align-items-center">
    <span>{{ dish.recipe?.name || 'Sin nombre' }}</span>
    <span :class="(statusConfig[dish.status]?.badge || 'badge bg-secondary')">
      {{ statusConfig[dish.status]?.label || 'Sin estado' }}
    </span>
  </div>

  <!-- Mostrar ingredientes faltantes si aplica -->
  <div v-if="dish.status === 'failed' && dish.missing_ingredients" class="mt-2">
    <small class="text-danger fw-semibold">Ingredientes faltantes:</small>
    <ul class="mb-0">
      <li
        v-for="ingredient in parseMissingIngredients(dish.missing_ingredients)"
        :key="ingredient.name"
      >
      {{ getDisplayName(ingredient.name) }} — Faltaban: {{ ingredient.needed }}
      </li>
    </ul>
  </div>
</div>
            </div>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3">
            <button class="btn btn-sm btn-outline-primary" :disabled="meta.current_page === 1" @click="prevPage">Anterior</button>
            <small class="text-muted">Página {{ meta.current_page }} de {{ meta.last_page }}</small>
            <button class="btn btn-sm btn-outline-primary" :disabled="meta.current_page === meta.last_page" @click="nextPage">Siguiente</button>
          </div>
        </div>

        <div v-else class="text-center text-muted fw-semibold">
          No hay órdenes para la fecha seleccionada.
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.order-card {
  border-radius: 0.5rem;
  transition: all 0.2s ease-in-out;
}

.order-card:hover {
  background-color: #f8f9fa;
  transform: scale(1.005);
}

.badge {
  font-size: 0.75rem;
  border-radius: 0.5rem;
  padding: 0.25em 0.5em;
}

.cursor-pointer {
  cursor: pointer;
}
</style>
