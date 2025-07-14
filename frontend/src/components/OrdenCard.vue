<template>
  <div
    class="p-3 bg-white rounded shadow-sm border-start position-relative mb-3"
    :class="order.borderClass"
  >
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h6 class="fw-semibold mb-1 d-flex align-items-center gap-2">
          {{ order.recipe }}
          <span
            v-if="order.status !== 'listo'"
            class="badge rounded-pill bg-light text-dark d-flex align-items-center gap-1"
            :class="order.badgeClass"
          >
            <i :class="order.badgeIcon"></i> {{ capitalize(order.status) }}
          </span>
          <span
            v-else
            class="badge rounded-pill bg-success-subtle text-success d-flex align-items-center gap-1"
          >
            <i class="bi bi-check-circle"></i> Listo
          </span>
        </h6>
        <p class="mb-1 text-muted small">
          Cantidad: {{ order.quantity }} porciones
          <i class="bi bi-person ms-2"></i> {{ order.assignedTo || 'No asignado' }}
        </p>
      </div>

      <div class="text-end">
        <small class="text-muted">{{ order.time }}</small>
        <div class="text-muted small">
          Orden: {{ order.orderId }}<br />
          Platillo ID: #{{ order.id }}
        </div>
      </div>
    </div>

    <!-- Ingredientes faltantes si falló -->
    <div v-if="order.status === 'failed'" class="mt-2 alert alert-danger p-2 small">
      <strong>No se pudo prepararss:</strong>
      <ul class="mb-0 ps-3">
        <li v-for="(item, idx) in order.missingIngredients" :key="idx">
          {{ item.name }} (faltan {{ item.needed }})
        </li>
      </ul>
    </div>

    <!-- Botones si está listo -->
    <div v-if="order.status === 'listo'" class="mt-2 d-flex gap-2">
      <button class="btn btn-success btn-sm">Servir</button>
      <button class="btn btn-outline-secondary btn-sm">Detalles</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  order: Object
})

function capitalize(str) {
  return str.charAt(0).toUpperCase() + str.slice(1)
}
</script>
