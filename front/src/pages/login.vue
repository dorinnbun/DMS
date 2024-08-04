<template>
  <v-card class="px-6 py-8 form-container" max-width="344">

    <h2>ចូលគណនីរបស់អ្នក</h2>

    <v-form
      v-model="form"
      @submit.prevent="onSubmit"
    >
      <v-text-field
        v-model="email"
        :readonly="loading"
        :rules="[required]"
        class="mb-4"
        label="អ៊ីមែល"
        clearable
      ></v-text-field>

      <v-text-field
        v-model="password"
        :readonly="loading"
        :rules="[required]"
        label="ពាក្យសម្ងាត់"
        placeholder="បញ្ចូលពាក្យសម្ងាត់របស់អ្នក"
        clearable
      ></v-text-field>

      <br>

      <v-btn
        :disabled="!form"
        :loading="loading"
        color="#1867C0"
        size="large"
        type="submit"
        variant="elevated"
        block
      >
        ចូល
      </v-btn>
    </v-form>
  </v-card>
</template>

<script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAuthStore } from '@/stores/auth'

  const form = ref(false)
  const email = ref(null)
  const password = ref(null)
  const loading = ref(false)
  const router = useRouter()
  const authStore = useAuthStore()

  const onSubmit = () => {
    if (!form.value) return
    loading.value = true

    // login API

    authStore.$patch({
      isLoggedIn: true
    })
    router.push({ path: '/' })
  }

  const required = (v) => {
    return !!v || 'សូមបំពេញទិន្នន័យខាងលេី'
  }
</script>

<style>
  .form-container {
    position: absolute; 
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%); 
    width: 100%;
  }

  h2 {
    text-align: center; 
    margin-bottom: 14px; 
    color: #1867C0;
  }
</style>
