<template>
  <v-card class="px-6 py-8 form-container" max-width="25%">
    <h4 class="mb-8">បំពេញអ៊ីមែលរបស់អ្នកដើម្បីទទួលបានតំណភ្ជាប់សម្រាប់កំណត់ពាក្យសម្ងាត់ឡើងវិញ</h4>
    <v-form
      v-model="form"
      @submit.prevent="resetPassword"
    >
      <template v-for="field in resetPasswordFields">
        <v-text-field
          v-model="field.value"
          :label="field.label"
          :type="showPassword ? 'text' : 'password'"
          persistent-hint="false"
          :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល', v => /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,}$/.test(v) || 'លេខសម្ងាត់ត្រូវតែមានយ៉ាងហោចណាស់ 8 តួអក្សរ, យ៉ាងហោចណាស់ អក្សរពិសេស1តួ និង លេខ1តួ']"
          @click:append-inner="togglePasswordVisibility"
          style="margin-bottom: 12px;"
        >
          <template v-slot:append-inner>
            <v-icon @click="togglePasswordVisibility">
              {{ showPassword ? 'mdi-eye-off' : 'mdi-eye' }}
            </v-icon>
          </template>
        </v-text-field>
      </template>
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
        កំណត់លេខសម្ងាត់
      </v-btn>
      <p v-if="displayConfirmationMessage" class="mt-4" style="color: green; font-size: 14px;">
        **{{ confirmationMessage }}
      </p>
    </v-form>
  </v-card>
</template>
<script setup>
  import { ref } from 'vue'
  import { useTmpStore } from '@/stores/tmp';
  import { storeToRefs } from 'pinia';
  import { resetPassword as resetPasswordAPI } from '@/_api/user';
  const form = ref(false)
  const loading = ref(false)
  const errorMessage = ref('អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ')
  const displayConfirmationMessage = ref(false)
  const confirmationMessage = ref('')
  const showPassword = ref(false)
  const anyError = ref(false)
  const tmpStore = useTmpStore()
  const { userUUID } = storeToRefs(tmpStore)
  const emailRule = (v) => {
    if (!v) return true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(v) || 'អ៊ីមែលមិនត្រឹមត្រូវ';
  };
  const resetPasswordFields = ref([
    {
      key: "password",
      label: "លេខសម្ងាត់",
      value: "",
      type: "password"
    }, {
      key: "confirm_password",
      label: "បញ្ជាក់លេខសម្ងាត់ម្តងទៀត",
      value: "",
      type: "password"
    }
  ])
  // resetPassword
  const resetPassword = async () => {
      if (!form.value) return
      loading.value = true
      anyError.value = false
      try {
        const { data } = await resetPasswordAPI({
          password: resetPasswordFields.value[0].value,
          confirm_password: resetPasswordFields.value[1].value
        }, userUUID.value)
        tmpStore.$reset()
        const { status, code } = data
        if (status == 201 && code == 201) {
          displayConfirmationMessage.value = true
          confirmationMessage.value = 'លេខសម្ងាត់របស់អ្នកត្រូវបានកំណត់ឡើងវិញរួចរាល់!'
          setTimeout(() => {
            router.push({ path: '/login' })
          }, 3000)
        } else if (code == 404) {
          anyError.value = true
          errorMessage.value = 'អ៊ីមែលរបស់អ្នកមិនមាននៅក្នុងប្រព័ន្ធទេ!'
        } else if (code == 401) {
          anyError.value = true
          errorMessage.value = data.message
        }
      } catch (error) {
        console.log(error)
      }
      loading.value = false
    }
  const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value
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