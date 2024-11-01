<template>
  <v-card class="px-6 py-8 form-container" max-width="30%">

    <h4>បំពេញអ៊ីមែលរបស់អ្នកដើម្បីទទួលបានតំណភ្ជាប់សម្រាប់កំណត់ពាក្យសម្ងាត់ឡើងវិញ</h4>

    <v-form
      v-model="form"
      @submit.prevent="submitResetPasswordEmail"
    >
      <v-text-field
        v-model="email"
        :readonly="loading"
        :rules="[required, emailRule]"
        class="mb-4"
        label="អ៊ីមែល"
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
        ផ្ញើអ៊ីមែល
      </v-btn>

      <p v-if="displayConfirmationMessage" class="mt-4" style="color: darkorange; font-size: 14px;">
        **{{ confirmationMessage }}
      </p>
      
    </v-form>
  </v-card>
</template>

<script setup>
  import { ref } from 'vue'
  import { useAuthStore } from '@/stores/auth'
  import { sendResetPasswordEmail } from '@/_api/user'
  import { useRouter } from 'vue-router'

  const form = ref(false)
  const email = ref(null)
  const loading = ref(false)
  const errorMessage = ref('អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ')
  const displayConfirmationMessage = ref(false)
  const confirmationMessage = ref ('អ៊ីមែលត្រូវបានផ្ញើទៅអ៊ីមែលរបស់អ្នកហើយ! សូមពិនិត្យមើលអ៊ីមែលរបស់អ្នកសម្រាប់តំណកំណត់ពាក្យសម្ងាត់ឡើងវិញ')
  const anyError = ref(false)

  const router = useRouter()

  const emailRule = (v) => {
    if (!v) return true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(v) || 'អ៊ីមែលមិនត្រឹមត្រូវ';
  };


  // submitResetPasswordEmail
  const submitResetPasswordEmail = async () => {
      if (!form.value) return
      loading.value = true
      anyError.value = false



      try {
        const { data } = await sendResetPasswordEmail(
          email.value
        )

        console.log("data===", data);
        
        const { status, code } = data

        console.log("status===", status);
        console.log("code===", code);
        

        if (status == 201 && code == 201) {
          displayConfirmationMessage.value = true          
          router.push({ path: `/verify-otp/${ data.data.item.uuid }` })

        } else if (code == 404) {
          anyError.value = true
          errorMessage.value = 'អ៊ីមែលរបស់អ្នកមិនមាននៅក្នុងប្រព័ន្ធទេ!'

        } else if (code == 401) {
          anyError.value = true
          errorMessage.value = data.message
        }

      } catch (error) {
        console.log(error)
        if (error.response.data.code == 404) {
          anyError.value = true
          errorMessage.value = 'អ៊ីមែលរបស់អ្នកមិនមាននៅក្នុងប្រព័ន្ធទេ!'
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

h4 {
  text-align: center; 
  margin-bottom: 14px; 
  color: #1867C0;
}
</style>