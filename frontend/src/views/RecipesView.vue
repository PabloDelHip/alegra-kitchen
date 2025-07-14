<template>
  <div class="container py-4">
    <div class="card shadow-sm">
      <div class="card-body">
        <h4 class="fw-bold mb-4 d-flex align-items-center">
          <i class="bi bi-journal-bookmark-fill me-2 text-purple"></i>
          Recetas disponibles
          <span class="badge bg-secondary ms-2">{{ recipes.length }}</span>
        </h4>

        <!-- Spinner de carga -->
        <div v-if="isLoading" class="text-center my-4">
          <div class="spinner-border text-primary"></div>
        </div>

        <!-- Lista de recetas -->
        <div v-else class="row g-4">
          <div v-for="recipe in recipes" :key="recipe.id" class="col-md-6 col-xl-6">
            <div class="border rounded p-3 h-100">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <h5 class="card-title">{{ recipe.name }}</h5>
                <span
                  class="badge"
                  :class="{
                    'bg-success': recipe.status === 'Disponible',
                    'bg-warning text-dark': recipe.status === 'Limitada',
                    'bg-danger': recipe.status === 'Agotada'
                  }"
                >
                  {{ recipe.status }}
                </span>
              </div>

              <div class="mb-2 text-muted small">
                <i class="bi bi-clock me-1 ms-2"></i> {{ recipe.time }} ·
                <span class="ms-2">
                  <i class="bi bi-thermometer-half me-1"></i> {{ recipe.difficulty }}
                </span>
              </div>

              <div class="mb-2">
                <span class="badge bg-light text-dark">{{ recipe.type }}</span>
              </div>

              <p class="mb-1 fw-semibold">Ingredientes:</p>
              <ul class="list-unstyled small mb-3">
                <li v-for="(ingredient, idx) in recipe.ingredients" :key="idx">
                  <i :class="ingredient.available ? 'bi bi-check text-success' : 'bi bi-x text-danger'" class="me-1"></i>
                  <span :class="{ 'text-decoration-line-through text-muted': !ingredient.available }">
                    {{ ingredient.name }}
                  </span>
                  <span v-if="ingredient.amount" class="ms-1 text-muted">({{ ingredient.amount }})</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from '../axios'

const recipes = ref([])
const isLoading = ref(false)

onMounted(async () => {
  isLoading.value = true
  try {
    const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/kitchen/recipes`)
    recipes.value = response.data.map(recipe => ({
      ...recipe,
      status: 'Disponible',
      portions: 20,
      time: '30 min',
      difficulty: 'Fácil',
      type: 'Plato principal',
      ingredients: recipe.ingredients.map(i => ({
        name: translate(i.name),
        available: true,
        amount: i.quantity
      }))
    }))
  } catch (error) {
    console.error('Error cargando recetas:', error)
  } finally {
    isLoading.value = false
  }
})

function translate(english) {
  const dict = {
    chicken: 'Pollo',
    onion: 'Cebolla',
    rice: 'Arroz',
    lemon: 'Limón',
    lettuce: 'Lechuga',
    tomato: 'Tomate',
    cheese: 'Queso',
    ketchup: 'Catsup',
    potato: 'Papa',
    meat: 'Carne'
  }
  return dict[english] || english
}
</script>

<style scoped>
.text-purple {
  color: #6f42c1;
}
</style>
