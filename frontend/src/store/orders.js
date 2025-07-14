import { defineStore } from 'pinia'
import { ref } from 'vue'

// Dentro de tu defineStore

import axios from '../axios'

export const useOrdersStore = defineStore('orders', () => {
  const orders = ref([])

  const INGREDIENT_TRANSLATIONS = {
    tomato: 'jitomate',
    lemon: 'limón',
    potato: 'papa',
    rice: 'arroz',
    ketchup: 'catsup',
    lettuce: 'lechuga',
    onion: 'cebolla',
    cheese: 'queso',
    meat: 'carne',
    chicken: 'pollo'
  }

  function translateIngredients(list) {
    console.log("llego el list", list)
    return list.map(item => ({
      ...item,
      name: INGREDIENT_TRANSLATIONS[item.name] || item.name
    }))
  }

  function addOrder(data) {
    const badgeConfig = {
      ready: {
        badgeClass: 'text-success bg-success-subtle',
        badgeIcon: 'bi bi-check-circle',
        borderClass: 'border-3 border-success'
      },
      preparando: {
        badgeClass: 'text-primary bg-primary-subtle',
        badgeIcon: 'bi bi-hourglass-split',
        borderClass: 'border-3 border-primary'
      },
      esperando: {
        badgeClass: 'text-dark bg-light',
        badgeIcon: 'bi bi-clock',
        borderClass: 'border-3 border-secondary'
      },
      failed: {
        badgeClass: 'text-danger bg-danger-subtle',
        badgeIcon: 'bi bi-x-circle',
        borderClass: 'border-3 border-danger'
      }
    }

    const status = data.dish_status || 'preparando'
    const config = badgeConfig[status] || badgeConfig['preparando']

    const newOrder = {
      id: data.dish_id || 'ORD',
      orderId: data.order_id,
      recipe: data.recipe_name || 'Desconocido',
      quantity: data.quantity || 1,
      assignedTo: data.assignedTo || '',
      time: data.time || 'recién',
      status: status,
      missingIngredients: translateIngredients(data.missing_ingredients || []),
      ...config
    }

    const existingIndex = orders.value.findIndex(o => o.id === newOrder.id)

    if (existingIndex !== -1) {
      orders.value[existingIndex] = newOrder
    } else {
      orders.value = [newOrder, ...orders.value]
    }
  }

  async function fetchOrders() {
    try {
      const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/orders/list/all`, {
        params: { page: 1, per_page: 400 }
      })
      console.log(response.data)
      orders.value = response.data.data.map(o => ({
        id: o.id,
        orderId: o.order?.order_code || '',
        recipe: o.recipe?.name || '',
        quantity: 1, // O ajusta si tienes la cantidad real
        assignedTo: '',
        time: o.updated_at || '',
        status: o.status,
        missingIngredients: translateIngredients(JSON.parse(o?.missing_ingredients) || []),
        ...getBadgeConfig(o.status)
      }))
    } catch (e) {
      console.error('❌ Error al cargar órdenes:', e)
    }
  }

  function getBadgeConfig(status) {
    const badgeConfig = {
      ready: {
        badgeClass: 'text-success bg-success-subtle',
        badgeIcon: 'bi bi-check-circle',
        borderClass: 'border-3 border-success'
      },
      preparando: {
        badgeClass: 'text-primary bg-primary-subtle',
        badgeIcon: 'bi bi-hourglass-split',
        borderClass: 'border-3 border-primary'
      },
      esperando: {
        badgeClass: 'text-dark bg-light',
        badgeIcon: 'bi bi-clock',
        borderClass: 'border-3 border-secondary'
      },
      failed: {
        badgeClass: 'text-danger bg-danger-subtle',
        badgeIcon: 'bi bi-x-circle',
        borderClass: 'border-3 border-danger'
      }
    }
    return badgeConfig[status] || badgeConfig['preparando']
  }

  function connectWebSocket() {
    const socket = new WebSocket('ws://127.0.0.1:8080/app/local?protocol=7&client=js&version=8.0.0&flash=false')

    socket.onopen = () => {
      console.log('🔌 Conectado a WebSocket')
      socket.send(JSON.stringify({
        event: 'pusher:subscribe',
        data: {
          auth: '',
          channel: 'orders'
        }
      }))
    }

    socket.onmessage = (event) => {
      try {
        const message = JSON.parse(event.data)

        if (message.event === 'dish.prepared') {
          const data = JSON.parse(message.data)
          console.log('🆕 Orden recibida:', data)
          addOrder(data)
        } else {
          console.log('📩 Mensaje recibido:', message)
        }
      } catch (e) {
        console.error('❌ Error al procesar mensaje:', e)
      }
    }
  }

  return {
    orders,
    addOrder,
    connectWebSocket,
    fetchOrders
  }
})

