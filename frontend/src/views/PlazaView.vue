<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import axios from '../axios'

const selectedDate = ref(new Date().toISOString().slice(0, 10))
const ingredients = ref([])
const loading = ref(false)

const loadPurchases = async () => {
  loading.value = true
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/warehouse/purchases`, {
      params: { date: selectedDate.value }
    })
    ingredients.value = response.data.data || []
  } catch (error) {
    console.error('Error cargando compras:', error)
  } finally {
    loading.value = false
  }
}

const totalUnits = computed(() =>
  ingredients.value.reduce((sum, item) => sum + Number(item.total_quantity), 0)
)

watch(selectedDate, loadPurchases)
onMounted(loadPurchases)

function capitalize(str) {
  if (!str) return ''
  return str.charAt(0).toUpperCase() + str.slice(1)
}
</script>

<template>
  <div class="card mt-4">
    <div class="card-body">
      <!-- Header -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0">
          🛒 Compras en plaza <span class="text-muted">({{ ingredients.length }})</span>
        </h5>
      </div>

      <!-- Filtro y botón -->
      <div class="d-flex align-items-center mb-3 gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
          <label class="fw-semibold mb-0">Filtrar por fecha:</label>
          <input type="date" v-model="selectedDate" class="form-control form-control-sm w-auto">
        </div>

        <button class="btn btn-sm btn-primary" @click="loadPurchases" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-1" role="status"></span>
          Actualizar
        </button>

        <span class="badge bg-light text-dark border fw-semibold">
          {{ ingredients.length }} ingredientes
        </span>
      </div>

      <!-- Cargando -->
      <div v-if="loading" class="text-center text-muted my-3">
        <div class="spinner-border spinner-border-sm text-primary" role="status"></div> Cargando...
      </div>

      <!-- Tabla -->
      <div v-else>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th>Ingrediente</th>
                <th>Cantidad comprada</th>
                <th>Última actualización</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in ingredients" :key="index">
                <td>{{ capitalize(item.name) }}</td>
                <td>
                  <span :class="['badge border fw-semibold', item.total_quantity > 0 ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-dark']">
                    {{ item.total_quantity }}
                  </span>
                </td>
                <td>{{ item.last_update ?? '—' }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Resumen -->
        <div class="d-flex justify-content-around pt-3 mt-3 border-top">
          <div class="text-center">
            <h5 class="fw-bold mb-0">{{ ingredients.length }}</h5>
            <small class="text-muted">Ingredientes diferentes</small>
          </div>
          <div class="text-center">
            <h5 class="fw-bold mb-0">{{ totalUnits }}</h5>
            <small class="text-muted">Total unidades</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.table > tbody > tr > td {
  vertical-align: middle;
}
</style>
