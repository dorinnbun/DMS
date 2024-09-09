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
        label="អ៊ីមែល"
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
        v-if="wrongCredential"
        type="error"
        dismissible
        class="mt-3"
      >
        {{ loginError }}
      </v-alert>

      <br>

      <v-row class="mb-4">
        <v-col cols="12" class="text-right">
          <router-link to="/forgot-password">ភ្លេចពាក្យសម្ងាត់?</router-link>
        </v-col>
      </v-row>

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

  import { login as loginAPI } from '@/_api/auth'

  const form = ref(false)
  const email = ref(null)
  const password = ref(null)
  const loading = ref(false)
  const wrongCredential = ref(false)
  const loginError = ref('អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ')

  const router = useRouter()
  const authStore = useAuthStore()


  // login
  const login = async () => {
      if (!form.value) return
      loading.value = true

      try {
        const { data } = await loginAPI({
          email: email.value,
          password: password.value
        })
        const { status, code } = data

        if (status == 200 && code == 200) {
          
          const token = data.data.item.authorisation.token
          localStorage.setItem('dms-token', token)
          authStore.$patch({ isLoggedIn: true })
          router.push({ path: '/' })

        } else if (code == 401) {
          wrongCredential.value = true
        }

      } catch (error) {
        console.log(error)
        wrongCredential.value = true
        loginError.value = 'សូមព្យាយាមម្តងទៀត។'
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
