<template>
  <v-card class="px-6 py-8 form-container" max-width="25%">

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

      <!-- <v-alert
        v-if="emailNotExist"
        type="error"
        dismissible
        class="mt-3"
      >
        {{ submitResetPasswordEmailError }}
      </v-alert> -->

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

  const form = ref(false)
  const email = ref(null)
  const loading = ref(false)
  const submitResetPasswordEmailError = ref('អ៊ីមែល ឬ ពាក្យសម្ងាត់ មិនត្រឹមត្រូវ')
  const displayConfirmationMessage = ref(false)
  const confirmationMessage = ref ('សូមពិនិត្យមើលអ៊ីមែលរបស់អ្នកសម្រាប់តំណកំណត់ពាក្យសម្ងាត់ឡើងវិញ')

  const emailRule = (v) => {
    if (!v) return true;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(v) || 'អ៊ីមែលមិនត្រឹមត្រូវ';
  };


  // submitResetPasswordEmail
  const submitResetPasswordEmail = async () => {
      if (!form.value) return
      loading.value = true

      try {
        // const { data } = await submitResetPasswordEmailAPI({
        //   email: email.value
        // })
        // const { status, code } = data

        // if (status == 200 && code == 200) {


        // } else if (code == 401) {
        //   emailNotExist.value = true
        // }

      } catch (error) {
        // console.log(error)
        // emailNotExist.value = true
        // submitResetPasswordEmailError.value = 'អ៊ីមែលរបស់អ្នកមិនមាននៅក្នុងប្រព័ន្ធរបស់យើងទេ។'
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