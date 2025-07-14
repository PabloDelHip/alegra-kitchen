<template>
  <div class="card shadow-sm">
    <div class="card-body">
      <!-- Encabezado -->
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
          <i class="bi bi-box"></i> Inventario de bodega
        </h5>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-danger-subtle text-danger fw-semibold">
            <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ lowCount }} bajos
          </span>
        </div>
      </div>

      <!-- Spinner de carga -->
      <div v-if="isLoading" class="text-center my-3">
        <div class="spinner-border text-primary" role="status"></div>
      </div>

      <!-- Lista de inventario -->
      <div v-else class="inventory-list vstack gap-3">
        <div
          v-for="item in inventory"
          :key="item.name"
          class="d-flex justify-content-between align-items-center border rounded p-3"
        >
          <div class="d-flex align-items-start gap-3 w-100">
            <div class="fs-4">{{ item.icon }}</div>
            <div class="flex-grow-1">
              <div class="fw-semibold d-flex align-items-center gap-2">
                {{ item.name }}
                <span class="badge bg-light text-dark small">{{ item.category }}</span>
              </div>
              <div class="text-muted small">Mínimo: {{ item.min }} {{ item.unit }}</div>

              <div class="progress mt-2" style="height: 6px;">
                <div class="progress-bar bg-dark" :style="{ width: item.percentage + '%' }"></div>
              </div>
              <div class="small text-muted mt-1">{{ item.percentage }}%</div>
            </div>
          </div>

          <div class="text-end ms-3">
            <div :class="['fw-bold', item.isLow ? 'text-danger' : 'text-dark']">
              {{ item.current }} {{ item.unit }}
            </div>
            <button
              class="btn btn-outline-primary btn-sm mt-1 d-flex align-items-center gap-1"
              :disabled="item.isLoading"
              @click="buyIngredient(item)"
            >
              <span v-if="item.isLoading" class="spinner-border spinner-border-sm"></span>
              <span v-else><i class="bi bi-cart"></i> Comprar</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../axios'
import { toast } from 'vue3-toastify'

const MAX_QUANTITY = 10
const LOW_STOCK_THRESHOLD = 5

const inventory = ref([])
const lowCount = ref(0)
const isLoading = ref(false)

const fetchInventory = async () => {
  isLoading.value = true
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/warehouse/ingredients`)
    const ingredients = response.data.data

    inventory.value = ingredients.map(item => ({
      name: getDisplayName(item.name),
      icon: getIcon(item.name),
      category: getCategory(item.name),
      current: item.quantity,
      min: LOW_STOCK_THRESHOLD,
      unit: 'pz',
      percentage: Math.min(Math.round((item.quantity / MAX_QUANTITY) * 100), 100),
      isLow: item.quantity <= LOW_STOCK_THRESHOLD,
      isLoading: false,
    }))

    lowCount.value = inventory.value.filter(item => item.isLow).length
  } catch (error) {
    console.error('❌ Error al obtener inventario:', error)
  } finally {
    isLoading.value = false
  }
}

const buyIngredient = async (item) => {
  try {
    item.isLoading = true
    const apiName = getApiName(item.name)
    const response = await axios.post(`${import.meta.env.VITE_API_BASE_URL}/warehouse/ingredients/buy`, { name: apiName })
    const quantityBought = response.data.quantityBought ?? 0

    item.current += quantityBought
    item.percentage = Math.min(Math.round((item.current / MAX_QUANTITY) * 100), 100)
    item.isLow = item.current <= LOW_STOCK_THRESHOLD
    lowCount.value = inventory.value.filter(i => i.isLow).length

    toast.success(`✅ Compraste ${quantityBought} ${item.unit} de ${item.name}`, { position: 'bottom-right' })
  } catch (error) {
    console.error(`❌ Error al comprar ${item.name}:`, error)
    toast.error(`❌ Error al comprar ${item.name}`, { position: 'bottom-right' })
  } finally {
    item.isLoading = false
  }
}

onMounted(fetchInventory)

// Helpers
function getDisplayName(name) {
  const names = { tomato: 'Tomate', lemon: 'Limón', potato: 'Papa', rice: 'Arroz', ketchup: 'Catsup', lettuce: 'Lechuga', onion: 'Cebolla', cheese: 'Queso', meat: 'Carne', chicken: 'Pollo' }
  return names[name] || capitalize(name)
}

function getApiName(displayName) {
  const names = { Tomate: 'tomato', Limón: 'lemon', Papa: 'potato', Arroz: 'rice', Catsup: 'ketchup', Lechuga: 'lettuce', Cebolla: 'onion', Queso: 'cheese', Carne: 'meat', Pollo: 'chicken' }
  return names[displayName] || displayName.toLowerCase()
}

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

function getIcon(name) {
  const icons = { tomato: '🍅', lemon: '🍋', potato: '🥔', rice: '🌾', ketchup: '🍶', lettuce: '🥬', onion: '🧅', cheese: '🧀', meat: '🥩', chicken: '🐔' }
  return icons[name] || '📦'
}

function getCategory(name) {
  const categories = { tomato: 'Verduras', lemon: 'Frutas', potato: 'Verduras', rice: 'Granos', ketchup: 'Condimentos', lettuce: 'Verduras', onion: 'Verduras', cheese: 'Lácteos', meat: 'Proteínas', chicken: 'Proteínas' }
  return categories[name] || 'Otros'
}
</script>

<style scoped>
.progress {
  background-color: #f1f1f1;
}
.progress-bar {
  transition: width 0.3s ease;
}
.inventory-list {
  max-height: 500px;
  overflow-y: auto;
  padding-right: 4px;
}
</style>
