<template>
  
  <v-navigation-drawer
    theme="dark"
    permanent
    style="padding-top: 10px; background-color: #212632;"
  >

    <template v-slot:prepend>
      <v-list-item
        lines="two"
        prepend-icon="mdi-account-circle"
        :title="'Admin'"
      ></v-list-item>
    </template>

    <v-divider></v-divider>
    
    <v-list color="transparent">
      <v-list-item prepend-icon="mdi-account-multiple">
        <RouterLink to="/">អ្នកប្រេីប្រាស់</RouterLink>
      </v-list-item>

      <v-list-item prepend-icon="mdi-text-box-multiple">
        <RouterLink to="/records">ឯកសារ</RouterLink>
      </v-list-item>

      <v-list-item prepend-icon="mdi-delete-outline">
        <RouterLink to="/trashbin">ធុងសម្រាម</RouterLink>
      </v-list-item>
    </v-list>

    <template v-slot:append>
      <div class="pa-2">
        <v-btn block style="background-color: #1867C0">
          <RouterLink to="/" style="color: white !important;">
            ចាកចេញ
          </RouterLink>
        </v-btn>
      </div>
    </template>
  </v-navigation-drawer>
</template>


<style>
  .v-list-item__content a {
    color: white;
    font-weight: bold;
  }

  a {
    text-decoration: none;
  }
</style>