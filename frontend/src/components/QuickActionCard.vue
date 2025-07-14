<template>
  <div
    class="d-flex align-items-center justify-content-between rounded-3 p-3 border"
    :class="borderClass"
  >
    <div class="d-flex align-items-center gap-3">
      <div
        :class="['rounded-3 p-2 text-white d-flex align-items-center justify-content-center', iconBgClass]"
        style="width: 40px; height: 40px;"
      >
        <i :class="iconClass"></i>
      </div>
      <div>
        <h5 class="mb-1 fw-bold">{{ title }}</h5>
        <p class="mb-0 text-muted small">{{ subtitle }}</p>
      </div>
    </div>

    <div class="d-flex align-items-center gap-2">
      <input
        type="number"
        v-model.number="quantity"
        min="1"
        class="form-control form-control-sm"
        style="width: 80px;"
        placeholder="Cantidad"
        :disabled="loading"
      />
      <button
        class="btn d-flex align-items-center gap-1"
        :class="buttonClass"
        @click="emitAction"
        :disabled="loading || quantity < 1"
      >
        <span v-if="loading" class="spinner-border spinner-border-sm"></span>
        <span v-else>{{ buttonText }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, defineExpose } from 'vue'

const props = defineProps({
  iconClass: String,
  iconBgClass: String,
  title: String,
  subtitle: String,
  buttonText: String,
  buttonClass: String,
  borderClass: String
})

const emit = defineEmits(['action'])
const quantity = ref(1)
const loading = ref(false)

const emitAction = async () => {
  loading.value = true
  try {
    await emit('action', quantity.value, setLoadingOff)
  } catch (e) {
    loading.value = false
  }
}

const setLoadingOff = () => {
  loading.value = false
}

defineExpose({
  setLoadingOff
})
</script>
