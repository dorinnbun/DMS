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
            v-model="dialogCreate"
            max-width="80%"
          >
            <template v-slot:activator="{ props }" v-if="userRole !== 'user'">
              <v-btn
                class="mb-2 primary-btn"
                v-bind="props"
                prepend-icon="mdi-plus-circle"
                @click="openCreateDialog"
              >
                បញ្ចូលថ្មី
              </v-btn>
            </template>

            <v-card>
              <v-card-title style="padding: 30px 0 0 30px !important;">
                <span class="text-h5">{{ isEditMode ? 'កែប្រែទិន្នន័យអ្នកប្រេីប្រាស់' : 'បង្កេីតអ្នកប្រេីប្រាស់ថ្មី' }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <template v-for="key in isEditMode ? editObject : createObject">
                      <v-col cols="12" md="4" sm="6">
                        <label for="email" class="mb-2">{{ key.label }}</label>
                        <v-text-field
                          v-if="key.type === 'text'"
                          v-model="key.value"
                          persistent-hint="false"
                          :rules="[
                            v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល', 
                            key.key === 'email' ? v => /.+@.+\..+/.test(v) || 'ទិន្នន័យអ៊ីម៉ែលមិនត្រឹមត្រូវ' : null,
                            key.key === 'phone_number' ? v => /^(\+855|0)\d{8,9}$/.test(v) || 'ទិន្នន័យលេខទូរស័ព្ទមិនត្រឹមត្រូវ' : null
                          ]"
                          placeholder=" "
                        ></v-text-field>

                        <v-text-field
                          v-else-if="key.type === 'password'"
                          v-model="key.value"
                          :type="showPassword ? 'text' : 'password'"
                          :name="key.label"
                          :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល', v => /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,}$/.test(v) || 'ត្រូវតែមានយ៉ាងហោចណាស់8តួ, អក្សរពិសេស1តួ និង លេខ1តួ']"
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
                  @click="isEditMode ? updateUser() : createUser()"
                  :disabled="isEditMode ? !editObject.every(item => item.value) : !createObject.every(item => item.value)"
                >
                  រក្សាទុក
                </v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- Error Dialog -->
            <v-dialog v-model="errorDialog" max-width="350px">
            <v-card style="color: red; font-weight: bold;">
              <v-card-title class="headline">
              <v-icon left color="red" style="margin-right: 12px;">mdi-alert-circle</v-icon>
                Error
              </v-card-title>
              <v-card-text>{{ errorMessage }}</v-card-text>
            </v-card>
            </v-dialog>

            <!-- Success Dialog -->
            <v-dialog v-model="successDialog" max-width="400px">
              <v-card style="font-weight: bold; text-align: center;">
                <v-card-text>
                  <v-icon left color="green" style="margin-right: 12px;">mdi-check-circle</v-icon>
                  {{ successMessage }}
                </v-card-text>
              </v-card>
            </v-dialog>

          <!-- Dialog reset password -->
          <v-dialog
            v-model="resetPasswordDialog"
            max-width="50%"
          >

            <v-card>
              <v-card-title style="padding: 30px 0 0 30px !important;">
                <span class="text-h5">កំណត់លេខសម្ងាត់ឡើងវិញរបស់អ្នកប្រេីប្រាស់: {{ selectedUser.name }}</span>
              </v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <p style="color: red; font-weight: bold; margin-bottom: 12px;">
                      ** លេខសម្ងាត់ត្រូវតែមានយ៉ាងហោចណាស់ 8 តួអក្សរ, យ៉ាងហោចណាស់ អក្សរពិសេស1តួ និង លេខ1តួ**
                    </p>
                    <template v-for="field in resetPasswordFields">
                      <v-col cols="12" md="6" sm="12">
                        <label  class="mb-2">{{ field.label }}</label>
                        <v-text-field
                          v-model="field.value"
                          :type="showPassword ? 'text' : 'password'"
                          persistent-hint="false"
                          :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល', v => /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,}$/.test(v) || 'លេខសម្ងាត់ត្រូវតែមានយ៉ាងហោចណាស់ 8 តួអក្សរ, យ៉ាងហោចណាស់ អក្សរពិសេស1តួ និង លេខ1តួ']"
                          @click:append-inner="togglePasswordVisibility"
                          style="max-width: 300px;"
                        >
                          <template v-slot:append-inner>
                            <v-icon @click="togglePasswordVisibility">
                              {{ showPassword ? 'mdi-eye-off' : 'mdi-eye' }}
                            </v-icon>
                          </template>
                        </v-text-field>
                      </v-col>
                    </template>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions style="padding: 0 30px 30px 0 !important;">
                <v-spacer></v-spacer>
                <v-btn class="danger-btn" @click="resetPasswordDialog = false">
                  បោះបង់
                </v-btn>
                <v-btn
                  color="blue-darken-1 primary-btn"
                  variant="text"
                  @click="resetPasswordConfirm"
                  :disabled="disableResetPasswordBtn"
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
                <v-btn class="danger-btn" @click="deleteUserConfirm">លុបចោល</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- User detail -->
          <v-dialog v-model="dialogDetail" max-width="80%">
            <v-card>
              <v-card-title style="padding: 30px !important;" class="text-h5">ពត័មានអ្នកប្រេីប្រាស់</v-card-title>

              <v-card-text>
                <v-container>
                  <v-row>
                    <template v-for="field in createObject">
                      <v-col cols="12" md="4" sm="6">
                        <div v-if="field.key !== 'password'">
                          {{ field.label }}: {{ field.value }}
                        </div>
                      </v-col>
                    </template>
                  </v-row>
                </v-container>
              </v-card-text>

              <v-card-actions style="padding: 0 30px 30px 0 !important;">
                <v-spacer></v-spacer>
                <v-btn class="danger-btn" @click="closeDetail">បិទ</v-btn>
              </v-card-actions>
            </v-card>
          </v-dialog>
          
        </v-toolbar>
      </template>

      <template v-slot:item="{ item, index }">
        <tr @click="viewUser(item)" style="cursor: pointer;">

          <td v-for="header in headers" :key="index">
            <template v-if="header.key === 'full_name'">
              {{ item.first_name + ' ' + item.last_name }}
            </template>
            
            <template v-else-if="header.key === 'id'">
              {{ (currentParams.page - 1) * itemsPerPage + index + 1 }}
            </template>
            
            <template v-else>
              {{ item [ header.key ] }}
            </template>
          </td>

          <td v-if="userRole === 'admin' || userRole === 'manager'">
            <v-icon small color="blue" @click.stop="editItem(item)" :disabled="item.role === 'user' && userRole === 'manager'">mdi-pencil</v-icon>
            <v-icon small color="red" @click.stop="deleteUser(item)" v-if="userRole === 'admin'">mdi-delete</v-icon>
            <v-icon small color="orange" @click.stop="resetPassword(item)" :disabled="item.role === 'user' && userRole === 'manager'">mdi-lock-reset</v-icon>
          </td>
        </tr>

      </template>
  
    </v-data-table-server>

    <v-dialog v-model="displaySpinner" max-width="400px" persistent>
      <v-card style="background-color: transparent; box-shadow: none;">
        <v-card-text class="d-flex justify-center">
          <v-progress-circular
            color="red"
            indeterminate
            size="64"
          ></v-progress-circular>
        </v-card-text>
      </v-card>
    </v-dialog>

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

  import { useUserStore } from '@/stores/user.js';

  export default {
    data: () => ({

      errorDialog: false,
      errorMessage: 'មានបញ្ហាបច្ចេកទេសកើតឡើង',
      successDialog: false,
      successMessage: 'ប្រតិបត្តិការជោគជ័យ',

      displaySpinner: false,

      showPassword: false,
      selectedUser: {},
      isEditMode: false,

      itemsPerPage: 5, //
      search: '',
      fetchedData: [],
      loading: true,
      totalItems: 20,//
      dialogDetail: false,
      dialogCreate: false,
      dialogDelete: false,
      resetPasswordDialog: false,

      currentParams: {
        page: 1
      },
      itemsPerPage: 10, //

      headers: [
        { title: 'លេខ', key: 'id', sortable: false  },
        {
          title: 'គោត្តនាមនាម',
          align: 'start',
          key: 'name',
          sortable: false
        },
        { title: 'អ៊ីម៉ែល', key: 'email', sortable: false  },
        { title: 'តួនាទី', key: 'role', sortable: false  },
        { title: '', key: 'actions', sortable: false },
      ],
      editedIndex: -1,
      // editedItem: {
      //   name: '',
      //   id: 0,
      //   email: "",
      //   role: "",
      // },
      defaultItem: {
        name: '',
        id: 0,
        email: "",
        role: "",
      },
      createObject: [
        {
          label: 'គោត្តនាមនាម',
          key: 'name',
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
        }
      ],
      editObject: [
        {
          label: 'គោត្តនាមនាម',
          key: 'name',
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
        }
      ],
      resetPasswordFields: [
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
      ]
    }),

    computed: {

      userRole () {
        return useUserStore().user.role
      },
      formTitle () {
        return this.editedIndex === -1 ? 'ពត័មានអ្នកប្រេីប្រាស់ថ្មី' : 'កែពត័មានអ្នកប្រេីប្រាស់'
      },
      messages() {
        return this.rules.map(rule => rule(this.inputValue)).filter((msg) => msg !== true);
      },
      userStore() {
        return useUserStore();
      },
      userRole() {
        return this.userStore.user.role;
      },
      disableResetPasswordBtn () {
        return !this.resetPasswordFields.every(field => field.value) || this.resetPasswordFields[0].value !== this.resetPasswordFields[1].value
      }
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

      const token = localStorage.getItem('dms_token');
      if (!token) {
        this.$router.push('/');
      }

      if (this.userRole === 'user') {
        this.$router.push('/records');
      }
      
      await this.getAllRoles()
    },

    methods: {

      async showSuccessMessage () {
        this.successDialog = true
        setTimeout(() => {
          this.successDialog = false;
        }, 2500);
      },

      async showErrorMessage (message) {
        this.errorMessage = message
        this.errorDialog = true
        setTimeout(() => {
          this.errorDialog = false;
        }, 5000);
      },

      closeDetail () {
        this.dialogDetail = false
        this.createObject.forEach(field => {
          field.value = "";
        });
      },

      async viewUser (item) {
        this.dialogDetail = true
        const user = await this.getSelectedUser(item.id);
        this.createObject.forEach(field => {
          field.value = user[field.key] || '';
        });
        
      },

      openCreateDialog() {
        // Reset fields when creating a new user
        this.isEditMode = false;
        this.createObject.forEach(item => item.value = '');
        this.dialogCreate = true;
      },

      async editItem(item) {

        const user = await this.getSelectedUser(item.id);
        const roles = await this.getAllRoles();

        this.selectedUser = user;

        this.editObject.forEach(field => {
          if (field.key === 'role') {
            field.value = roles.find (role => role.name === user.role).id
          } else {
            field.value = user[field.key] || '';
          }
        });

        this.isEditMode = true;
        this.dialogCreate = true;
      },

      async getAllRoles () {
        try {
          const { data: { data: { items } } } = await getAllRolesAPI()
          this.createObject.map (item => {
            if (item.key === 'role') {
              item.items = items
            }
          })

          this.editObject.map (item => {
            if (item.key === 'role') {
              item.items = items
            }
          })

          return items
          
        } catch (error) {
          console.log("error", error);
        }
      },

      async fetchUserData ({ page, itemsPerPage, sortBy }) {
        
        const { data: { data } } = await getAllUsers({ 
          page, 
          limit: itemsPerPage,
          sort: sortBy
        })
        return {
          items: data.items,
          meta: data.meta
        }
      },

      togglePasswordVisibility() {
        this.showPassword = !this.showPassword;
      },

      initialize () {
        this.fetchedData = []
      },

      loadItems ({ page, itemsPerPage, sortBy }) {  
            
        this.fetchUserData({ page, itemsPerPage, sortBy }).then(({ items, meta }) => {
          this.fetchedData = items;
          this.totalItems = meta.total;
          this.itemsPerPage = itemsPerPage;
          this.loading = false;
        }).catch(() => {
          this.loading = false;
        });
      },

      
      async getSelectedUser (id) {

        try {
          const result = await getUserAPI(id);
          const user = result?.data?.data?.item;
          return user
          
        } catch (error) {
          console.log(error);
        }
      },

      deleteUser (item) {
        this.selectedUser = item
        this.dialogDelete = true
      },

      async deleteUserConfirm () {
        this.displaySpinner = true
        try {
          const result = await deleteUserAPI(this.selectedUser.id)
          this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] })
          this.showSuccessMessage()

        } catch (error) {
          console.log(error);

          const errorMessage = error?.response?.data?.message;
          if (errorMessage) {
            console.log("errorMessaage", errorMessage);
            
            this.showErrorMessage(errorMessage);
          }
        }
        this.closeDelete()
        this.displaySpinner = false
      },
      
      async resetPassword (item) {
        this.selectedUser = item
        this.resetPasswordDialog = true
      },

      async resetPasswordConfirm () {
        this.displaySpinner = true

        try {

          const user = await this.getSelectedUser(this.selectedUser.id); // @TODO: optimize?

          const _roles = await this.getAllRoles()
          const roleID = _roles.find(role => role.name === user.role).id

          const updatedUser = {
            id: user.id,
            name: user.name,
            email: user.email,
            role: roleID,
            password: this.resetPasswordFields.find(field => field.key === 'password').value,
            phone_number: user.phone_number
          };
          await updateUserAPI(updatedUser);
          this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] });
          this.resetPasswordFields.forEach(field => field.value = '');

          this.showSuccessMessage()
          
        } catch (error) {
          console.log(error);

          const errorMessage = error?.response?.data?.message;
          if (errorMessage) {
            console.log("errorMessaage", errorMessage);
            
            this.showErrorMessage(errorMessage);
          }
        }
        
        this.resetPasswordDialog = false
        this.displaySpinner = false
      },



      close () {
        this.dialogCreate = false
      },

      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedIndex = -1
        })
      },

      async createUser () {

        this.displaySpinner = true

        let bodyObject = {}
        this.createObject.map (item => {
          bodyObject[item["key"]] = item["value"]
        })

        try {
          await createUserAPI({
            name: bodyObject.name,
            email: bodyObject.email,
            role: bodyObject.role,
            password: bodyObject.password,
            phone_number: bodyObject.phone_number
          })

          this.showSuccessMessage()
          
        } catch (error) {
          console.log(error);
          const errorMessage = error?.response?.data?.message;
          if (errorMessage) {
            console.log("errorMessaage", errorMessage);
            
            this.showErrorMessage(errorMessage);
          }
        }
        
        this.close()
        this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] })

        this.displaySpinner = false

      },

      async updateUser() {

        this.displaySpinner = true

        try {
          const updatedUser = {
            id: this.selectedUser.id,
            name: this.editObject.find(item => item.key === 'name').value,
            email: this.editObject.find(item => item.key === 'email').value,
            role: this.editObject.find(item => item.key === 'role').value,
            phone_number: this.editObject.find(item => item.key === 'phone_number').value
          };
          await updateUserAPI(updatedUser);
          this.showSuccessMessage()
          this.loadItems({ page: 1, itemsPerPage: this.itemsPerPage, sortBy: [] })
          
        } catch (error) {
          console.log(error);
          const errorMessage = error?.response?.data?.message;
          if (errorMessage) {
            this.showErrorMessage(errorMessage);
          }
        }
        this.close();
        this.displaySpinner = false
      },
    },
  }
</script>


<style scoped src="../styles/table.scss"></style>