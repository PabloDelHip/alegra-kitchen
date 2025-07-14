<template>
  <header class="py-3 border-bottom bg-white shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
      <!-- Logo y título -->
      <div class="d-flex align-items-center gap-3">
        <div class="bg-orange text-white fw-bold rounded px-3 py-2 text-center logo-font">
          ψq
        </div>
        <div>
          <h1 class="h5 fw-bold mb-0">FoodShare Manager</h1>
          <p class="text-muted small mb-0">Sistema de gestión de donaciones</p>
        </div>
      </div>

      <!-- Estado del sistema -->
      <div class="border border-success text-success rounded-pill px-3 py-1 d-flex align-items-center gap-2 small fw-medium">
        <span class="bg-success rounded-circle" style="width: 8px; height: 8px;"></span>
        Sistema activo
      </div>
    </div>
  </header>
</template>

<script setup>
// No se necesita lógica JS aquí
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
