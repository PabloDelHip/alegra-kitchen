<template>
  <div class="chart-container">
    <Pie v-if="chartData" :data="chartData" :options="chartOptions" />
    <p v-else>Cargando...</p>
  </div>
</template>

<script>
import { Pie } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
} from 'chart.js'
import axios from '../axios'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

export default {
  name: 'PieChart',
  components: { Pie },
  data() {
    return {
      chartData: null,
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
          },
          title: {
            display: true,
            text: 'Platillos más vendidos',
          },
        },
      },
    }
  },
  mounted() {
    axios.get(`${import.meta.env.VITE_API_BASE_URL}/metrics/recipes`) // asegúrate que este endpoint está bien
      .then(response => {
        const labels = response.data.map(item => item.name)
        const data = response.data.map(item => item.total)
        const colors = ['#fd7e14', '#0d6efd', '#20c997', '#9b59b6', '#6c757d', '#f39c12', '#17a2b8']

        this.chartData = {
          labels,
          datasets: [
            {
              label: 'Platillos vendidos',
              data,
              backgroundColor: colors.slice(0, labels.length),
            },
          ],
        }
      })
      .catch(error => {
        console.error('Error al cargar datos para gráfica de pastel:', error)
      })
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
