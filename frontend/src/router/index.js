import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../App.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: HomePage
  },
// {
  //   path: '/game',
  //   name: 'Game',
  //   // Route reserved for the Godot web game component (implement as needed)
  //   component: () => import('../views/GodotGame.vue')
  // },
  {
    path: '/moderator',
    name: 'Moderator',
    component: () => import('../views/Moderator.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    } else if (savedPosition) {
      return savedPosition
    } else {
      return { top: 0 }
    }
  }
})

export default router
