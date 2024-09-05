<template>

  <div style="padding: 0 15px">
    
    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      :headers="headers"
      :items="fetchedData"
      :items-length="totalItems"
      :loading="loading"
      item-value="name"
      @update:options="loadItems"
    >
      <template v-slot:top>
        <v-toolbar flat >

            <input type="text" placeholder="ស្វែករកឯកសារ..." v-model="search"/>
            <v-select
              v-model="filterCategory"
              :items="filterOptions"
              :item-title="item => item.text"
              :item-value="item => item.value"
              label="ស្វែងរកតាមរយ:"
              @update:model-value="updateFilter"
              class="c-form-field"
              style="max-width: 200px !important; margin-left: 20px;"
            ></v-select>

            <v-text-field
              v-if="filterCategory === 'filtering_date'"
              v-model="startDate"
              label="កាលបរិច្ឆេទចាប់ផ្តើម"
              class="c-form-field"
              type="date"
            ></v-text-field>

            <v-text-field
              v-if="filterCategory === 'filtering_date'"
              v-model="endDate"
              label="កាលបរិច្ឆេទបញ្ចប់"
              class="c-form-field"
              type="date"
            ></v-text-field>

            <v-select
              v-if="filterCategory === 'filtering_current_address' || filterCategory === 'filtering_pob_address'"
              v-model="selectedProvince"
              :items="provincesFiltering"
              :item-title="item => item.name"
              :item-value="item => item.id"
              label="ខេត្ត"
              @update:model-value="updateAddressSelection(filterCategory, filterCategory === 'filtering_current_address' ? 'province' : 'pob_province')"
              class="c-form-field"
            ></v-select>

            <v-select
              v-if="filterCategory === 'filtering_current_address' || filterCategory === 'filtering_pob_address'"
              v-model="selectedDistrict"
              :items="districtsFiltering"
              :item-title="item => item.name"
              :item-value="item => item.id"
              label="ស្រុក"
              class="c-form-field"
              @update:model-value="updateAddressSelection(filterCategory, filterCategory === 'filtering_current_address' ? 'district' : 'pob_district')"
            ></v-select>

            <v-select
              v-if="filterCategory === 'filtering_current_address' || filterCategory === 'filtering_pob_address'"
              v-model="selectedCommune"
              :items="communesFiltering"
              :item-title="item => item.name"
              :item-value="item => item.id"
              class="c-form-field"
              label="ឃុំ"
              @update:model-value="updateAddressSelection(filterCategory, filterCategory === 'filtering_current_address' ? 'commune' : 'pob_commune')"
            ></v-select>

            <v-select
              v-if="filterCategory === 'filtering_user'"
              v-model="selectedUser"
              :items="usersList"
              :item-title="item => item.name"
              :item-value="item => item.id"
              class="c-form-field"
              label="ឈ្មោះមន្ត្រី"
              @update:model-value="filterUser(selectedUser)"

            ></v-select>

            <v-btn icon @click="clearFilter">
              <v-icon>mdi-filter-remove</v-icon>
            </v-btn>
          <v-spacer></v-spacer>

          <!-- adding new record ==> a custom form dialog -->
          <v-dialog v-model="dialog" max-width="90%" persistent>
            <template v-slot:activator="{ props }">
              <v-btn
                class="mb-2 primary-btn"
                v-bind="props"
                prepend-icon="mdi-plus-circle"
              >
                បង្កេីតឯកសារថ្មី
              </v-btn>
            </template>

            <v-card>
              <v-card-title class="form-title">
                <v-text-field
                  v-model="formName"
                  style="text-align: center; width: 250px; font-size: 20px; font-weight: bold; margin: auto;"
                ></v-text-field>
              </v-card-title>
  
              <v-card-text>
                <v-container style="margin: 0; max-width: 100% !important;">
                  <v-row>

                    <!-- form detail -->
                    <v-col cols="12" sm="2">
                      <template v-for="field in inputFields">

                        <v-text-field
                          v-if="field.type === 'text'"
                          v-model="field.value"
                          :label="field.label"
                          :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
                        ></v-text-field>

                        <v-file-input 
                          v-else 
                          v-model="field.value"
                          :label="field.label" 
                          :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
                          accept=".jpg,.png,.pdf"
                          @change="handleFileChange($event, field)"
                        ></v-file-input>

                      </template>
                    </v-col>


                    <!-- personal information -->

                    <v-col cols="12" sm="10">
                      <v-row>
                        <template v-for="field in personalInfoInputFields.slice(0, 21)" >
                          <v-col cols="12" :sm="field.col" :offset-md="field.offset || 0">

                            <v-select
                              v-if="field.type === 'select'"
                              v-model="field.value"
                              :label="field.label"
                              :items="field.options"
                              :item-title="item => item.name"
                              :item-value="item => item.id"
                              :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
                              @update:model-value="updateAddressSelection(field, null)"
                            ></v-select>

                            <v-text-field
                              v-else
                              v-model="field.value"
                              :label="field.label"
                              :type="field.type"
                              :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
                            ></v-text-field>

                          </v-col>
                        </template>
                      </v-row>
                    </v-col>


                    <!-- page 1 files input -->
                    <v-col class="column-5" cols="12" sm="6" v-for="field in fingerPrintsFileInput">
                      <v-file-input 
                        v-model="field.value" 
                        :label="field.label" 
                        accept=".jpg,.png,.pdf"
                        :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>


                    <!-- Full fingers prints file input -->
                    <!-- to follow the original template ==> use statically -->
                    <v-col cols="12" sm="4">
                      <v-file-input 
                        v-model="fullFingersPrintFileInput[0].value" 
                        :label="fullFingersPrintFileInput[0].label" 
                        accept=".jpg,.png,.pdf"
                        :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>

                    <v-col cols="12" sm="4">
                      <p style="text-align: center; margin-bottom: 12px;">
                        ផ្តិតមេដៃទាំងពីរ
                      </p>
                      <v-row>

                        <v-col cols="12" sm="6">
                          <v-file-input 
                            v-model="fullFingersPrintFileInput[1].value" 
                            :label="fullFingersPrintFileInput[1].label" 
                            accept=".jpg,.png,.pdf"
                            :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                          ></v-file-input>
                        </v-col>

                        <v-col cols="12" sm="6">
                          <v-file-input 
                            v-model="fullFingersPrintFileInput[2].value" 
                            :label="fullFingersPrintFileInput[2].label" 
                            accept=".jpg,.png,.pdf"
                            :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                            @change="handleFileChange($event, field)"
                          ></v-file-input>
                        </v-col>

                      </v-row>
                    </v-col>

                    <v-col cols="12" sm="4">
                      <v-file-input 
                        v-model="fullFingersPrintFileInput[3].value" 
                        :label="fullFingersPrintFileInput[3].label" 
                        accept=".jpg,.png,.pdf"
                        :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>


                    <!-- Full body pictures -->
                    <template v-for="field in fullBodyPhotoFileInput">
                      <v-col cols="12" sm="4">
                        <v-file-input 
                          v-model="field.value" 
                          :label="field.label" 
                          accept=".jpg,.png,.pdf"
                          :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                          @change="handleFileChange($event, field)"
                        ></v-file-input>
                      </v-col>
                    </template>


                    <!-- end of page 1 input fields -->
                    <v-divider></v-divider>
                    <div style="height: 50px;"></div>

                    <!-- parents info -->
                    <v-col cols="12" sm="6">
                      <template v-for="field in personalInfoInputFields.slice(21, 25)">
                          <v-text-field
                            v-model="field.value"
                            :label="field.label"
                            :type="field.type"
                            :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
                          ></v-text-field>
                      </template>
                    </v-col>


                    <!-- officers in charge -->
                    <v-col cols="12" sm="6">
                      <template v-for="field in personalInfoInputFields.slice(25, 30)" >
                        <v-text-field
                          v-model="field.value"
                          :label="field.label"
                          :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
                        ></v-text-field>
                      </template>
                    </v-col>

                    <v-col cols="12" sm="6" v-for="field in palmPrintFileInput">
                      <v-file-input 
                        v-model="field.value" 
                        :label="field.label" 
                        accept=".jpg,.png,.pdf"
                        :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>


                    <!-- Special mark -->
                    <v-col
                      v-for="field in specialMark"
                      :key="field.key"
                      cols="12"
                      sm="4"
                      class="special-mark-item"
                    >
                      <v-file-input 
                        v-model="field.value" 
                        :label="field.label" 
                        accept=".jpg,.png,.pdf"
                        :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>


                    <v-col
                      :key="formTemplate.key"
                      cols="12"
                      sm="2"
                      class="special-mark-item"
                    >
                      <v-file-input 
                        v-model="formTemplate.value" 
                        :label="formTemplate.label" 
                        accept=".jpg,.png,.pdf"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>
                    
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
                  @click="createRecord"
                  :disabled="formNotSubmitable"
                >
                  រក្សាទុក
                </v-btn>
              </v-card-actions>

            </v-card>
          </v-dialog>

          <!-- Delete record confirmation modal -->
          <v-dialog v-model="dialogDelete" max-width="40%">
            <v-card>
              <v-card-title style="padding: 30px !important;" class="text-h5">តើអ្នកប្រាកដថាចង់លុបឯកសារនេះទេ?</v-card-title>
              <v-card-actions style="padding-bottom: 30px !important;">
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1 primary-btn" variant="text" @click="closeDelete">បោះបង់</v-btn>
                <v-btn class="danger-btn" @click="deleteItemConfirm">លុបចោល</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          
        </v-toolbar>
      </template>

      <template v-slot:item="{ item, index }">
        <tr @click="viewRecord(item)" style="cursor: pointer;">

          <td v-for="header in headers" :key="index">
            <template v-if="header.key === 'full_name'">
              {{ item.first_name + ' ' + item.last_name }}
            </template>
            
            <template v-else>
              {{ item [header.key] }}
            </template>
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

  import { 
    getProvinces as getProvincesAPI,  
    getDistricts as getDistrictsAPI,
    getCommunes as getCommunesAPI
  } from '@/_api/address'

  import { getAllRecords, createRecord as createRecordAPI } from '@/_api/document'
  import { getAllUsers as getAllUsersAPI } from '@/_api/user'

  export default {
    data: () => ({

      filterOptions: [
        { text: 'កាលបរិច្ឆេទ', value: 'filtering_date' },
        { text: 'អាស័យដ្ឋានបច្ចុប្បន្ន', value: 'filtering_current_address' },
        { text: 'ទីកន្លែងកំណើត', value: 'filtering_pob_address' },
        { text: 'មន្ត្រី', value: 'filtering_user' }
      ],
      filterCategory: "",
      startDate: "",
      endDate: "",
      selectedProvince: "",
      selectedDistrict: "",
      selectedCommune: "",
      selectedUser: "",
      provincesFiltering: [],
      districtsFiltering: [],
      communesFiltering: [],
      usersList: [],

      itemsPerPage: 5, //
      search: '',
      fetchedData: [],
      loading: true,
      totalItems: 20,//

      dialog: false,
      dialogDelete: false,
      headers: [
          { title: 'លេខរៀង', key: 'id', sortable: false },
          { title: 'លេខសៀវភៅ', key: 'book_id' },
          { title: 'នាមគោត្តនាម', align: 'start', key: 'full_name' },
          { title: 'អាសយដ្ឋាន', key: 'current_address', sortable: false },
          { title: '', key: 'actions', sortable: false }
        ],
      editedIndex: -1,
      editedItem: {
        name: '',
        id: 0,
        book_id: "",
        nationality: "",
        address: "",
        book_id: ""
      },
      defaultItem: {
        name: '',
        id: 0,
        nationality: "",
        address: "",
        book_id: ""
      },

      formName: 'សលាកប័ត្រឯកកត្តជន',
      inputFields: [
        {
          key: "number",
          label: "លេខ",
          type: "text",
          value: "12345"
        }, {
          key: "identity_photo",
          label: "រូបថត ៤x៦",
          type: "file",
          value: ""
        },  {
          key: "book_id",
          label: "លេខសៀវភៅ",
          type: "text",
          value: "ABC123"
        }, {
          key: "madeAt",
          label: "ធ្វេីនៅ",
          type: "text",
          value: "Phnom Penh"
        }
      ],

      personalInfoInputFields: [{
        key: "formula",
        label: "រូបមន្ត",
        type: "text",
        col: 6,
        offset: 6,
        value: "Formula123"
      }, {
        key: "last_name",
        label: "គោត្តនាម",
        type: "text",
        col: 4,
        value: "Smith"
      }, {
        key: "first_name",
        label: "នាម",
        type: "text",
        col: 4,
        value: "John"
      }, {
        key: "nickname",
        label: "ឈ្មោះហៅក្រៅ",
        type: "text",
        col: 4,
        value: "Johnny"
      }, {
        key: "dob",
        label: "ថ្ងៃខែឆ្នាំកំណេីត",
        type: "date",
        col: 12,
        value: "1990-01-01"
      }, {
        key: "pob_province",
        label: "ខេត្ត/ក្រុងកំណេីត",
        type: "select",
        options: [],
        col: 4,
        value: "Phnom Penh"
      }, {
        key: "pob_district",
        label: "ស្រុក/ខណ្ឌកំណេីត",
        type: "select",
        options: [],
        col: 4,
        value: "Chamkarmon"
      }, {
        key: "pob_commune",
        label: "ភូមិ/សង្កាត់កំណេីត",
        type: "select",
        options: [],
        col: 4,
        value: "Boeung Keng Kang I"
      }, {
        key: "ethnicity",
        label: "ជនជាតិ",
        type: "text",
        col: 4,
        value: "Khmer"
      }, {
        key: "nationality",
        label: "សញ្ជាតិ",
        type: "text",
        col: 4,
        value: "Cambodian"
      }, {
        key: "religion",
        label: "សាសនា",
        type: "text",
        col: 4,
        value: "Buddhist"
      }, {
        key: "previous_occupation",
        label: "មុខរបរធ្លាប់ធ្វេីពីមុន",
        type: "text",
        col: 6,
        value: "Teacher"
      }, {
        key: "occupation",
        label: "មុខរបរបច្ចុប្បន្ន",
        type: "text",
        col: 6,
        value: "Engineer"
      }, {
        key: "current_address",
        label: "អាស័យដ្ឋានបច្ចុប្បន្ន",
        type: "text",
        col: 12,
        value: "123 Main St, Phnom Penh"
      }, {
        key: "province",
        label: "ខេត្ត/ក្រុង",
        type: "select",
        options: [],
        col: 4,
        value: "Phnom Penh"
      }, {
        key: "district",
        label: "ស្រុក/ខណ្ឌ",
        type: "select",
        options: [],
        col: 4,
        value: "Chamkarmon"
      }, {
        key: "commune",
        label: "ភូមិ/សង្កាត់",
        type: "select",
        options: [],
        col: 4,
        value: "Boeung Keng Kang I"
      }, {
        key: "identity",
        label: "ភិនភាគ",
        type: "text",
        col: 10,
        value: "123456789"
      }, {
        key: "height",
        label: "កម្ពស់ (ម៉ែត្រ)",
        type: "text", // number
        col: 2,
        value: "1.75"
      }, {
        key: "spouse",
        label: "ប្តី ឬ ប្រពន្ធ",
        type: "text",
        col: 6,
        value: "Jane Doe"
      }, {
        key: "spouse_address",
        label: "នៅ",
        type: "text",
        col: 6,
        value: "456 Elm St, Phnom Penh"
      },

        // parents info
        {
          key: "father_name",
          label: "ឪពុកឈ្មោះ",
          type: "text",
          col: 12,
          value: "James Smith"
        }, {
          key: "father_address",
          label: "នៅ",
          type: "text",
          col: 12,
          value: "789 Oak St, Phnom Penh"
        }, {
          key: "mother_name",
          label: "ម្តាយឈ្មោះ",
          type: "text",
          col: 12,
          value: "Mary Smith"
        }, {
          key: "mother_address",
          label: "នៅ",
          type: "text",
          col: 12,
          value: "789 Oak St, Phnom Penh"
        },

        // officers in charge
        {
          key: "ផrivate_certificate_officer",
          // label: "មន្ត្រីធ្វេីសលាកប័ត្រឯកកត្តជន",
          label: "មន្ត្រីធ្វេីឯកសារ",
          type: "text",
          col: 12,
          value: "Officer A"
        }, {
          key: "supervision_officer",
          label: "មន្ត្រីបែងចែកត្រួតពិនិត្យ",
          type: "text",
          col: 12,
          value: "Officer B"
        }, {
          key: "scheduling_research_officer",
          label: "មន្ត្រីស្រាវជ្រាវ រៀបតារាង",
          type: "text",
          col: 12,
          value: "Officer C"
        }],

        fingerPrintsFileInput: [
          { key: "leftThumbPrint",  label : "មេដៃឆ្វេង",      type  : "file", value: "" },
          { key: "leftIndexPrint",  label : "ចង្អុលដៃឆ្វេង",    type: "file", value: "" },
          { key: "leftMiddlePrint", label : "ដៃកណ្តាលឆ្វេង",  type  : "file", value: "" },
          { key: "leftRingPrint",   label : "នាងដៃឆ្វេង",     type  : "file", value: "" },
          { key: "leftPinkyPrint",  label : "កូនដៃឆ្វេង",      type : "file", value: "" },
          
          { key: "rightThumbPrint",  label: "មេដៃស្តាំ",      type  : "file", value: "" },
          { key: "rightIndexPrint",  label: "ចង្អុលដៃស្តាំ",    type: "file", value: "" },
          { key: "rightMiddlePrint", label: "ដៃកណ្តាលស្តាំ",  type  : "file", value: "" },
          { key: "rightRingPrint",   label: "នាងដៃស្តាំ",     type  : "file", value: "" },
          { key: "rightPinkyPrint",  label: "កូនដៃស្តាំ",      type : "file", value: "" },
        ],

        fullFingersPrintFileInput: [
          { key: "fourLeftFingersPrint", label: "ផ្តិតម្រាមដៃឆ្វេងទាំងបួន", type: "file", value: "" },
          { key: "leftThumbPrint01", label: "មេដៃឆ្វេង", type: "file", value: "" },
          { key: "rightThumbPrint01", label: "មេដៃស្តាំ", type: "file", value: "" },
          { key: "fourRightFingersPrint", label: "ផ្តិតម្រាមដៃស្តាំទាំងបួន", type: "file", value: "" },
        ],

        fullBodyPhotoFileInput: [
          { key: "frontBodyPhoto", label: "រូបមួយជំហរ", type: "file", value: "" },
          { key: "rightProfilePhoto", label: "រូបចំហៀងស្តាំ", type: "file", value: "" },
          { key: "leftProfilePhoto",  label: "រូបចំហៀងឆ្វេង", type: "file", value: "" },
        ],

        palmPrintFileInput: [
          { key: "leftPalmPrint",  label: "បាតដៃឆ្វេង", type: "file", value: "" },
          { key: "rightPalmPrint", label: "បាតដៃស្តាំ", type: "file", value: "" },
        ],

        specialMark: [
          { key: "specialMark1", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "" },
          { key: "specialMark2", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "" },
          { key: "specialMark3", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "" },
        ],

        formTemplate: {key: "formTemplate", label: 'រូបភាពឯកសារ (បេីមាន)', type: 'file', value: ""},

    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'ពត័មានអ្នកប្រេីប្រាស់ថ្មី' : 'កែពត័មានអ្នកប្រេីប្រាស់'
      },

      formNotSubmitable() {
        return !this.inputFields.every(item => item.value) 
          && !this.personalInfoInputFields.every(item => item.value) 
          && !this.fingerPrintsFileInput.every(item => item.value) 
          && !this.palmPrintFileInput.every(item => item.value)
      },

      async getProvincesID () {
        try {
          const { data: { data: { item: provinces } } } = await getProvincesAPI()
          return provinces.map(province => province.id)
        } catch (error) {
          console.log("error", error);
        }
      },

      async getAllProvinces() {
        this.personalInfoInputFields.map (async field => {
          if (field.key.includes("province")) {
            field.options = await this.fetchProvinces()
          }
        })
      },
    },

    watch: {
      dialog (val) {
        val || this.close()
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
      endDate(val) {
        if (this.startDate && this.endDate) {
          this.loadItems({
            others: {
              between: 'madeAt',
              min: this.startDate,
              max: this.endDate
            }
          })
        }
      },
      search(val) {
        this.loadItems({
          keySearch: {
            first_name: val, 
            last_name: val,
            book_id: val,
            number: val
          }
        })
      }
    },

    created () {
      this.initialize()
      this.getAllProvinces
      console.log("this.loading", this.loading);
    },

    methods: {

      async filterUser (userId) {
        this.loadItems({
          filters: {
            upload_by: userId
          }
        })
      },

      // for query purpose
      async fetchAllUsers () {
        try {
          const { data: { data: { items } } } = await getAllUsersAPI()
          return items
          
        } catch (error) {
          console.log("error===", error);
          
        }
      },

      resetFilterValue () {
        this.startDate = ""
        this.endDate = ""
        this.selectedProvince = ""
        this.selectedDistrict = ""
        this.selectedCommune = ""
        this.selectedUser = ""
        this.loadItems()
      },

      async updateFilter (val) {
        this.resetFilterValue()

        if (this.filterCategory === 'filtering_current_address' || this.filterCategory === 'filtering_pob_address') {
          this.provincesFiltering = await this.fetchProvinces()
          
        } else if (this.filterCategory === 'filtering_user') {
          this.usersList = await this.fetchAllUsers()
          
        }
      },

      clearFilter() {
          this.filterCategory = "";
          this.startDate = "";
          this.endDate = "";
          this.selectedProvince = "";
          this.selectedDistrict = "";
          this.selectedCommune = "";
          this.selectedUser = "";
          this.districtsFiltering = [];
          this.communesFiltering = [];
          this.loadItems()
        },

      async updateAddressSelection(category, key) {

        const resetField = async (field, newOptions = []) => {
          field.value = null;
          field.options = newOptions;
        };

        const updateFields = async (keyToMatch, fetchFunction) => {
          await Promise.all(
            this.personalInfoInputFields.map(async (field) => {
              if (field.key === keyToMatch) {
                const options = fetchFunction ? await fetchFunction(category.value) : [];
                await resetField(field, options);
              }
            })
          );
        };

        // for the table filtering purpose
        if (key) {

          let filters = {};

          switch (key) {
            case 'province':
            case 'pob_province':
              this.districtsFiltering = [];
              this.communesFiltering = [];
              filters[key] = this.selectedProvince;
              this.districtsFiltering = await this.fetchDistricts(this.selectedProvince);
              break;

            case 'district':
            case 'pob_district':
              this.communesFiltering = [];
              filters[key] = this.selectedDistrict;
              this.communesFiltering = await this.fetchCommunes(this.selectedDistrict);
              break;

            case 'commune':
            case 'pob_commune':
              filters[key] = this.selectedCommune;
              break;

            default:
              break;
          }

          this.loadItems({ filters });
        }


        switch (category.key) {
          case "pob_province":
            await updateFields("pob_district", this.fetchDistricts);
            await updateFields("pob_commune");
            break;

          case "province":
            await updateFields("district", this.fetchDistricts);
            await updateFields("commune");
            break;

          case "pob_district":
            await updateFields("pob_commune", this.fetchCommunes);
            break;

          case "district":
            await updateFields("commune", this.fetchCommunes);
            break;
            
          default:
            break;
        }
      },


      async fetchProvinces () {
        try {
          const { data: { data: { item: provinces } } } = await getProvincesAPI()
          return provinces
        } catch (error) {
          console.log("error", error);
        }
      },


      async fetchDistricts (provinceID) {
        try {
          const { data: { data: { item: districts } } } = await getDistrictsAPI(provinceID)
          return districts

        } catch (error) {
          console.log("error", error);
        }
      },


      async fetchCommunes (districtID) {
        try {
          const { data: { data: { item: communes } } } = await getCommunesAPI(districtID)
          return communes

        } catch (error) {
          console.log("error", error);
        }
      },


      async fetchRecordData (params) {
        const { data: { data } } = await getAllRecords(params)
        return data
      },

      async fetchUsers () {
        try {
          const { data: { data: { item: users } } } = await getAllUsers()
          
        } catch (error) {
          console.log("error===", error);
          
        }
      },

      initialize () {
        this.fetchedData = []
      },

      async loadItems (params) {
        if (params) {
          const { page, itemsPerPage, sortBy } = params
        }
        this.loading = true
        // fetchRecordData.fetch({ page, itemsPerPage, sortBy }).then(({ items, total }) => {
        //   this.fetchedData = items
        //   console.log("in loaditem", this.fetchedData);
          
        //   this.totalItems = total
        //   this.loading = false
        // })
        let data = await this.fetchRecordData(params || {})
        this.fetchedData = data.items
        this.totalItems = data.meta.total
        this.loading = false
      },

      editItem (item) {
        this.editedIndex = this.fetchedData.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialog = true
      },

      deleteItem (item) {
        this.editedIndex = this.fetchedData.indexOf(item)
        this.editedItem = Object.assign({}, item)
        this.dialogDelete = true
      },

      deleteItemConfirm () {
        this.fetchedData.splice(this.editedIndex, 1)
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
        this.$router.push("/records/" + id) //@TODO: use the uuid / number instead
      },

      handleFileChange(event, key) {
        const file = event.target.files[0];
        if (file) {
          this[key] = [{ key, value: file, type: 'file' }];
        }
      },

      async createRecord () {

        let formData = new FormData();

        const createObject = [
          ...this.inputFields, 
          ...this.personalInfoInputFields, 
          ...this.fingerPrintsFileInput, 
          ...this.fullFingersPrintFileInput, 
          ...this.fullBodyPhotoFileInput, 
          ...this.palmPrintFileInput, 
          ...this.specialMark,
          this.formTemplate
        ]

        console.log("createObject", createObject);

        createObject.forEach(item => {
          formData.append(item.key, item.value);
        });

        console.log("formData", formData);

        formData.forEach((value, key) => {
          console.log(`${key}:`, value);
        });

        try {

          const result = await createRecordAPI(formData, createObject)
          console.log("result====", result);
          
          await this.loadItems()
          
        } catch (error) {
          console.log(error);
        }
      },
    },
  }
</script>


<style scoped src="../../styles/table.scss"></style>
<style scoped src="../../styles/records.scss"></style>
<style scoped src="../../styles/form.scss"></style>

