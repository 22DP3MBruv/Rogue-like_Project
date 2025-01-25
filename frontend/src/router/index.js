import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../App.vue'


const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
  {
    path: '/game',
    name: 'Game',
    // component: () => import('../components/GameComponent.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
