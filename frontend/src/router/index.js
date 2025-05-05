import { createRouter, createWebHistory } from 'vue-router'
import Layout from '../components/Layout.vue'
import HomePage from '../components/HomePage.vue'

const routes = [
  {
    path: '/',
    component: Layout,
    children: [
      {
        path: '',
        name: 'Home',
        component: HomePage
      },
      {
        path: 'moderator',
        name: 'Moderator',
        component: () => import('../views/Moderator.vue')
      }
    ]
  }
]

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
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

router.beforeEach((to) => {
  if (to.name === 'Moderator') {
    const role = localStorage.getItem('userRole')
    return role === 'Moderator' ? true : { path: '/' }
  }
})

export default router
