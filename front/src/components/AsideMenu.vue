<template>
  
  <v-navigation-drawer
    theme="dark"
    permanent
  >

    <template v-slot:prepend>
      <v-list-item
        lines="two"
        prepend-icon="mdi-account-circle"
        :title="'Admin'"
      ></v-list-item>
    </template>

    <v-divider></v-divider>
    
    <v-list color="transparent">
      <v-list-item prepend-icon="mdi-account-multiple">
        <RouterLink to="/">អ្នកប្រេីប្រាស់</RouterLink>
      </v-list-item>

      <v-list-item prepend-icon="mdi-text-box-multiple">
        <RouterLink to="/records">ឯកសារ</RouterLink>
      </v-list-item>

      <v-list-item prepend-icon="mdi-delete-outline">
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

  const router = useRouter()
  const authStore = useAuthStore()

  const logout = () => {
    authStore.$patch({
      isLoggedIn: false
    })
    router.push({ path: '/login' })
  }

</script>