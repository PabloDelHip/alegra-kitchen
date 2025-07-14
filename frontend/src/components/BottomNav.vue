<template>
  <div class="nav-tabs-custom container-fluid">
    <ul class="nav justify-content-center">
      <li class="nav-item" v-for="(tab, index) in tabs" :key="index">
        <a
          href="#"
          class="nav-link"
          :class="{ active: activeTab === tab.name }"
          @click.prevent="selectTab(tab.name)"
        >
          <i :class="tab.icon" class="me-1"></i>
          {{ tab.label }}
        </a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { ref } from 'vue'

// Emitir evento personalizado
const emit = defineEmits(['tab-selected'])

const activeTab = ref('Resumen')

const tabs = [
  { name: 'Resumen', icon: 'bi bi-bar-chart-line', label: 'Resumen' },
  { name: 'ordenes', icon: 'bi bi-clock', label: 'Órdenes' },
  { name: 'inventario', icon: 'bi bi-box', label: 'Inventario' },
  { name: 'plaza', icon: 'bi bi-cart', label: 'Plaza' },
  { name: 'Recetas', icon: 'bi bi-journal-bookmark', label: 'Recetas' },
  { name: 'historial', icon: 'bi bi-clock-history', label: 'Historial' },
  { name: 'metricas', icon: 'bi bi-graph-up', label: 'Métricas' }
]

function selectTab(name) {
  activeTab.value = name
  emit('tab-selected', name)
}
</script>

<style scoped>
.nav-tabs-custom {
  background-color: #f8f9fa;
  padding: 12px;
  border-radius: 10px;
}

.nav-link {
  color: #6b7280;
  font-weight: 500;
  padding: 10px 16px;
  border-radius: 6px;
  display: inline-flex;
  align-items: center;
  transition: background-color 0.2s;
}

.nav-link:hover {
  background-color: #e9ecef;
  color: #111827;
}

.nav-link.active {
  background-color: #e9f0f7;
  color: #111827;
  font-weight: 600;
}
</style>
