// src/axios.js
import axios from 'axios'

const instance = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL
})

// Interceptor para agregar token automáticamente
instance.interceptors.request.use(config => {
  const token = localStorage.getItem('token')
  if (token && !config.url.includes('/login')) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
}, error => {
  return Promise.reject(error)
})

// Interceptor para manejar respuestas 401
instance.interceptors.response.use(response => {
  return response
}, error => {
  if (error.response?.status === 401) {
    localStorage.clear()
    window.location.href = '/login'
  }
  return Promise.reject(error)
})

export default instance
