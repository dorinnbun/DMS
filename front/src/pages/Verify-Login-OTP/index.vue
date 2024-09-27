<template>
  <v-card class="px-6 py-8 form-container" max-width="30%">

    <h4>OTPត្រូវបានផ្ញើទៅអ៊ីមែលរបស់អ្នកហើយ! <br>សូមពិនិត្យមើលអ៊ីមែលរបស់អ្នកសម្រាប់លេខកូដ</h4>

    <v-form
      v-model="form"
      @submit.prevent="verifyOTP"
    >
      <v-text-field
        v-model="OTP"
        :readonly="loading"
        :rules="[required]"
        class="mb-4"
        label="លេខកូដ6ខ្ទង់ដែលបានផ្ញើទៅអ៊ីមែលរបស់អ្នក"
      ></v-text-field>

      <v-alert
        v-if="anyError"
        type="error"
        dismissible
        class="my-3"
      >
        {{ errorMessage }}
      </v-alert>

      <v-btn
        :disabled="!form"
        :loading="loading"
        color="#1867C0"
        size="large"
        type="submit"
        variant="elevated"
        block
      >
        បញ្ជាក់
      </v-btn>

    </v-form>
  </v-card>
</template>

<script setup>
  import { ref } from 'vue'
  import { useAuthStore } from '@/stores/auth'
  import { useUserStore } from '@/stores/user'
  import { useTmpStore } from '@/stores/tmp'
  import { verifyOtp as verifyOTPAPI } from '@/_api/auth';
  import { useRouter, useRoute } from 'vue-router';
  import { storeToRefs } from 'pinia';

  const form = ref(false)
  const OTP = ref(null)
  const loading = ref(false)
  const errorMessage = ref('អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ')

  const anyError = ref(false)
  const router = useRouter()

  const authStore = useAuthStore()
  const userStore = useUserStore()
  const tmpStore = useTmpStore()

  const { tmpObject } = storeToRefs(tmpStore)

  const verifyOTP = async () => {
      if (!form.value) return
      loading.value = true
      anyError.value = false

      try {
        const result = await verifyOTPAPI({
          otp: OTP.value,
          email: tmpObject.value.email,
          password: tmpObject.value.password
        })

        console.log("result===", result);
        

        const data = result?.data
        const code = data?.code
        const status = data?.status
        // const { status, code } = data
        console.log("data===", data);

        console.log("status===", status);
        console.log("code===", code);
        
        

        if (status == 201 && code == 201) {

          const user = data?.data?.item
          const token = user?.authorisation?.token
          localStorage.setItem('dms-token', token)
          delete user?.authorisation
          tmpStore.$reset()

          authStore.$patch({ isLoggedIn: true })
          router.push({ path: '/' })

          userStore.$patch({ user: user })

        } else if (code == 404) {
          anyError.value = true
          errorMessage.value = 'អ៊ីមែលរបស់អ្នកមិនមាននៅក្នុងប្រព័ន្ធទេ!'

        } else if (code == 401) {
          anyError.value = true
          errorMessage.value = data.message
        } else {
          anyError.value = true
          errorMessage.value = 'សូមព្យាយាមម្តងទៀត!'
        }

      } catch (error) {
        console.log(error)
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

h4 {
  text-align: center; 
  margin-bottom: 14px; 
  color: #1867C0;
}
</style>