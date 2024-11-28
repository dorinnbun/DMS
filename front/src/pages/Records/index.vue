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

            <input class="search" type="text" placeholder="ស្វែករកឯកសារ..." v-model="search"/>
            <v-select
              v-model="filterCategory"
              :items="userRole === 'user' ? filterOptions.slice(0, 3) : filterOptions"
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
              <div class="form-title">
                <div style="flex: 1"></div>

                <v-text-field
                  v-model="formName"
                  style="text-align: center; width: 250px !important; font-size: 20px; font-weight: bold;"
                ></v-text-field>

                <div style="flex: 1; text-align: right;">
                  <v-btn icon @click="close" style="color: red; font-weight: bold;">
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                </div>
              </div>
  
              <v-card-text>
                <v-container style="margin: 0; max-width: 100% !important;">
                  <v-row>

                    <!-- form detail -->
                    <v-col cols="12" sm="2">
                      <template v-for="field in inputFields">
                        <label class="mb-2">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                        <v-text-field
                          v-if="field.type === 'text'"
                          v-model="field.value"
                          :rules="[
                            ...(field.required !== false ? [requiredRule] : []),
                            noSpecialCharactersRule
                          ]"
                        ></v-text-field>

                        <template v-else>
                          <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: auto;">
                          <v-file-input
                            v-model="field.value"
                            :rules="field.required !== false ? [v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល'] : []"
                            accept=".jpg,.jpeg,.pdf,.png,.gif"
                            @change="handleFileChange($event, field)"
                          ></v-file-input>
                        </template>

                      </template>
                    </v-col>


                    <!-- personal information -->

                    <v-col cols="12" sm="10">
                      <v-row>
                        <template v-for="field in personalInfoInputFields.slice(0, 21)" >
                          <v-col cols="12" :sm="field.col" :offset-md="field.offset || 0">
                    
                            <label class="mb-2">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                            <v-select
                              v-if="field.type === 'select'"
                              v-model="field.value"
                              :items="field.options"
                              :item-title="item => item.name"
                              :item-value="item => item.id"
                              :rules="[
                                ...(field.required !== false ? [requiredRule] : []),
                                noSpecialCharactersRule
                              ]"
                              @update:model-value="updateAddressSelection(field, null)"
                            ></v-select>

                            <v-text-field
                              v-else-if="field.type === 'date'"
                              v-model="field.value"
                              :type="field.type"
                              :rules="[
                                ...(field.required !== false ? [requiredRule] : [])
                              ]"
                            ></v-text-field>
                            

                            <v-text-field
                              v-else
                              v-model="field.value"
                              :type="field.type"
                              :rules="[
                                ...(field.required !== false ? [requiredRule] : []),
                                noSpecialCharactersRule
                              ]"
                            ></v-text-field>

                          </v-col>
                        </template>
                      </v-row>
                    </v-col>


                    <!-- page 1 files input -->
                    <v-col class="column-5" cols="12" sm="6" v-for="field in fingerPrintsFileInput">
                      <label class="mb-2">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                      <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                      <v-file-input 
                        v-model="field.value" 
                        accept=".jpg,.png,.pdf,.jpeg"
                        :rules="field.required !== false ? [v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល'] : []"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>


                    <!-- Full fingers prints file input -->
                    <!-- to follow the original template ==> use statically -->
                    <v-col cols="12" sm="4">
                      <label class="mb-2">{{ fullFingersPrintFileInput[0].label }}<span style="color: red" v-if="fullFingersPrintFileInput[0].required"> *</span></label>
                      <img v-if="isEditingMode && fullFingersPrintFileInput[0].value && typeof fullFingersPrintFileInput[0].value === 'string'" :src="fullFingersPrintFileInput[0].value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                      <v-file-input 
                        v-model="fullFingersPrintFileInput[0].value" 
                        accept=".jpg,.png,.pdf,.jpeg"
                        :rules="fullFingersPrintFileInput[0].required !== false ? [v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល'] : []"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>

                    <v-col cols="12" sm="4">
                      <p style="text-align: center; margin-bottom: 12px;">
                        ផ្តិតមេដៃទាំងពីរ
                      </p>
                      <v-row>

                        <v-col cols="12" sm="6">
                          <label class="mb-2">{{ fullFingersPrintFileInput[1].label }}<span style="color: red" v-if="fullFingersPrintFileInput[1].required"> *</span></label>
                          <img 
                            v-if="isEditingMode && fullFingersPrintFileInput[1].value && typeof fullFingersPrintFileInput[1].value === 'string'" 
                            :src="fullFingersPrintFileInput[1].value" 
                            alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;"
                          >
                          <v-file-input 
                            accept=".jpg,.png,.pdf,.jpeg"
                            :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                          ></v-file-input>
                        </v-col>

                        <v-col cols="12" sm="6">
                          <label class="mb-2">{{ fullFingersPrintFileInput[2].label }}<span style="color: red" v-if="fullFingersPrintFileInput[2].required"> *</span></label>
                          <img v-if="isEditingMode && fullFingersPrintFileInput[2].value && typeof fullFingersPrintFileInput[2].value === 'string'" :src="fullFingersPrintFileInput[2].value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                          <v-file-input 
                            v-model="fullFingersPrintFileInput[2].value" 
                            accept=".jpg,.png,.pdf,.jpeg"
                            :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                            @change="handleFileChange($event, field)"
                          ></v-file-input>
                        </v-col>

                      </v-row>
                    </v-col>

                    <v-col cols="12" sm="4">
                      <label class="mb-2">{{ fullFingersPrintFileInput[3].label }}<span style="color: red" v-if="fullFingersPrintFileInput[3].required"> *</span></label>
                      <img 
                        v-if="isEditingMode && fullFingersPrintFileInput[3].value && typeof fullFingersPrintFileInput[3].value === 'string'" :src="fullFingersPrintFileInput[3].value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;"
                      >
                      <v-file-input 
                        v-model="fullFingersPrintFileInput[3].value" 
                        accept=".jpg,.png,.pdf,.jpeg"
                        :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>


                    <!-- Full body pictures -->
                    <template v-for="field in fullBodyPhotoFileInput">
                      <v-col cols="12" sm="4">
                        <label class="mb-2">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                        <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="full profile" style="width: 100px; height: 100px;">
                        <v-file-input 
                          v-model="field.value" 
                          accept=".jpg,.png,.pdf,.jpeg"
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
                        <label class="mb-2">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                          <v-text-field
                            v-model="field.value"
                            :type="field.type"
                            :rules="[
                              ...(field.required !== false ? [requiredRule] : []),
                              noSpecialCharactersRule
                            ]"
                          ></v-text-field>
                      </template>
                    </v-col>


                    <!-- officers in charge -->
                    <v-col cols="12" sm="6">
                      <template v-for="field in personalInfoInputFields.slice(25, 30)" >
                        <label class="mb-2">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                        <v-text-field
                          v-model="field.value"
                          :rules="[
                            ...(field.required !== false ? [requiredRule] : []),
                            noSpecialCharactersRule
                          ]"
                        ></v-text-field>
                      </template>
                    </v-col>

                    <v-col cols="12" sm="6" v-for="field in palmPrintFileInput">
                      <label class="mb-2">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                      <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="officers" style="width: 100px; height: 100px;">
                      <v-file-input 
                        v-model="field.value" 
                        accept=".jpg,.png,.pdf,.jpeg"
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
                    <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="special mark" style="width: 100px; height: 100px;">
                      <label class="mb-2">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                      <v-file-input 
                        v-model="field.value" 
                        accept=".jpg,.png,.pdf,.jpeg"
                        :rules="field.required !== false ? [v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល'] : []"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>

                    <v-divider></v-divider>
                    <div style="height: 50px;"></div>

                    <v-col
                      v-for="field in formTemplates"
                      :key="field.key"
                      cols="12"
                      sm="4"
                      class="special-mark-item"
                    >
                    <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="special mark" style="width: 100px; height: 100px;">
                      <label class="mb-2 block">{{ field.label }}<span style="color: red" v-if="field.required"> *</span></label>
                      <v-file-input 
                        v-model="field.value" 
                        accept=".jpg,.png,.pdf,.jpeg"
                        :rules="field.required !== false ? [v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល'] : []"
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
                  @click="saveAction"
                  :disabled="!formSubmitable"
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
              {{ item.last_name + ' ' + item.first_name }}
            </template>

            <template v-else-if="header.key === 'id'">
              {{ (currentParams.page - 1) * itemsPerPage + index + 1 }}
            </template>

            <template v-else-if="['created_by', 'updated_by'].includes(header.key)">
              <div v-if="item [ header.key ].name && item [ header.key ].role">
              {{ item [ header.key ].name }} ({{ item [ header.key ].role }})
              </div>
              <p v-else>គ្មាន</p>
            </template>
            
            <template v-else>
              {{ item [ header.key ] ?? 'N/A' }}
            </template>
            </td>

          <td>
            <v-icon small color="blue" @click.stop="editItem(item)" v-if="userRole !== 'user'">mdi-pencil</v-icon>
            <v-icon small color="red" @click.stop="deleteItem(item)" v-if="userRole !== 'user'">mdi-delete</v-icon>
          </td>

        </tr>
      </template>
  
    </v-data-table-server>

    <!-- Error Dialog -->
    <v-dialog v-model="errorDialog" max-width="400px">
      <v-card style="background-color: red; color: white; font-weight: bold;">
      <v-card-title class="headline">Error</v-card-title>
      <v-card-text>{{ errorMessage }}</v-card-text>
      </v-card>
    </v-dialog>

    <!-- Success Dialog -->
    <v-dialog v-model="successDialog" max-width="400px">
      <v-card style="background-color: green; color: white; font-weight: bold;">
      <v-card-title class="headline">ជោគជ័យ</v-card-title>
      <v-card-text>{{ successMessage }}</v-card-text>
      </v-card>
    </v-dialog>

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
    getProvinces as getProvincesAPI,  
    getDistricts as getDistrictsAPI,
    getCommunes as getCommunesAPI
  } from '@/_api/address'

  import { 
    getAllRecords, 
    createRecord as createRecordAPI,
    getRecord as getRecordAPI,
    updateRecord as updateRecordAPI,
    deleteRecord as deleteRecordAPI
  } from '@/_api/document'
  import { getAllUsers as getAllUsersAPI } from '@/_api/user'
  import { useUserStore } from '@/stores/user'
  import { useTableStateStore } from '@/stores/tableState'
  import { useRouteStore } from '@/stores/route'

  export default {
    data: () => ({

      requiredRule: v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល',
      noSpecialCharactersRule: v => /^[a-zA-Z0-9._ \u1780-\u17FF]*$/.test(v) || 'អក្សរពិសេសមិនត្រូវបានអនុញ្ញាត',

      errorDialog: false,
      errorMessage: 'មានបញ្ហាបច្ចេកទេសកើតឡើង',
      successDialog: false,
      successMessage: 'ប្រតិបត្តិការជោគជ័យ',

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
      currentParams: {
        page: 1,
        filters: {},
        keySearch: {}
      },

      editingItem: [],
      selectedRecord: {},

      isEditingMode: false,

      itemsPerPage: 10, //
      search: '',
      fetchedData: [],
      loading: true,
      displaySpinner: false,
      totalItems: 20,//

      dialog: false,
      dialogDelete: false,
      headers: [
          { title: 'លេខរៀង', key: 'id', sortable: false },
          { title: 'លេខសៀវភៅ', key: 'book_id', sortable: true },
          { title: 'នាមគោត្តនាម', align: 'start', key: 'full_name', sortable: true },
          { title: 'អាសយដ្ឋាន', key: 'current_address', sortable: false },
          { title: 'បង្កេីតដោយ', key: 'created_by', sortable: false },
          { title: 'កែដោយ', key: 'updated_by', sortable: false },
          // { title: '', key: 'actions', sortable: false }, 
        ],
      formName: 'សលាកប័ត្រឯកកត្តជន',
      inputFields: [
        { key: "number",        label: "លេខ",       type: "text",            value: "", required: false },
        { key: "identityPhoto", label: "រូបថត ៤x៦",   name: "identity_photo", type: "file",  value: "", required: false },
        { key: "book_id",       label: "លេខសៀវភៅ",  name: "book_id",        type: "text",  value: "", required: false },
        { key: "madeAt",        label: "ធ្វេីនៅ",       type: "text",           value: "", required: false }
      ],

      personalInfoInputFields: [
        { key: "formula",                     label: "រូបមន្ត",              type: "text",   value: "", col: 6,  offset: 6, required: false },
        { key: "last_name",                   label: "គោត្តនាម",            type: "text",   value: "", col: 4 , required: true },
        { key: "first_name",                  label: "នាម",               type: "text",   value: "", col: 4 , required: true },
        { key: "nickname",                    label: "ឈ្មោះហៅក្រៅ",        type: "text",   value: "", col: 4, required: false},
        { key: "dob",                         label: "ថ្ងៃខែឆ្នាំកំណេីត",        type: "date",   value: "", col: 12 , required: true },
        { key: "pob_province",                label: "ខេត្ត/ក្រុងកំណេីត",       type: "select", value: "", col: 4, options: [], required: true },
        { key: "pob_district",                label: "ស្រុក/ខណ្ឌកំណេីត",      type: "select", value: "", col: 4, options: [], required: true },
        { key: "pob_commune",                 label: "ភូមិ/សង្កាត់កំណេីត",      type: "select", value: "", col: 4, options: [], required: true },
        { key: "ethnicity",                   label: "ជនជាតិ",              type: "text",   value: "", col: 4, required: false },
        { key: "nationality",                 label: "សញ្ជាតិ",              type: "text",   value: "", col: 4, required: false },
        { key: "religion",                    label: "សាសនា",             type: "text",   value: "", col: 4, required: false },
        { key: "previous_occupation",         label: "មុខរបរធ្លាប់ធ្វេីពីមុន",       type: "text",   value: "", col: 6, required: false },
        { key: "occupation",                  label: "មុខរបរបច្ចុប្បន្ន",          type: "text",   value: "", col: 6, required: false },
        { key: "current_address",             label: "អាស័យដ្ឋានបច្ចុប្បន្ន",       type: "text",   value: "", col: 12, required: false },
        { key: "province",                    label: "ខេត្ត/ក្រុង",            type: "select", value: "",  col: 4, options: [], required: true },
        { key: "district",                    label: "ស្រុក/ខណ្ឌ",           type: "select", value: "",  col: 4, options: [], required: true },
        { key: "commune",                     label: "ភូមិ/សង្កាត់",           type: "select", value: "",  col: 4, options: [], required: true },
        { key: "identity",                    label: "ភិនភាគ",              type: "text",   value: "", col: 10, required: false },
        { key: "height",                      label: "កម្ពស់ (ម៉ែត្រ)",        type: "number", value: "", col: 2, required: false },
        { key: "spouse",                      label: "ប្តី ឬ ប្រពន្ធ (បេីមាន)",     type: "text",   value: "", col: 6, required: false },
        { key: "spouse_address",              label: "នៅ (បេីមាន)",          type: "text",   value: "", col: 6, required: false },
        { key: "father_name",                 label: "ឪពុកឈ្មោះ",           type: "text",   value: "", col: 12, required: false },
        { key: "father_address",              label: "នៅ",                type: "text",   value: "", col: 12, required: false },
        { key: "mother_name",                 label: "ម្តាយឈ្មោះ",           type: "text",   value: "", col: 12, required: false },
        { key: "mother_address",              label: "នៅ",                type: "text",   value: "", col: 12, required: false },
        { key: "private_certificate_officer", label: "មន្ត្រីធ្វេីឯកសារ",         type: "text",   value: "", col: 12, required: false },
        { key: "supervision_officer",         label: "មន្ត្រីបែងចែកត្រួតពិនិត្យ",   type: "text",   value: "", col: 12, required: false },
        { key: "scheduling_research_officer", label: "មន្ត្រីស្រាវជ្រាវ រៀបតារាង", type: "text",   value: "", col: 12, required: false }
      ],

      fingerPrintsFileInput: [
        { key: "leftThumbPrint",  label : "មេដៃឆ្វេង",      type  : "file", value: "", name: "left_thumb_print", required: false },
        { key: "leftIndexPrint",  label : "ចង្អុលដៃឆ្វេង",    type: "file",   value: "", name: "left_index_print", required: false },
        { key: "leftMiddlePrint", label : "ដៃកណ្តាលឆ្វេង",  type  : "file", value: "", name: "left_middle_print", required: false },
        { key: "leftRingPrint",   label : "នាងដៃឆ្វេង",     type  : "file", value: "", name: "left_ring_print", required: false },
        { key: "leftPinkyPrint",  label : "កូនដៃឆ្វេង",      type : "file", value: "", name: "left_pinky_print", required: false },
        { key: "rightThumbPrint",  label: "មេដៃស្តាំ",      type  : "file", value: "", name: "right_thumb_print", required: false },
        { key: "rightIndexPrint",  label: "ចង្អុលដៃស្តាំ",    type: "file",   value: "", name: "right_index_print", required: false },
        { key: "rightMiddlePrint", label: "ដៃកណ្តាលស្តាំ",  type  : "file", value: "", name: "right_middle_print", required: false },
        { key: "rightRingPrint",   label: "នាងដៃស្តាំ",     type  : "file", value: "", name: "right_ring_print", required: false },
        { key: "rightPinkyPrint",  label: "កូនដៃស្តាំ",     type : "file",   value: "", name: "right_pinky_print", required: false },
      ],

        fullFingersPrintFileInput: [
          { key: "fourLeftFingersPrint",  label: "ផ្តិតម្រាមដៃឆ្វេងទាំងបួន", type: "file", value: "", name: "four_left_fingers_print", required: false  },
          { key: "leftThumbPrint01",      label: "មេដៃឆ្វេង",         type: "file", value: "", name: "left_thumb_print01", required: false       },
          { key: "rightThumbPrint01",     label: "មេដៃស្តាំ",          type: "file", value: "", name: "right_thumb_print01", required: false      },
          { key: "fourRightFingersPrint", label: "ផ្តិតម្រាមដៃស្តាំទាំងបួន",  type: "file", value: "", name: "four_right_fingers_print", required: false },
        ],

        fullBodyPhotoFileInput: [
          { key: "frontBodyPhoto",    label: "រូបមួយជំហរ",   type: "file", value: "", name: "front_body_photo", required: false },
          { key: "rightProfilePhoto", label: "រូបចំហៀងស្តាំ",  type: "file", value: "", name: "right_profile_photo", required: false },
          { key: "leftProfilePhoto",  label: "រូបចំហៀងឆ្វេង",  type: "file", value: "", name: "left_profile_photo", required: false },
        ],

        palmPrintFileInput: [
          { key: "leftPalmPrint",   label: "បាតដៃឆ្វេង",  type: "file", value: "", name: "left_palm_print", required: false },
          { key: "rightPalmPrint",  label: "បាតដៃស្តាំ",  type: "file", value: "", name: "right_palm_print", required: false },
        ],

        specialMark: [
          { key: "specialMark1", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "", name: "special_mark1", required: false },
          { key: "specialMark2", label: "ស្លាកសញ្ញាពិសេស (បេីមាន)", type: "file", value: "", name: "special_mark2", required: false },
          { key: "specialMark3", label: "ស្លាកសញ្ញាពិសេស (បេីមាន)", type: "file", value: "", name: "special_mark3", required: false },
      ],

      formTemplates: [
        { key: "formTemplate", label: 'រូបភាពឯកសារ (1)', type: 'file', value: "", name: "form_template", required: true },
        { key: "formTemplate1", label: 'រូបភាពឯកសារ (2)', type: 'file', value: "", name: "form_template1", required: false },
        { key: "formTemplate2", label: 'រូបភាពឯកសារ (3)', type: 'file', value: "", name: "form_template2", required: false }
      ],

    }),

    computed: {
      formSubmitable() {
        const isFieldValid = (field) => field?.required === false || field.value;
        
        return [
          ...this.inputFields,
          ...this.personalInfoInputFields,
          ...this.fingerPrintsFileInput,
          ...this.fullFingersPrintFileInput,
          ...this.fullBodyPhotoFileInput,
          ...this.palmPrintFileInput,
          ...this.specialMark,
          ...this.formTemplates
        ].every(isFieldValid);
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

      userRole () {
        return useUserStore().user.role
      }
    },

    watch: {
      dialog (val) {
        val || this.close()
        if (!val) {
          this.resetFieldValue()
        }
      },
      dialogDelete (val) {
        val || this.closeDelete()
      },
      startDate(val) {
        if (this.startDate && this.endDate) {
          this.loadItems({
            others: {
              between: 'created_at',
              min: this.startDate,
              max: this.endDate
            }
          })
        }
      },
      endDate(val) {
        if (this.startDate && this.endDate) {
          this.loadItems({
            others: {
              between: 'created_at',
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
    },

    methods: {

        // check file extension 
      isImage(url) {
        return /\.(jpg|jpeg|png|bmp|tiff)$/i.test(url);
      },
      isPdf(url) {
        return /\.pdf$/i.test(url);
      },
      isGif(url) {
        return /\.gif$/i.test(url);
      },

      async showErrorMessage (message) {
        this.errorMessage = message
        this.errorDialog = true
        setTimeout(() => {
          this.errorDialog = false;
        }, 5000);
      },

      async showSuccessMessage () {
        this.successDialog = true
        setTimeout(() => {
          this.successDialog = false;
        }, 5000);
      },

      saveAction () {
        if (this.isEditingMode) {
          this.updateRecord()
        } else {
          this.createRecord()
        }
      },

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
          this.currentParams.filters = {};
          this.currentParams.others = {};
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
        
        return {
          items: data.items,
          meta: data.meta
        }
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
      
      async loadItems(params = {}) {
        
        this.loading = true;
        const previousRoute = useRouteStore().previous
        const state = useTableStateStore().$state.state

        if (previousRoute === '/Records/[id]') {
          this.currentParams = {
            ...this.currentParams, 
            page: state.page,
            itemsPerPage: state.itemsPerPage,
            keySearch: state.keySearch
          }
          useRouteStore().$patch({
            previous: this.$route.name
          })
        } 
        else {
        console.log("here??", this.currentParams);
        console.log("params---", params);
        
        
          this.currentParams = {
            ...this.currentParams,
            ...params,
            limit: this.itemsPerPage
          };
        }

        useTableStateStore().$patch({
          state: {
            page: this.currentParams.page,
            itemsPerPage: this.currentParams.itemsPerPage,
            keySearch: this.currentParams.keySearch,
            sortBy: this.currentParams.sortBy
          }
        });

        this.fetchRecordData(this.currentParams).then(({ items, meta }) => {
          this.fetchedData = items;
          this.totalItems = meta.total;
          this.itemsPerPage = meta.per_page;
          this.loading = false;
        }).catch(() => {
          this.loading = false;
        });
      },

      async editItem (item) {
        this.isEditingMode = true
        const { data } = await getRecordAPI(item.uuid)
        if (data) {
          const record = data?.data?.item;
          this.selectedRecord = record;

          let fieldsToUpdate = [
            ...this.inputFields,
            ...this.personalInfoInputFields,
            ...this.fingerPrintsFileInput,
            ...this.fullFingersPrintFileInput,
            ...this.fullBodyPhotoFileInput,
            ...this.palmPrintFileInput,
            ...this.specialMark,
            ...this.formTemplates
          ];

          fieldsToUpdate.forEach(field => {
            field.value = record[field.name || field.key] || '';
          });

          this.editingItem = fieldsToUpdate;
          
          this.dialog = true
        }
      },

      deleteItem (item) {
        this.selectedRecord = item
        this.dialogDelete = true
      },

      async deleteItemConfirm () {
        try {
          const result = await deleteRecordAPI(this.selectedRecord.id)
          
          this.loadItems()
          this.showSuccessMessage()

        } catch (error) {
          console.log(error);
          const errorMessage = error?.response?.data?.message;
          if (errorMessage) {
            this.showErrorMessage(errorMessage);
          }

        }
        this.closeDelete()
      },

      close () {
        this.dialog = false
      },

      closeDelete () {
        this.dialogDelete = false
      },

      viewRecord (item) {
        const uuid = item.uuid;
        this.$router.push("/records/" + uuid)
      },

      handleFileChange(event, key) {
        const file = event.target.files[0];
        if (file) {
          this[key] = [{ key, value: file, type: 'file' }];
        }
      },

      async updateRecord () {
        let fieldsToUpdate = [
            ...this.inputFields,
            ...this.personalInfoInputFields,
            ...this.fingerPrintsFileInput,
            ...this.fullFingersPrintFileInput,
            ...this.fullBodyPhotoFileInput,
            ...this.palmPrintFileInput,
            ...this.specialMark,
            ...this.formTemplates
          ];

        try {

          this.displaySpinner = true

          const formData = new FormData();
          fieldsToUpdate.forEach(item => {

            if (["commune", "district", "province", "pob_commune", "pob_district", "pob_province"].includes(item.key) && typeof item.value === 'object') {
              formData.append(item.key, item.value.id);
            } else {
              formData.append(item.key, item.value);
            }

          });
          

          const result = await updateRecordAPI(this.selectedRecord.id, formData)
          
          // formData.forEach((value, key) => {
          //   console.log(`${key}:`, value);
          // });
          
          await this.loadItems()
          this.showSuccessMessage()
          this.dialog = false
          
        } catch (error) {
          console.log(error);
          const errorMessage = error?.response?.data?.message;
          if (errorMessage) {
            this.showErrorMessage(errorMessage);
          }
        }

        this.displaySpinner = false
        
        
      },

      async createRecord () {

        this.displaySpinner = true

        let formData = new FormData();

        const createObject = [
          ...this.inputFields, 
          ...this.personalInfoInputFields, 
          ...this.fingerPrintsFileInput, 
          ...this.fullFingersPrintFileInput, 
          ...this.fullBodyPhotoFileInput, 
          ...this.palmPrintFileInput, 
          ...this.specialMark,
          ...this.formTemplates
        ]

        createObject.forEach(item => {
          formData.append(item.key, item.value);
        });


        console.log("createObject", createObject);
        console.log("formData", formData);

        try {

          const result = await createRecordAPI(formData, createObject)
          if (result.data.code === "200") {
            this.dialog = false
          }
          
          await this.loadItems()
          this.showSuccessMessage()

          this.dialog = false
          this.resetFieldValue()
          
        } catch (error) {
          console.log(error);
          const errorMessage = error?.response?.data?.message;
          if (errorMessage) {
            this.showErrorMessage(errorMessage);
          }
        }

        this.displaySpinner = false
      },


      resetFieldValue () {

        const resetFields = (fields) => {
          fields.forEach(field => field.value = "");
        };

        const allFields = [
          ...this.inputFields,
          ...this.personalInfoInputFields,
          ...this.fingerPrintsFileInput,
          ...this.fullFingersPrintFileInput,
          ...this.fullBodyPhotoFileInput,
          ...this.palmPrintFileInput,
          ...this.specialMark,
          ...this.formTemplates
        ];

        resetFields(allFields);
      }
    },

    unmounted() {
      useRouteStore().$reset()
    }
  }
</script>


<style scoped src="../../styles/table.scss"></style>
<style scoped src="../../styles/records.scss"></style>
<style scoped src="../../styles/form.scss"></style>