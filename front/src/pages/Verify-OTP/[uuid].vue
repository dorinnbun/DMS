<template>
  <v-card class="px-6 py-8 form-container" max-width="30%">
    <h4>អ៊ីមែលត្រូវបានផ្ញើទៅអ៊ីមែលរបស់អ្នកហើយ! <br>សូមពិនិត្យមើលអ៊ីមែលរបស់អ្នកសម្រាប់លេខកូដយកមកកំណត់ពាក្យសម្ងាត់ឡើងវិញ</h4>
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
  import { useTmpStore } from '@/stores/tmp';
  import { verifyOTP as verifyOTPAPI } from '@/_api/user';
  import { useRouter, useRoute } from 'vue-router';
  import { storeToRefs } from 'pinia';
  const form = ref(false)
  const OTP = ref(null)
  const loading = ref(false)
  const errorMessage = ref('អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ')
  const displayConfirmationMessage = ref(false)
  const anyError = ref(false)
  const route = useRoute()
  const router = useRouter()
  console.log("route===", route);
  
  const tmpStore = useTmpStore()
  // get the params from the route 
  const uuid = route.params.uuid
  const verifyOTP = async () => {
      if (!form.value) return
      loading.value = true
      anyError.value = false
      try {
        const { data } = await verifyOTPAPI({
          otp: OTP.value
        }, uuid)
        console.log("data===", data);
        
        const { status, code } = data
        console.log("status===", status);
        console.log("code===", code);
        if (status == 201 && code == 201) {
          displayConfirmationMessage.value = true
          tmpStore.$patch({ userUUID: uuid })
          router.push({ path: '/reset-password' })
        } else if (code == 404) {
          anyError.value = true
          errorMessage.value = 'អ៊ីមែលរបស់អ្នកមិនមាននៅក្នុងប្រព័ន្ធទេ!'
        } else if (code == 401) {
          anyError.value = true
          errorMessage.value = data.message
        }
        // router.push({ path: '/reset-password' })
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