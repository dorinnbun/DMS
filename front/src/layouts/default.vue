<template>

  <v-app>
    <v-card>
      <v-layout style="height: 100vh">
        
        <AsideMenu />

        <v-main style="min-height: 100vh">
            <router-view />
        </v-main>
        
      </v-layout>
    </v-card>
    
  </v-app>

</template>

<script setup>
  //
</script>
