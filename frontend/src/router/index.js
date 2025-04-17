import { createRouter, createWebHistory } from 'vue-router'
import HomePage from '../App.vue'

// Define routes for the application
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
    // Moderator dashboard that is accessible only to users with the "Moderator" role
    component: () => import('../views/Moderator.vue')
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router
