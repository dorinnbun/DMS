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

          <input type="text" placeholder="ស្វែករកឯកសារ..." v-model="search"/>

          <v-spacer></v-spacer>

          <!-- adding new record ==> a custom form dialog -->
          <v-dialog v-model="dialog" max-width="90%">
            <template v-slot:activator="{ props }">
              <v-btn
                class="mb-2"
                color="primary"
                v-bind="props"
                prepend-icon="mdi-plus-circle"
              >
                បង្កេីតឯកសារថ្មី
              </v-btn>
            </template>

            <v-card>
              <v-card-title style="text-align: center; margin: auto;">
                <v-text-field
                  v-model="formName"
                  style="text-align: center; width: 250px; font-size: 20px; font-weight: bold; margin: auto;"
                ></v-text-field>
              </v-card-title>
  
              <v-card-text>
                <v-container style="margin: 0;">
                  <v-row>

                    <!-- form detail -->
                    <v-col cols="12" sm="2">
                      <v-text-field
                        v-for="field in inputFields"
                        v-model="field.id"
                        :label="field.label"
                      ></v-text-field>
                    </v-col>


                    <!-- personal information -->
                    <v-col cols="12" sm="10">
                      <v-row>
                        <template v-for="field in personalInfoInputFields.slice(1, 21)" >
                          <v-col cols="12" :sm="field.col">

                            <v-select
                              v-if="field.type === 'select'"
                              v-model="field.id"
                              :label="field.label"
                              :items="field.options"
                            ></v-select>

                            <v-text-field
                              v-else
                              v-model="field.id"
                              :label="field.label"
                              :type="field.type"
                            ></v-text-field>

                          </v-col>
                        </template>
                      </v-row>
                    </v-col>


                    <!-- parents info -->
                    <v-col cols="12" sm="6">
                      <template v-for="field in personalInfoInputFields.slice(21, 25)">
                          <v-text-field
                            v-model="field.id"
                            :label="field.label"
                            :type="field.type"
                          ></v-text-field>
                      </template>
                    </v-col>


                    <!-- officers in charge -->
                    <v-col cols="12" sm="6">
                      <template v-for="field in personalInfoInputFields.slice(25, 30)" >
                        <v-text-field
                          v-model="field.id"
                          :label="field.label"
                        ></v-text-field>
                      </template>
                    </v-col>

                  </v-row>
                </v-container>
              </v-card-text>
  
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="close">
                  បោះបង់
                </v-btn>

                <v-btn color="blue-darken-1" variant="text" @click="save">
                  រក្សាទុក
                </v-btn>
              </v-card-actions>

            </v-card>
          </v-dialog>

          <!-- Delete record confirmation modal -->
          <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">តើអ្នកប្រាកដថាចង់លុបឯកសារនេះទេ?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDelete">បោះបង់</v-btn>
                <v-btn color="red" variant="text" @click="deleteItemConfirm">លុបចោល</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          
        </v-toolbar>
      </template>

      <template v-slot:item="{ item, index }">
        <tr @click="viewRecord(item)" style="cursor: pointer;">

          <td v-for="key in Object.keys(item)" :key="index">
            {{ item [ key ] }}
          </td>

          <td>
            <v-icon small color="blue" @click.stop="editItem(item)">mdi-pencil</v-icon>
            <v-icon small color="red" @click.stop="deleteItem(item)">mdi-delete</v-icon>
          </td>

        </tr>
      </template>
  
    </v-data-table-server>

  </div>
</template>

<script>

  const fetchRecordData = {
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
        { 
          title: 'លេខរៀង', 
          key: 'id', 
          sortable: false  
        },
        { 
          title: 'លេខសៀវភៅ', 
          key: 'bookID' 
        },
        {
          title: 'នាមគោត្តនាម',
          align: 'start',
          key: 'name',
        },
        { 
          title: 'សញ្ជាតិ', 
          key: 'nationality', 
          sortable: false  
        },
        { 
          title: 'អាសយដ្ឋាន', 
          key: 'address', 
          sortable: false  
        },
        { 
          title: '', 
          key: 'actions', 
          sortable: false 
        },
      ],
      editedIndex: -1,
      editedItem: {
        name: '',
        id: 0,
        bookID: "",
        nationality: "",
        address: "",
        bookID: ""
      },
      defaultItem: {
        name: '',
        id: 0,
        nationality: "",
        address: "",
        bookID: ""
      },

      formName: 'សលាកប័ត្រឯកកត្តជន',
      inputFields: [
        {
          key: "id",
          label: "លេខ",
          type: "text",
          value: ""
        },{
          key: "bookID",
          label: "លេខសៀវភៅ",
          type: "text",
          value: ""
        }, {
          key: "madeAt",
          label: "ធ្វេីនៅ",
          type: "text",
          value: ""
        }
      ]
    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'ពត័មានអ្នកប្រេីប្រាស់ថ្មី' : 'កែពត័មានអ្នកប្រេីប្រាស់'
      },

      personalInfoInputFields () {
        return [{
          key: "formula",
          label: "រូបមន្ត",
          type: "text",
          col: 12,
          value: ""
        }, {
          key: "last_name",
          label: "គោត្តនាម",
          type: "text",
          col: 4,
          value: ""
        }, {
          key: "first_name",
          label: "នាម",
          type: "text",
          col: 4,
          value: ""
        }, {
          key: "nickname",
          label: "ឈ្មោះហៅក្រៅ",
          type: "text",
          col: 4,
          value: ""
        }, {
          key: "dob",
          label: "ថ្ងៃខែឆ្នាំកំណេីត",
          type: "date",
          col: 12,
          value: ""
        }, {
          key: "pob_province",
          label: "ខេត្ត/ក្រុងកំណេីត",
          type: "select",
          options: ['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming'], // to fetch 
          col: 4,
          value: ""
        }, {
          key: "pob_district",
          label: "ស្រុក/ខណ្ឌកំណេីត",
          type: "select",
          options: ['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming'], // to fetch 
          col: 4,
          value: ""
        }, {
          key: "pob_commune",
          label: "ភូមិ/សង្កាត់កំណេីត",
          type: "select",
          options: ['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming'], // to fetch 
          col: 4,
          value: ""
        }, {
          key: "ethnicity",
          label: "ជនជាតិ",
          type: "text",
          col: 4,
          value: ""
        }, {
          key: "nationality",
          label: "សញ្ជាតិ",
          type: "text",
          col: 4,
          value: ""
        }, {
          key: "religion",
          label: "សាសនា",
          type: "text",
          col: 4,
          value: ""
        }, {
          key: "previous_occupation",
          label: "មុខរបរធ្លាប់ធ្វេីពីមុន",
          type: "text",
          col: 6,
          value: ""
        }, {
          key: "occupation",
          label: "មុខរបរបច្ចុប្បន្ន",
          type: "text",
          col: 6,
          value: ""
        }, {
          key: "current_address",
          label: "អាស័យដ្ឋានបច្ចុប្បន្ន",
          type: "text",
          col: 12,
          value: ""
        }, {
          key: "province",
          label: "ខេត្ត/ក្រុង",
          type: "select",
          options: ['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming'], // to fetch 
          col: 4,
          value: ""
        }, {
          key: "district",
          label: "ស្រុក/ខណ្ឌ",
          type: "select",
          options: ['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming'], // to fetch 
          col: 4,
          value: ""
        }, {
          key: "commune",
          label: "ភូមិ/សង្កាត់",
          type: "select",
          options: ['California', 'Colorado', 'Florida', 'Georgia', 'Texas', 'Wyoming'], // to fetch 
          col: 4,
          value: ""
        }, {
          key: "identity",
          label: "ភិនភាគ",
          type: "text",
          col: 10,
          value: ""
        }, {
          key: "height",
          label: "កម្ពស់ (ម៉ែត្រ)",
          type: "text", // number
          col: 2,
          value: ""
        }, {
          key: "spouse",
          label: "ប្តី ឬ ប្រពន្ធ",
          type: "text",
          col: 6,
          value: ""
        }, {
          key: "spouse_address",
          label: "នៅ",
          type: "text",
          col: 6,
          value: ""
        }, 

        // parents info
        {
          key: "father_name",
          label: "ឪពុកឈ្មោះ",
          type: "text",
          col: 12,
          value: ""
        }, {
          key: "father_address",
          label: "នៅ",
          type: "text",
          col: 12,
          value: ""
        }, {
          key: "mother_name",
          label: "ម្តាយឈ្មោះ",
          type: "text",
          col: 12,
          value: ""
        }, {
          key: "mother_address",
          label: "នៅ",
          type: "text",
          col: 12,
          value: ""
        }, 
        
        // officers in charge
        {
          key: "ផrivate_certificate_officer",
          // label: "មន្ត្រីធ្វេីសលាកប័ត្រឯកកត្តជន",
          label: `មន្ត្រីធ្វេី${ this.formName }`,
          type: "text",
          col: 12,
          value: ""
        }, {
          key: "supervision_officer",
          label: "មន្ត្រីបែងចែកត្រួតពិនិត្យ",
          type: "text",
          col: 12,
          value: ""
        }, {
          key: "scheduling_research_officer",
          label: "មន្ត្រីស្រាវជ្រាវ រៀបតារាង",
          type: "text",
          col: 12,
          value: ""
        }]
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
            bookID: 88,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
          },
          {
            name: 'Daniel Lee',
            id: 237,
            bookID: 848,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
          },
          {
            name: 'Neary Lee',
            id: 262,
            bookID: 888,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
          },
          {
            name: 'Bopha Lee',
            id: 305,
            bookID: 188,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ",
          },
          {
            name: 'Dyna Lee',
            id: 356,
            bookID: 89,
            nationality: "កម្ពុជា",
            address: "សង្កាត់ចំការមន រាជធានីភ្នំពេញ"
          }
        ]
      },

      loadItems ({ page, itemsPerPage, sortBy }) {
        this.loading = true
        // fetchRecordData.fetch({ page, itemsPerPage, sortBy }).then(({ items, total }) => {
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

      viewRecord (item) {
        const id = item.id;
        console.log("id", id);
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


<style scoped src="../../styles/table.scss"></style>