/**
 * router/index.ts
 *
 * Automatic routes for `./src/pages/*.vue`
 */

// Composables
import { createRouter, createWebHistory } from 'vue-router/auto'
import { setupLayouts } from 'virtual:generated-layouts'
import { routes } from 'vue-router/auto-routes'
import NotFound from '@/pages/NotFound.vue'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: setupLayouts([
    ...routes,
    {
      path: '/:pathMatch(.*)*',
      name: 'NotFound',
      component: NotFound
    }
  ]),
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition; // restores scroll position when navigating back
    } else {
      return { left: 0, top: 0 }; // scrolls to the top for new pages
    }
  }
});


// Add a global navigation guard to handle initial load
router.beforeResolve((to, from, next) => {
  const authStore = useAuthStore()
  const isAuthenticated = authStore.isLoggedIn
  const token = localStorage.getItem('dms-token')
  
  if (!isAuthenticated && !token && to.name !== '/login' 
    && to.name !== '/Verify-OTP/[uuid]' 
    && to.fullPath !== '/forgot-password'
    && to.fullPath !== '/reset-password'
    && to.fullPath !== '/verify-otp'
    && to.name !== '/forgot-password'
    && to.name !== '/reset-password'
    && to.name !== '/verify-otp'
  ) {
  next({ path: 'login' })
} else {
  next()
}
})

// Workaround for https://github.com/vitejs/vite/issues/11804
router.onError((err, to) => {
  if (err?.message?.includes?.('Failed to fetch dynamically imported module')) {
    if (!localStorage.getItem('vuetify:dynamic-reload')) {
      console.log('Reloading page to fix dynamic import error')
      localStorage.setItem('vuetify:dynamic-reload', 'true')
      location.assign(to.fullPath)
    } else {
      console.error('Dynamic import error, reloading page did not fix it', err)
    }
  } else {
    console.error(err)
  }
})

router.isReady().then(() => {
  localStorage.removeItem('vuetify:dynamic-reload')
})

export default router
