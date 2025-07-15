<template>
  <div>
    <Header />

    <div class="container py-4">
      <!-- Acciones rápidas -->
      <h4 class="fw-bold mb-4">Acciones rápidas</h4>
      <div class="row g-4 mb-4">
        <div class="col-md-6">
          <RecommendationCard
          />
        </div>

        <div class="col-md-6">
          <QuickActionCard
            iconClass="bi bi-egg-fried"
            iconBgClass="bg-primary"
            title="Enviar a cocina"
            subtitle="Procesar pedidos pendientes"
            buttonText="Enviar"
            buttonClass="btn btn-outline-primary"
            borderClass="border-blue"
            @action="sendToKitchen"
          />
        </div>
      </div>

      <!-- Submenú -->
      <BottomNav @tab-selected="selectedTab = $event" />

      <!-- Vistas por tab -->
      <div class="pt-4" v-if="selectedTab === 'Resumen'">
        <ResumenView ref="resumenRef" />
      </div>

      <!-- Ejemplo para futuras tabs -->
      <div v-if="selectedTab === 'historial'">
        <OrderList />
        <!-- <OrderStatus /> -->
      </div>

      <div v-if="selectedTab === 'ordenes'">
        <OrderStatus />
      </div>

      <div v-if="selectedTab === 'Recetas'">
        <RecipesView />
      </div>

      <div v-if="selectedTab === 'metricas'">
        <MetricsView />
      </div>

      <div v-if="selectedTab === 'inventario'">
        <InventarioDosView class="mt-3" />
      </div>

      <div v-if="selectedTab === 'plaza'">
        <PlazaView class="mt-3" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { toast } from 'vue3-toastify'
import axios from '../axios'
import Header from '../components/Header.vue'
import BottomNav from '../components/BottomNav.vue'
import ResumenView from './ResumenView.vue'
import OrderList from './OrderList.vue'
import RecipesView from './RecipesView.vue'
import MetricsView from './MetricsView.vue'
import PlazaView from './PlazaView.vue'
import InventarioDosView from './InventarioDosView.vue'
import OrderStatus from '../components/OrderStatus.vue'
import QuickActionCard from '../components/QuickActionCard.vue'
import RecommendationCard from '../components/RecommendationCard.vue'
import { useOrdersStore } from '../store/orders'
const store = useOrdersStore()

const selectedTab = ref('Resumen') // Tab activo por defecto
const resumenRef = ref(null)

function startSession() {
  console.log('🚀 Iniciar jornada')
}

async function sendToKitchen(quantity, done) {
  try {
    const response = await axios.post(`${import.meta.env.VITE_API_BASE_URL}/orders`, {
      quantity
    })

    const dishes = response.data.dishes.map(dish => ({
      dish_id: dish.dish_id,
      order_id: response.data.order_id,
      recipe_name: dish.recipe_name,
      dish_status: 'esperando'
    }))

    dishes.forEach(store.addOrder)

    toast.success('✅ Platillos enviados a cocina. ¡Puedes verlos en Órdenes!')
  } catch (error) {
    console.error('❌ Error al enviar:', error)
    toast.error('❌ Hubo un error al enviar los platillos')
  } finally {
    done()
  }
}
</script>

<style scoped>
.bg-orange {
  background-color: #ff6b35;
}
.border-orange {
  border: 1px dashed #ff6b35 !important;
}
.border-blue {
  border: 1px dashed #0d6efd !important;
}
.btn-orange {
  background-color: #ff6b35;
  border-color: #ff6b35;
}
</style>
