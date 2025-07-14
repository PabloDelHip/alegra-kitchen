<template>
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
          <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
            <i class="bi bi-egg-fried text-orange"></i> Últimos Platillos
            <span class="badge bg-success-subtle text-success">Listos: {{ readyCount }}</span>
            <span class="badge bg-danger-subtle text-danger">Fallidos: {{ failedCount }}</span>
          </h5>
        </div>

        <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" @click="fetchOrders" :disabled="isLoading">
          <span v-if="isLoading" class="spinner-border spinner-border-sm"></span>
          <span v-else><i class="bi bi-arrow-clockwise"></i> Actualizar</span>
        </button>
      </div>

      <div class="order-list vstack gap-3">
        <div
          v-for="order in orders"
          :key="order.id"
          class="p-3 bg-white rounded shadow-sm border-start"
          :class="getBorderClass(order.status)"
        >
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <h6 class="fw-semibold mb-1 d-flex align-items-center gap-2">
                {{ order.recipe.name }}
                <span
                  class="badge rounded-pill d-flex align-items-center gap-1"
                  :class="getBadgeClass(order.status)"
                >
                  <i :class="getIconClass(order.status)"></i> {{ capitalize(order.status) }}
                </span>
              </h6>
              <p class="mb-1 text-muted small">
                Orden: {{ order.order.order_code }} <br />
                ID Platillo: #{{ order.id }}
              </p>
            </div>

            <div class="text-end">
              <small class="text-muted">{{ formatDate(order.updated_at) }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
import { onMounted, ref, computed } from 'vue'
import axios from '../axios'

const orders = ref([])
const isLoading = ref(false)

function formatDate(datetime) {
  const date = new Date(datetime)
  const options = { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }
  return date.toLocaleString('es-MX', options)
}

const readyCount = computed(() => orders.value.filter(o => o.status === 'ready').length)
const failedCount = computed(() => orders.value.filter(o => o.status === 'failed').length)

const fetchOrders = async () => {
  isLoading.value = true
  orders.value = []  // ✅ Limpia la lista antes de consultar

  try {
    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/orders/list/all`, {
      params: {
        page: 1,
        per_page: 20
      }
    })

    orders.value = response.data.data
  } catch (error) {
    console.error('❌ Error al obtener las órdenes:', error)
  } finally {
    isLoading.value = false
  }
}

function getBadgeClass(status) {
  return {
    ready: 'text-success bg-success-subtle',
    failed: 'text-danger bg-danger-subtle',
    waiting: 'text-dark bg-light',
    partial: 'text-warning bg-warning-subtle',
    pending: 'text-secondary bg-light'
  }[status] || 'text-dark bg-light'
}

function getBorderClass(status) {
  return {
    ready: 'border-3 border-success',
    failed: 'border-3 border-danger',
    waiting: 'border-3 border-secondary',
    partial: 'border-3 border-warning',
    pending: 'border-3 border-secondary'
  }[status] || 'border-3 border-secondary'
}

function getIconClass(status) {
  return {
    ready: 'bi bi-check-circle',
    failed: 'bi bi-x-circle',
    waiting: 'bi bi-clock',
    partial: 'bi bi-exclamation-circle',
    pending: 'bi bi-clock'
  }[status] || 'bi bi-info-circle'
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

onMounted(fetchOrders)
</script>

<style scoped>
.text-orange {
  color: #ff6b35;
}

.order-list {
  max-height: 500px;
  overflow-y: auto;
  padding-right: 4px;
}
</style>
