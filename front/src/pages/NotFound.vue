<template>
  <v-container class="text-center">
    <v-row>
      <v-col>
        <h1>404 - ទំព័រមិនអាចរកឃើញ</h1>
        <p class="mb-2">ទំព័រដែលអ្នកកំពុងស្វែងរកមិនមានទេ។</p>
        <v-btn color="primary" @click="$router.push('/')">ទៅទំព័រដើម</v-btn>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
export default {
  name: 'NotFound'
};
</script>

<style scoped>
h1 {
  font-size: 3rem;
  margin-top: 2rem;
}
</style>