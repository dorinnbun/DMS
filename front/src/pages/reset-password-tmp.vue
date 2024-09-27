<template>
  <v-card class="px-6 py-8 form-container" max-width="344">

    <h2>Reset your password</h2>

    <v-form
      v-model="form"
      @submit.prevent="resetPassword"
    >
      <v-text-field
        v-model="OTP"
        :readonly="loading"
        :rules="[required]"
        class="mb-4"
        label="OTP"
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
  import { resetPassword as resetPasswordAPI } from '@/_api/user'

  const form = ref(false)
  const OTP = ref(null)
  const password = ref(null)
  const confirmPassword = ref(null)
  const loading = ref(false)
  const passwordNotMatched = ref(true)
  const errorMessage = ref('អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ')

  const router = useRouter()
  const authStore = useAuthStore()
  

  const resetPassword = async () => {
    try {
      const { data } = await resetPasswordAPI({
        otp: OTP.value,
        password: password.value,
        confirm_password: confirmPassword.value
      })
      const { status, code } = data

      if (status == 200 && code == 200) {
        router.push({ path: '/login' })
      } else {
        errorMessage.value = 'សូមព្យាយាមម្តងទៀត។'
      }

    } catch (error) {
      console.log(error)
      errorMessage.value = 'សូមព្យាយាមម្តងទៀត។'
    }
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
