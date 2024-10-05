<template>
  <v-card class="px-6 py-8 form-container" max-width="344">

    <h2>ចូលគណនីរបស់អ្នក</h2>

    <v-form
      v-model="form"
      @submit.prevent="login"
    >
      <v-text-field
        v-model="email"
        :readonly="loading"
        :rules="[required]"
        class="mb-4"
        label="អ៊ីមែល ឬ ឈ្មោះអ្នកប្រើប្រាស់"
      ></v-text-field>

      <v-text-field
        v-model="password"
        :readonly="loading"
        :rules="[required]"
        type="password"
        label="ពាក្យសម្ងាត់"
        placeholder="បញ្ចូលពាក្យសម្ងាត់របស់អ្នក"
      ></v-text-field>

      <v-alert
        v-if="isLoginError"
        type="error"
        dismissible
        class="mt-3"
      >
        {{ errorMessage }}
      </v-alert>

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
  import { useUserStore } from '@/stores/user'
  import { useTmpStore } from '@/stores/tmp'

  import { login as loginAPI } from '@/_api/auth'

  const form = ref(false)
  const email = ref(null)
  const password = ref(null)
  const loading = ref(false)
  const isLoginError = ref(false)
  const errorMessage = ref('អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ')

  const router = useRouter()
  const authStore = useAuthStore()
  const userStore = useUserStore()
  const tmpStore = useTmpStore()


  const emailRule = (v) => {
    const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
    return pattern.test(v) || 'អ៊ីមែលមិនត្រឹមត្រូវ'
  }

  // login
  const login = async () => {
      if (!form.value) return
      loading.value = true

      try {
        const result = await loginAPI({
          email: email.value,
          password: password.value
        })

        const { status, code } = result?.data
        const data = result?.data
        console.log("status", status);
        console.log("code", code);

        if (status == 201 && code == 201) {

          const user = data?.data?.item
          const token = user?.authorisation?.token
          localStorage.setItem('dms-token', token)
          delete user?.authorisation

          authStore.$patch({ isLoggedIn: true })
          router.push({ path: '/' })

          userStore.$patch({ user: user })

        } else if (code == 401) {
          isLoginError.value = true

        } else if (code == 404) {
          isLoginError.value = true
          errorMessage.value = 'User មិនមាននៅក្នុងប្រព័ន្ធទេ'

        } else {
          console.log("error", result);
          
        }

      } catch (error) {
        console.log(error)
        const errorMsg = error?.response?.data?.message
        isLoginError.value = true
        if (error?.response?.data?.code == 401) {
          errorMessage.value = 'អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ'
        } else {
          errorMessage.value = errorMsg ?? 'សូមព្យាយាមម្តងទៀត។'
        }
      }

      loading.value = false

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
