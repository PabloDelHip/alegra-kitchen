<template>
  <div class="chart-container">
    <Bar v-if="chartData.labels.length" :data="chartData" :options="chartOptions" />
    <div v-else class="text-center text-muted">Cargando datos...</div>
  </div>
</template>

<script>
import axios from '../axios'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
  name: 'BarChart',
  components: { Bar },
  data() {
    return {
      chartData: {
        labels: [],
        datasets: [
          {
            label: 'Cantidad usada',
            data: [],
            backgroundColor: '#fd7e14'
          }
        ]
      },
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' },
          title: {
            display: true,
            text: 'Ingredientes más usados'
          }
        }
      }
    }
  },
  methods: {
    getDisplayName(name) {
      const names = {
        tomato: 'Tomate',
        lemon: 'Limón',
        potato: 'Papa',
        rice: 'Arroz',
        ketchup: 'Catsup',
        lettuce: 'Lechuga',
        onion: 'Cebolla',
        cheese: 'Queso',
        meat: 'Carne',
        chicken: 'Pollo'
      }
      return names[name] || name.charAt(0).toUpperCase() + name.slice(1)
    }
  },
  async mounted() {
    try {
      const response = await axios.get(`${import.meta.env.VITE_API_BASE_URL}/metrics/ingredients-used`)
      const data = response.data

      this.chartData.labels = data.map(item => this.getDisplayName(item.name))
      this.chartData.datasets[0].data = data.map(item => item.total_used)
    } catch (error) {
      console.error('❌ Error al cargar ingredientes:', error)
    }
  }
}
</script>

<style scoped>
.chart-container {
  position: relative;
  height: 400px;
  width: 100%;
}
</style>
