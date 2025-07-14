import { createRouter, createWebHistory } from 'vue-router'
import KitchenView from '../views/KitchenView.vue'
import LoginView from '../views/LoginView.vue'

const routes = [
  { path: '/login', name: 'Login', component: LoginView },
  { path: '/', name: 'Kitchen', component: KitchenView, meta: { requiresAuth: true } },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token')

  // Si la ruta requiere autenticación y no hay token, redirige al login
  if (to.meta.requiresAuth && !token) {
    next({ name: 'Login' })

  // Si ya hay token y el usuario intenta ir a login, redirige a Kitchen
  } else if (token && to.name === 'Login') {
    next({ name: 'Kitchen' })

  } else {
    next()
  }
})

export default router
