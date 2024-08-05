<template>

  <div style="padding: 0 15px">
    
    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      :headers="headers"
      :items="serverItems"
      :items-length="totalItems"
      :loading="loading"
      :search="search"
      item-value="name"
      @update:options="loadItems"
    >
      <template v-slot:top>
        <v-toolbar flat >

          <input
            type="text"
            placeholder="ស្វែករកឯកសារ..."
            v-model="search"
          />

          <v-spacer></v-spacer>

          <v-dialog
            v-model="dialog"
            max-width="500px"
          >
            <template v-slot:activator="{ props }">
              <v-btn
                class="mb-2"
                color="primary"
                v-bind="props"
                prepend-icon="mdi-plus-circle"
              >
                បញ្ចូលថ្មី
              </v-btn>
            </template>

            <v-card>
              <v-card-title>
                <span class="text-h5">{{ formTitle }}</span>
              </v-card-title>
  
              <v-card-text>
                <v-container>
                  <v-row>
                    <!-- adding new item -->
                     <template v-for="key in headers.slice(1, headers.length-1)">
                      <v-col cols="12" md="4" sm="6">
                        <v-text-field
                          v-model="editedItem[key.key]"
                          :label="key.title"
                        ></v-text-field>
                      </v-col>
                     </template>
                  </v-row>
                </v-container>
              </v-card-text>
  
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                  color="blue-darken-1"
                  variant="text"
                  @click="close"
                >
                  បោះបង់
                </v-btn>

                <v-btn
                  color="blue-darken-1"
                  variant="text"
                  @click="save"
                >
                  រក្សាទុក
                </v-btn>

              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- Delete user modal -->
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">តើអ្នកប្រាកដថាចង់លុបអ្នកប្រើប្រាស់នេះទេ?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDelete">បោះបង់</v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">លុបចោល</v-btn>
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

  const fetchUserData = {
      async fetch ({ page, itemsPerPage, sortBy }) {
        // return new Promise(resolve => {
        //   setTimeout(() => {
        //     const start = (page - 1) * itemsPerPage
        //     const end = start + itemsPerPage
            
        //     const items = this.serverItems.slice()

        //     if (sortBy.length) {
        //       const sortKey = sortBy[0].key
        //       const sortOrder = sortBy[0].order
        //       items.sort((a, b) => {
        //         const aValue = a[sortKey]
        //         const bValue = b[sortKey]
        //         return sortOrder === 'desc' ? bValue - aValue : aValue - bValue
        //       })
        //     }

        //     const paginated = items.slice(start, end)

        //     resolve({ items: paginated, total: items.length })
        //   }, 500)
        // })
      },
    }
  export default {
    data: () => ({

      itemsPerPage: 5, //
      search: '',
      serverItems: [],
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
        { title: 'សញ្ជាតិ', key: 'nationality', sortable: false  },
        { title: 'អាសយដ្ឋាន', key: 'address', sortable: false  },
        { title: 'មុខរបរ', key: 'occupation' },
        { title: '', key: 'actions', sortable: false },
      ],
      editedIndex: -1,
      editedItem: {
        name: '',
        id: 0,
        nationality: "",
        address: "",
        occupation: ""
      },
      defaultItem: {
        name: '',
        id: 0,
        nationality: "",
        address: "",
        occupation: ""
      },
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

    created () {
      this.initialize()
      console.log("this.loading", this.loading);
    },

    methods: {
      initialize () {
        this.serverItems = [
          {
            name: 'David Lee',
            id: 159,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
            occupation: "លក់ដូរ",
          },
          {
            name: 'Daniel Lee',
            id: 237,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
            occupation: "លក់ដូរ",
          },
          {
            name: 'Neary Lee',
            id: 262,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
            occupation: "លក់ដូរ",
          },
          {
            name: 'Bopha Lee',
            id: 305,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
            occupation: "លក់ដូរ",
          },
          {
            name: 'Dyna Lee',
            id: 356,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
            occupation: "លក់ដូរ",
          }
        ]
      },

      loadItems ({ page, itemsPerPage, sortBy }) {
        this.loading = true
        // fetchUserData.fetch({ page, itemsPerPage, sortBy }).then(({ items, total }) => {
        //   this.serverItems = items
        //   console.log("in loaditem", this.serverItems);
          
        //   this.totalItems = total
        //   this.loading = false
        // })
        this.loading = false
      },

      editItem (item) {
        this.editedIndex = this.serverItems.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        this.editedIndex = this.serverItems.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
      },

      deleteItemConfirm () {
        this.serverItems.splice(this.editedIndex, 1)
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

      save () {
        if (this.editedIndex > -1) {
          Object.assign(this.serverItems[this.editedIndex], this.editedItem)
        } else {
          this.serverItems.push(this.editedItem)
        }
        this.close()
      },
    },
  }
</script>


<style scoped src="../styles/table.scss"></style>