<template>
  <div class="container d-flex align-items-center justify-content-center min-vh-100 bg-light">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
      <div class="text-center mb-2">
        <!-- <img src="/logo.png" alt="Logo" class="mb-2" width="60" /> -->
        <div class="bg-orange text-white fw-bold rounded px-3 py-2 text-center logo-font">
          ψ FoodShare Manager
        </div>
        <p class="text-muted small mt-2 mb-0">Bienvenido al sistema de gestión de donaciones</p>
      </div>

      <form @submit.prevent="handleLogin">
        <div class="mb-3">
          <label for="email" class="form-label">Correo electrónico</label>
          <input type="email" id="email" v-model="email" class="form-control" required />
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" id="password" v-model="password" class="form-control" required />
        </div>

        <button type="submit" class="btn btn-primary w-100" :disabled="loading">
          <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          Ingresar
        </button>

        <div v-if="error" class="alert alert-danger mt-3 p-2 py-1 text-center">
          {{ error }}
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from '../axios'

const email = ref('pablo@example.com')
const password = ref('12345678')
const error = ref('')
const loading = ref(false)

const handleLogin = async () => {
  error.value = ''
  loading.value = true

  try {
    const response = await axios.post(`${import.meta.env.VITE_API_BASE_URL}/login`, {
      email: email.value,
      password: password.value
    })

    localStorage.setItem('token', response.data.access_token)
    window.location.href = '/'
  } catch (err) {
    if (err.response && err.response.status === 401) {
      error.value = 'Credenciales incorrectas. Inténtalo nuevamente.'
    } else {
      error.value = 'Ocurrió un error. Por favor intenta más tarde.'
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.bg-orange {
  background-color: #ff6b35;
}

.logo-font {
  font-size: 1.2rem;
  font-family: sans-serif;
}
</style>
