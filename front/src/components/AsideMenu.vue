<template>
  
  <v-navigation-drawer
    theme="dark"
    permanent
    width="216"
  >

    <template v-slot:prepend>
      <v-list-item
        lines="two"
        prepend-icon="mdi-account-circle"
        :title="user.name"
      ></v-list-item>
    </template>

    <v-divider></v-divider>
    

    <v-list color="transparent">
      <v-list-item
        prepend-icon="mdi-account-multiple"
        v-if="user.role !== 'user'"
        :class="{ 'active-menu-item': $route.path === '/' }"
      >
        <RouterLink to="/">អ្នកប្រេីប្រាស់</RouterLink>
      </v-list-item>

      <v-list-item
        prepend-icon="mdi-text-box-multiple"
        :class="{ 'active-menu-item': $route.path === '/records' }"
      >
        <RouterLink to="/records">ឯកសារ</RouterLink>
      </v-list-item>

      <v-list-item
        prepend-icon="mdi-delete-outline"
        v-if="user.role !== 'user'"
        :class="{ 'active-menu-item': $route.path === '/trashbin' }"
      >
        <RouterLink to="/trashbin">ធុងសម្រាម</RouterLink>
      </v-list-item>
    </v-list>

    <template v-slot:append>
      <div class="pa-2">
        <v-btn block style="background-color: #1867C0; font-weight: bold;" @click="logout">
          ចេញពីគណនី
        </v-btn>
      </div>
    </template>
  </v-navigation-drawer>
</template>


<script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuthStore } from '@/stores/auth'
  import { useUserStore } from '@/stores/user'
  import { storeToRefs } from 'pinia'

  const router = useRouter()
  const authStore = useAuthStore()
  const userStore = useUserStore()
  const { user } = storeToRefs(userStore)

  const logout = () => {
    authStore.$patch({
      isLoggedIn: false
    })
    router.push({ path: '/login' })
    localStorage.removeItem('dms-token')
  }

</script>