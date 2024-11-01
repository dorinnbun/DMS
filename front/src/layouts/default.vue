<template>

  <v-app>
    <v-card>
      <v-layout v-if="displayAsideMenu" style="min-height: 100vh">
        
        <AsideMenu />

        <v-main style="min-height: 90vh; margin-top: 75px;">
            <router-view />
        </v-main>
        
      </v-layout>

      <v-layout v-else>

        <v-main style="min-height: 100vh">
            <router-view />
        </v-main>
        
      </v-layout>
      
    </v-card>
    
  </v-app>

</template>

<script setup>
  
  import { useAuthStore } from '../stores/auth.js'
  import { storeToRefs } from 'pinia'
  import { useRouter, useRoute } from 'vue-router'

  const router = useRouter()
  const route = useRoute()
  
  const authStore = useAuthStore()
  const { isLoggedIn } = storeToRefs(authStore)

  const token = localStorage.getItem('dms-token')

  if (token) {
    authStore.$patch({ isLoggedIn: true })
  }

  const displayAsideMenu = computed(() => {
    return route.fullPath !== '/login' 
      && route.name !== '/Records/[id]' 
      && route.name !== '/Verify-OTP/[uuid]' 
      && route.name !== 'NotFound'
      && route.fullPath !== '/forgot-password'
      && route.fullPath !== '/reset-password'
      && route.fullPath !== '/verify-otp'
      && route.fullPath !== '/verify-login-otp'
  })


  if (displayAsideMenu.value) {
    if (!isLoggedIn.value) {
      router.push({ path: '/login' })
    }
  }


</script>
