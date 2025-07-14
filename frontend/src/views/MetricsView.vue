<template>
  <div class="container py-4">
    <h4 class="fw-bold mb-4">
      <i class="bi bi-graph-up-arrow me-2 text-primary"></i> Métricas del sistema
    </h4>

    <div class="row g-4">
      <div class="col-md-6">
        <div class="card shadow-sm p-3">
          <BarChart />
        </div>
      </div>

      <div class="col-md-6">
        <div class="card shadow-sm p-3">
          <PieChart />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import BarChart from './BarChart.vue'
import PieChart from './PieChart.vue'
</script>

<style scoped>
.text-primary {
  color: #0d6efd;
}
</style>
