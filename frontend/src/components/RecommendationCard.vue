<template>
  <div class="border border-dashed border-purple-400 rounded p-3 d-flex align-items-center justify-content-between">
    <div class="d-flex align-items-center">
      <div class="rounded bg-light p-2 me-3">
        <i class="bi bi-robot text-purple"></i>
      </div>
      <div>
        <h5 class="mb-0 fw-bold">Recomendaciones del Bot-Chef</h5>
        <small class="text-muted">¡Prueba nuestra IA!</small>
      </div>
    </div>
    <button class="btn btn-gradient-purple" @click="handleClick">
      <i class="bi bi-robot me-1"></i> Consultar IA
    </button>

    <div v-if="modalOpen" class="modal fade show d-block" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Análisis y recomendación del Chef</h5>
            <button type="button" class="btn-close" @click="modalOpen = false"></button>
          </div>
          <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
            <div v-if="isLoading" class="d-flex justify-content-center align-items-center" style="height: 150px;">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
              </div>
            </div>
            <div v-else style="white-space: pre-wrap;" class="text-muted">
              {{ recommendation }}
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="modalOpen = false">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <div v-if="modalOpen" class="modal-backdrop fade show"></div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '../axios'
import { toast } from 'vue3-toastify'

const modalOpen = ref(false)
const recommendation = ref('')
const isLoading = ref(false)

async function handleClick() {
  modalOpen.value = true
  isLoading.value = true
  recommendation.value = ''

  try {
    const res = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/agent/recommendations`)
    recommendation.value = res.data.recomendacion
  } catch (error) {
    console.error('❌ Error al obtener la recomendación:', error)
    recommendation.value = '❌ Error al obtener la recomendación.'
    toast.error('❌ Hubo un error al obtener la recomendación')
  } finally {
    isLoading.value = false
  }
}
</script>

<style scoped>
.btn-gradient-purple {
  background: linear-gradient(to right, #a855f7, #ec4899);
  color: white;
  border: none;
}
.btn-gradient-purple:hover {
  opacity: 0.9;
}
.text-purple {
  color: #a855f7;
}
.border-purple-400 {
  border-color: #d8b4fe !important;
}
</style>
