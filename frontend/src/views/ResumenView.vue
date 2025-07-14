<template>
  <div>
    <!-- KPIs -->
    <!-- <div class="row g-4 mb-4">
      <div class="col-md-3" v-for="card in kpis" :key="card.title">
        <div class="kpi-card p-3 rounded shadow-sm h-100 d-flex justify-content-between">
          <div>
            <p class="fw-semibold text-muted mb-1 small">{{ card.title }}</p>
            <h3 class="fw-bold mb-1">{{ card.value }}</h3>
            <p class="text-muted small mb-0">{{ card.subtitle }}</p>
          </div>
          <i :class="card.icon" :style="{ color: card.iconColor || '#6b7280', fontSize: '1.4rem' }"></i>
        </div>
      </div>
    </div> -->

    <!-- Órdenes e Inventario -->
    <div class="row g-4">
      <!-- Órdenes en preparación (izquierda) -->
      <div class="col-lg-6">
        <OrdenesPanel />
      </div>

      <!-- Inventario (derecha) -->
      <div class="col-lg-6">
        <InventarioView />
      </div>
    </div>
  </div>
</template>

<script setup>
import OrdenesPanel from './OrdenesView.vue'
import InventarioView from './InventarioView.vue'

const kpis = [
  {
    title: 'Platos entregados',
    value: 245,
    subtitle: '',
    icon: 'bi bi-egg-fried',
    iconColor: '#6b7280'
  },
  {
    title: 'Órdenes en preparación',
    value: 12,
    subtitle: '3 próximas a completar',
    icon: 'bi bi-clock',
    iconColor: '#6b7280'
  },
  {
    title: 'Ingredientes bajos',
    value: 8,
    subtitle: 'Requieren reposición',
    icon: 'bi bi-exclamation-triangle',
    iconColor: '#fbbf24'
  },
  {
    title: 'Beneficiarios servidos',
    value: 189,
    subtitle: 'Meta: 200 personas',
    icon: 'bi bi-people',
    iconColor: '#6b7280'
  }
]
</script>

<style scoped>
.kpi-card {
  background-color: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  transition: box-shadow 0.2s ease-in-out;
}

.kpi-card:hover {
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}
</style>
