<template>
  <div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4 class="fw-bold mb-0">
        <i class="bi bi-list-task me-2 text-orange"></i> Órdenes actuales
      </h4>
      <button class="btn btn-primary btn-sm d-flex align-items-center gap-1" @click="store.fetchOrders" :disabled="isLoading">
        <span v-if="isLoading" class="spinner-border spinner-border-sm"></span>
        <span v-else><i class="bi bi-arrow-clockwise"></i> Actualizar</span>
      </button>
    </div>

    <div class="row g-4">
      <!-- En Preparación -->
      <div class="col-md-4">
        <div class="card h-100 shadow border-primary-subtle">
          <div class="card-header bg-primary-subtle fw-semibold text-primary border-0 d-flex align-items-center justify-content-between">
            <span><i class="bi bi-gear-fill me-1"></i> En Preparación</span>
            <span class="badge rounded-pill bg-primary text-white">{{ pendientes.length }}</span>
          </div>
          <div class="card-body p-2 overflow-y-auto" style="max-height: 70vh">
            <OrdenCard v-for="order in pendientes" :key="order.id" :order="order" />
          </div>
        </div>
      </div>

      <!-- Listos -->
      <div class="col-md-4">
        <div class="card h-100 shadow border-success-subtle">
          <div class="card-header bg-success-subtle fw-semibold text-success border-0 d-flex align-items-center justify-content-between">
            <span><i class="bi bi-check-circle me-1"></i> Listos</span>
            <span class="badge rounded-pill bg-success text-white">{{ listos.length }}</span>
          </div>
          <div class="card-body p-2 overflow-y-auto" style="max-height: 70vh">
            <OrdenCard v-for="order in listos" :key="order.id" :order="order" />
          </div>
        </div>
      </div>

      <!-- Fallidos -->
      <div class="col-md-4">
        <div class="card h-100 shadow border-danger-subtle">
          <div class="card-header bg-danger-subtle fw-semibold text-danger border-0 d-flex align-items-center justify-content-between">
            <span><i class="bi bi-x-circle me-1"></i> Fallidos</span>
            <span class="badge rounded-pill bg-danger text-white">{{ fallidos.length }}</span>
          </div>
          <div class="card-body p-2 overflow-y-auto" style="max-height: 70vh">
            <OrdenCard v-for="order in fallidos" :key="order.id" :order="order" />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useOrdersStore } from '../store/orders'
import { storeToRefs } from 'pinia'
import OrdenCard from './OrdenCard.vue'

const store = useOrdersStore()
const { orders } = storeToRefs(store)
const isLoading = ref(false)

const pendientes = computed(() => orders.value.filter(o => o.status !== 'ready' && o.status !== 'failed'))
const listos = computed(() => orders.value.filter(o => o.status === 'ready'))
const fallidos = computed(() => orders.value.filter(o => o.status === 'failed'))

async function loadInitial() {
  console.log("EL LOAD INICIALALALA")
  isLoading.value = true
  await store.fetchOrders()
  store.connectWebSocket()
  isLoading.value = false
}

onMounted(loadInitial)
</script>

<style scoped>
.text-orange {
  color: #ff6b35;
}
</style>
