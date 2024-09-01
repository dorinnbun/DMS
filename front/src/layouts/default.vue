<template>

  <v-app>
    <v-card>
      <v-layout v-if="displayAsideMenu" style="min-height: 100vh">
        
        <AsideMenu />

        <v-main style="min-height: 100vh; margin-top: 75px;">
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

  if (!isLoggedIn.value) {
    router.push({ path: '/login' })
  }

  const displayAsideMenu = computed(() => {
    return route.fullPath !== '/login' && route.fullPath !== '/records/detail' // to be updated to detail{id}
  })
</script>
