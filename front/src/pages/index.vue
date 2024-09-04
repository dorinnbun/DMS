<template>

  <div style="padding: 0 15px">
    
    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      :headers="headers"
      :items="fetchedData"
      :items-length="totalItems"
      :loading="loading"
      :search="search"
      item-value="name"
      @update:options="loadItems"
    >
      <template v-slot:top>
        <v-toolbar flat >

          <!-- <input
            type="text"
            placeholder="ស្វែករកឯកសារ..."
            v-model="search"
          /> -->

          <v-spacer></v-spacer>

          <v-dialog
            v-model="dialog"
            max-width="80%"
          >
            <template v-slot:activator="{ props }">
              <v-btn
                class="mb-2 primary-btn"
                v-bind="props"
                prepend-icon="mdi-plus-circle"
              >
                បញ្ចូលថ្មី
              </v-btn>
            </template>

            <v-card>
              <v-card-title style="padding: 30px 0 0 30px !important;">
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>
  
              <v-card-text>
                <v-container>
                  <v-row>

                    <!-- adding new user -->

                    <template v-for="key in createObject">
                      <v-col cols="12" md="4" sm="6">
                        <v-text-field
                          v-if="key.type === 'text'"
                          v-model="key.value"
                          :label="key.label"
                          persistent-hint="false"
                          :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
                        ></v-text-field>

                        <v-text-field
                          v-else-if="key.type === 'password'"
                          v-model="key.value"
                          :type="showPassword ? 'text' : 'password'"
                          :name="key.label"
                          :label="key.label"
                          @click:append-inner="togglePasswordVisibility"
                        >
                          <template v-slot:append-inner>
                            <v-icon @click="togglePasswordVisibility">
                              {{ showPassword ? 'mdi-eye-off' : 'mdi-eye' }}
                            </v-icon>
                          </template>
                        </v-text-field>

                        <v-select
                          v-else-if="key.type === 'select'"
                          v-model="key.value"
                          :label="key.label"
                          :items="key.items"
                          :item-title="item => item.name"
                          :item-value="item => item.id"
                          :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
                        ></v-select>
                        
                      </v-col>
                    </template>

                  </v-row>
                </v-container>
              </v-card-text>
  
              <v-card-actions style="padding: 0 30px 30px 0 !important;">
                <v-spacer></v-spacer>
                <v-btn class="danger-btn" @click="close">
                  បោះបង់
                </v-btn>

                <v-btn
                  color="blue-darken-1 primary-btn"
                  variant="text"
                  @click="createUser"
                  :disabled="!createObject.every(item => item.value)"
                >
                  រក្សាទុក
                </v-btn>

              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- Delete user modal -->
          <v-dialog v-model="dialogDelete" max-width="40%">
            <v-card>
              <v-card-title style="padding: 30px !important;" class="text-h5">តើអ្នកប្រាកដថាចង់លុបអ្នកប្រើប្រាស់នេះទេ?</v-card-title>
              <v-card-actions style="padding-bottom: 30px">
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1 primary-btn" variant="text" @click="closeDelete">បោះបង់</v-btn>
                <v-btn class="danger-btn" @click="deleteItemConfirm">លុបចោល</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          
        </v-toolbar>
      </template>

      <!-- Table's action menu -->
      <template v-slot:item.actions="{ item }">
        <v-icon
          class="me-2"
          size="small"
          @click="editItem(item)"
          color="blue"
        >
          mdi-pencil
        </v-icon>
        <v-icon
          size="small"
          @click="deleteItem(item)"
          color="red"
        >
          mdi-delete
        </v-icon>
      </template>
  
    </v-data-table-server>

  </div>
</template>

<script>

  import { 
    getAllUsers, 
    createUser as createUserAPI,
    getUser as getUserAPI,
    updateUser as updateUserAPI,
    deleteUser as deleteUserAPI
  } from '../_api/user.js'
  import { getAllRoles as getAllRolesAPI } from '../_api/role.js'

  export default {
    data: () => ({

      showPassword: false,

      itemsPerPage: 5, //
      search: '',
      fetchedData: [],
      loading: true,
      totalItems: 20,//

      dialog: false,
      dialogDelete: false,
      headers: [
        { title: 'លេខ', key: 'id', sortable: false  },
        {
          title: 'នាមគោត្តនាម',
          align: 'start',
          key: 'name',
        },
        { title: 'អ៊ីម៉ែល', key: 'email', sortable: false  },
        { title: 'តួនាទី', key: 'role', sortable: false  },
        { title: '', key: 'actions', sortable: false },
      ],
      editedIndex: -1,
      editedItem: {
        name: '',
        id: 0,
        email: "",
        role: "",
      },
      defaultItem: {
        name: '',
        id: 0,
        email: "",
        role: "",
      },
      createObject: [
        {
          label: 'គោត្តនាម',
          key: 'first_name',
          value: '',
          type: 'text'
        }, {
          label: 'នាម',
          key: 'last_name',
          value: '',
          type: 'text'
        }, {
          key: 'email',
          label: 'អ៊ីម៉ែល',
          value: '',
          type: 'text'
        }, {
          key: 'phone_number',
          label: 'លេខទូរស័ព្ទ',
          value: '',
          type: 'text'
        }, {
          key: 'role',
          label: 'តួនាទី',
          value: '',
          type: 'select',
          items: []
        }, {
          key: "password",
          label: "លេខសម្ងាត់",
          value: "",
          type: "password"
        }, 
        // {
        //   key: "confirm_password",
        //   label: "បញ្ជាក់លេខសម្ងាត់",
        //   value: "",
        //   type: "password"
        // }
      ]
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'ពត័មានអ្នកប្រេីប្រាស់ថ្មី' : 'កែពត័មានអ្នកប្រេីប្រាស់'
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
    },

    async created () {
      this.initialize()
      await this.fetchUserData()
      await this.getAllRoles()
      console.log("this.loading", this.loading);
    },

    methods: {

      async getAllRoles () {
        try {
          const { data: { data: { items } } } = await getAllRolesAPI()
          this.createObject.map (item => {
            if (item.key === 'role') {
              item.items = items
            }
          })
          
        } catch (error) {
          console.log("error", error);
        }
      },

    //   fetchUserData {
    //   async fetch ({ page, itemsPerPage, sortBy }) {
    //     return new Promise(resolve => {
    //       setTimeout(() => {
    //         const start = (page - 1) * itemsPerPage
    //         const end = start + itemsPerPage
            
    //         const items = this.fetchedData.slice()

    //         if (sortBy.length) {
    //           const sortKey = sortBy[0].key
    //           const sortOrder = sortBy[0].order
    //           items.sort((a, b) => {
    //             const aValue = a[sortKey]
    //             const bValue = b[sortKey]
    //             return sortOrder === 'desc' ? bValue - aValue : aValue - bValue
    //           })
    //         }

    //         const paginated = items.slice(start, end)

    //         resolve({ items: paginated, total: items.length })
    //       }, 500)
    //     })
    //   },
    // }

      async fetchUserData () {
        const { data: { data } } = await getAllUsers()
        return data
      },

      togglePasswordVisibility() {
        this.showPassword = !this.showPassword;
      },

      initialize () {
        this.fetchedData = []
      },

      async loadItems (params) {
        if (params) {
          const { page, itemsPerPage, sortBy } = params
        }
        this.loading = true
        // fetchUserData.fetch({ page, itemsPerPage, sortBy }).then(({ items, total }) => {
        //   this.fetchedData = items
        //   console.log("in loaditem", this.fetchedData);
          
        //   this.totalItems = total
        //   this.loading = false
        // })
        const data = await this.fetchUserData()
        
        this.fetchedData = data.items
        this.totalItems = data.meta.total

        this.loading = false
      },

      async getSelectedUser (id) {
        try {
          const { data: { data: { item: user } } } = await getUserAPI(id)
          return user
          
        } catch (error) {
          console.log(error);
        }
      },

      async editItem (item) {
        const user = await this.getSelectedUser(item.id)
        console.log("user to edit==", user);
        
        this.editedIndex = this.fetchedData.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        console.log("item to delete====", item);
        
        this.editedIndex = this.fetchedData.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
      },

      async deleteItemConfirm () {
        // this.fetchedData.splice(this.editedIndex, 1)
        try {
          const result = await deleteUserAPI(item.id)
          console.log("resuot after delete", result);
          
          await this.loadItems()

        } catch (error) {
          console.log(error);
        }
        this.closeDelete()
      },

      close () {
        this.dialog = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem)
          this.editedIndex = -1
        })
      },

      async createUser () {

        let bodyObject = {}
        this.createObject.map (item => {
          bodyObject[item["key"]] = item["value"]
        })

        try {
          await createUserAPI({
            name: bodyObject.first_name + bodyObject.last_name,
            email: bodyObject.email,
            role: bodyObject.role,
            password: bodyObject.password,
            phone_number: bodyObject.phone_number
          })
          await this.loadItems()
          
        } catch (error) {
          console.log(error);
        }
        
        this.close()

      },
    },
  }
</script>


<style scoped src="../styles/table.scss"></style>