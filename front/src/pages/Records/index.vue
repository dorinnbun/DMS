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

                        <template v-else>
                          <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: auto;">
                          <v-file-input
                            v-model="field.value"
                            :label="field.label" 
                            :rules="[v => !!v || 'ទិន្នន័យត្រូវបញ្ចូល']"
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
                        <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                        <v-file-input 
                          v-model="field.value" 
                          :label="field.label" 
                          accept=".jpg,.png,.pdf,.jpeg"
                          :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                          @change="handleFileChange($event, field)"
                        ></v-file-input>
                      </v-col>


                    <!-- Full fingers prints file input -->
                    <!-- to follow the original template ==> use statically -->
                    <v-col cols="12" sm="4">
                      <img v-if="isEditingMode && fullFingersPrintFileInput[0].value && typeof fullFingersPrintFileInput[0].value === 'string'" :src="fullFingersPrintFileInput[0].value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                      <v-file-input 
                        v-model="fullFingersPrintFileInput[0].value" 
                        :label="fullFingersPrintFileInput[0].label" 
                        accept=".jpg,.png,.pdf,.jpeg"
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
                          <img 
                            v-if="isEditingMode && fullFingersPrintFileInput[1].value && typeof fullFingersPrintFileInput[1].value === 'string'" 
                            :src="fullFingersPrintFileInput[1].value" 
                            alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;"
                          >
                          <v-file-input 
                            v-model="fullFingersPrintFileInput[1].value" 
                            :label="fullFingersPrintFileInput[1].label" 
                            accept=".jpg,.png,.pdf,.jpeg"
                            :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                          ></v-file-input>
                        </v-col>

                        <v-col cols="12" sm="6">
                          <img v-if="isEditingMode && fullFingersPrintFileInput[2].value && typeof fullFingersPrintFileInput[2].value === 'string'" :src="fullFingersPrintFileInput[2].value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                          <v-file-input 
                            v-model="fullFingersPrintFileInput[2].value" 
                            :label="fullFingersPrintFileInput[2].label" 
                            accept=".jpg,.png,.pdf,.jpeg"
                            :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                            @change="handleFileChange($event, field)"
                          ></v-file-input>
                        </v-col>

                      </v-row>
                    </v-col>

                    <v-col cols="12" sm="4">
                      <img 
                        v-if="isEditingMode && fullFingersPrintFileInput[3].value && typeof fullFingersPrintFileInput[3].value === 'string'" :src="fullFingersPrintFileInput[3].value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;"
                      >
                      <v-file-input 
                        v-model="fullFingersPrintFileInput[3].value" 
                        :label="fullFingersPrintFileInput[3].label" 
                        accept=".jpg,.png,.pdf,.jpeg"
                        :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>


                    <!-- Full body pictures -->
                    <template v-for="field in fullBodyPhotoFileInput">
                      <v-col cols="12" sm="4">
                        <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                        <v-file-input 
                          v-model="field.value" 
                          :label="field.label" 
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
                      <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                      <v-file-input 
                        v-model="field.value" 
                        :label="field.label" 
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
                    <img v-if="isEditingMode && field.value && typeof field.value === 'string'" :src="field.value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                      <v-file-input 
                        v-model="field.value" 
                        :label="field.label" 
                        accept=".jpg,.png,.pdf,.jpeg"
                        :rules="[v => !!v || 'ឯកសារត្រូវបញ្ចូល']"
                        @change="handleFileChange($event, field)"
                      ></v-file-input>
                    </v-col>


                    <v-col
                      :key="formTemplate.key"
                      cols="12"
                      sm="3"
                      class="special-mark-item"
                    >
                      <img v-if="isEditingMode && formTemplate.value && typeof formTemplate.value === 'string'" :src="formTemplate.value" alt="ស្នាមម្រាមដៃ" style="width: 100px; height: 100px;">
                      <v-file-input 
                        v-model="formTemplate.value" 
                        :label="formTemplate.label" 
                        accept=".jpg,.png,.pdf,.jpeg"
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
            
            <template v-else>
              {{ item [header.key] }}
            </template>
          </td>

          <td>
            <v-icon small color="blue" @click.stop="editItem(item)" v-if="userRole !== 'user'">mdi-pencil</v-icon>
            <v-icon small color="red" @click.stop="deleteItem(item)">mdi-delete</v-icon>
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

  export default {
    data: () => ({

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
          { title: 'លេខរៀង', key: 'id', sortable: true },
          { title: 'លេខសៀវភៅ', key: 'book_id', sortable: true },
          { title: 'នាមគោត្តនាម', align: 'start', key: 'full_name', sortable: true },
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
        { key: "number",        label: "លេខ",       type: "text",            value: "" },
        { key: "identityPhoto", label: "រូបថត ៤x៦",   name: "identity_photo", type: "file",  value: "" },
        { key: "book_id",       label: "លេខសៀវភៅ",  name: "book_id",        type: "text",  value: "" },
        { key: "madeAt",        label: "ធ្វេីនៅ",       type: "text",           value: "" }
      ],

      personalInfoInputFields: [
        { key: "formula",                     label: "រូបមន្ត",              type: "text",   value: "", col: 6,  offset: 6 },
        { key: "last_name",                   label: "គោត្តនាម",            type: "text",   value: "", col: 4 },
        { key: "first_name",                  label: "នាម",               type: "text",   value: "", col: 4 },
        { key: "nickname",                    label: "ឈ្មោះហៅក្រៅ",        type: "text",   value: "", col: 4 },
        { key: "dob",                         label: "ថ្ងៃខែឆ្នាំកំណេីត",        type: "date",   value: "", col: 12 },
        { key: "pob_province",                label: "ខេត្ត/ក្រុងកំណេីត",       type: "select", value: "", col: 4, options: [] },
        { key: "pob_district",                label: "ស្រុក/ខណ្ឌកំណេីត",      type: "select", value: "", col: 4, options: [] },
        { key: "pob_commune",                 label: "ភូមិ/សង្កាត់កំណេីត",      type: "select", value: "", col: 4, options: [] },
        { key: "ethnicity",                   label: "ជនជាតិ",              type: "text",   value: "", col: 4 },
        { key: "nationality",                 label: "សញ្ជាតិ",              type: "text",   value: "", col: 4 },
        { key: "religion",                    label: "សាសនា",             type: "text",   value: "", col: 4 },
        { key: "previous_occupation",         label: "មុខរបរធ្លាប់ធ្វេីពីមុន",       type: "text",   value: "", col: 6 },
        { key: "occupation",                  label: "មុខរបរបច្ចុប្បន្ន",          type: "text",   value: "", col: 6 },
        { key: "current_address",             label: "អាស័យដ្ឋានបច្ចុប្បន្ន",       type: "text",   value: "", col: 12 },
        { key: "province",                    label: "ខេត្ត/ក្រុង",            type: "select", value: "",  col: 4, options: [] },
        { key: "district",                    label: "ស្រុក/ខណ្ឌ",           type: "select", value: "",  col: 4, options: [] },
        { key: "commune",                     label: "ភូមិ/សង្កាត់",           type: "select", value: "",  col: 4, options: []},
        { key: "identity",                    label: "ភិនភាគ",              type: "text",   value: "", col: 10 },
        { key: "height",                      label: "កម្ពស់ (ម៉ែត្រ)",        type: "number", value: "", col: 2 },
        { key: "spouse",                      label: "ប្តី ឬ ប្រពន្ធ",          type: "text",   value: "", col: 6 },
        { key: "spouse_address",              label: "នៅ",                type: "text",   value: "", col: 6 },
        { key: "father_name",                 label: "ឪពុកឈ្មោះ",           type: "text",   value: "", col: 12 },
        { key: "father_address",              label: "នៅ",                type: "text",   value: "", col: 12 },
        { key: "mother_name",                 label: "ម្តាយឈ្មោះ",           type: "text",   value: "", col: 12 },
        { key: "mother_address",              label: "នៅ",                type: "text",   value: "", col: 12 },
        { key: "private_certificate_officer", label: "មន្ត្រីធ្វេីឯកសារ",         type: "text",   value: "", col: 12 },
        { key: "supervision_officer",         label: "មន្ត្រីបែងចែកត្រួតពិនិត្យ",   type: "text",   value: "", col: 12 },
        { key: "scheduling_research_officer", label: "មន្ត្រីស្រាវជ្រាវ រៀបតារាង", type: "text",   value: "", col: 12 }
      ],

        fingerPrintsFileInput: [
          { key: "leftThumbPrint",  label : "មេដៃឆ្វេង",      type  : "file", value: "", name: "left_thumb_print" },
          { key: "leftIndexPrint",  label : "ចង្អុលដៃឆ្វេង",    type: "file",   value: "", name: "left_index_print" },
          { key: "leftMiddlePrint", label : "ដៃកណ្តាលឆ្វេង",  type  : "file", value: "", name: "left_middle_print" },
          { key: "leftRingPrint",   label : "នាងដៃឆ្វេង",     type  : "file", value: "", name: "left_ring_print" },
          { key: "leftPinkyPrint",  label : "កូនដៃឆ្វេង",      type : "file", value: "", name: "left_pinky_print" },
          
          { key: "rightThumbPrint",  label: "មេដៃស្តាំ",      type  : "file", value: "", name: "right_thumb_print" },
          { key: "rightIndexPrint",  label: "ចង្អុលដៃស្តាំ",    type: "file",   value: "", name: "right_index_print" },
          { key: "rightMiddlePrint", label: "ដៃកណ្តាលស្តាំ",  type  : "file", value: "", name: "right_middle_print" },
          { key: "rightRingPrint",   label: "នាងដៃស្តាំ",     type  : "file", value: "", name: "right_ring_print" },
          { key: "rightPinkyPrint",  label: "កូនដៃស្តាំ",     type : "file",   value: "", name: "right_pinky_print" },
        ],

          fullFingersPrintFileInput: [
            { key: "fourLeftFingersPrint",  label: "ផ្តិតម្រាមដៃឆ្វេងទាំងបួន", type: "file", value: "", name: "four_left_fingers_print"  },
            { key: "leftThumbPrint01",      label: "មេដៃឆ្វេង",         type: "file", value: "", name: "left_thumb_print01"       },
            { key: "rightThumbPrint01",     label: "មេដៃស្តាំ",          type: "file", value: "", name: "right_thumb_print01"      },
            { key: "fourRightFingersPrint", label: "ផ្តិតម្រាមដៃស្តាំទាំងបួន",  type: "file", value: "", name: "four_right_fingers_print" },
          ],

          fullBodyPhotoFileInput: [
            { key: "frontBodyPhoto",    label: "រូបមួយជំហរ",   type: "file", value: "", name: "front_body_photo" },
            { key: "rightProfilePhoto", label: "រូបចំហៀងស្តាំ",  type: "file", value: "", name: "right_profile_photo" },
            { key: "leftProfilePhoto",  label: "រូបចំហៀងឆ្វេង",  type: "file", value: "", name: "left_profile_photo" },
          ],

          palmPrintFileInput: [
            { key: "leftPalmPrint",   label: "បាតដៃឆ្វេង",  type: "file", value: "", name: "left_palm_print" },
            { key: "rightPalmPrint",  label: "បាតដៃស្តាំ",  type: "file", value: "", name: "right_palm_print" },
          ],

          specialMark: [
            { key: "specialMark1", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "", name: "special_mark1" },
            { key: "specialMark2", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "", name: "special_mark2" },
            { key: "specialMark3", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "", name: "special_mark3" },
        ],

        formTemplate: { key: "formTemplate", label: 'រូបភាពឯកសារ (បេីមាន)', type: 'file', value: "", name: "form_template" },

    }),

    computed: {
      formTitle () {
        return this.editedIndex === -1 ? 'ពត័មានអ្នកប្រេីប្រាស់ថ្មី' : 'កែពត័មានអ្នកប្រេីប្រាស់'
      },

      formSubmitable() {
        return this.inputFields.every(item => item.value) 
          && this.personalInfoInputFields.every(item => item.value) 
          && this.fingerPrintsFileInput.every(item => item.value) 
          && this.fullFingersPrintFileInput.every(item => item.value)
          && this.fullBodyPhotoFileInput.every(item => item.value)
          && this.palmPrintFileInput.every(item => item.value)
          && this.specialMark.every(item => item.value)
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

        let { sortBy } = params

        if (sortBy.length > 0) {
          sortBy.map (sort => {
            if (sort.key === 'full_name') {
              // sort.key = 'first_name'
              // sort.order = sort.order
              sortBy.push({
                key: 'first_name',
                order: sort.order
              })
            }
            
          })
        }

        // console.log(params.sortBy);
        
        
        const { data: { data } } = await getAllRecords(params)
        // return data
        
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
        
        this.currentParams = {
          ...this.currentParams,
          ...params,
          limit: this.itemsPerPage
        };
        
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


          // console.log("record", record);

          // const fieldsToUpdate = [
          //   ...this.inputFields.map(field => ({ ...field })),
          //   ...this.personalInfoInputFields.map(field => ({ ...field })),
          //   ...this.fingerPrintsFileInput.map(field => ({ ...field })),
          //   ...this.fullFingersPrintFileInput.map(field => ({ ...field })),
          //   ...this.fullBodyPhotoFileInput.map(field => ({ ...field })),
          //   ...this.palmPrintFileInput.map(field => ({ ...field })),
          //   ...this.specialMark.map(field => ({ ...field })),
          // ];

          let fieldsToUpdate = [
            ...this.inputFields,
            ...this.personalInfoInputFields,
            ...this.fingerPrintsFileInput,
            ...this.fullFingersPrintFileInput,
            ...this.fullBodyPhotoFileInput,
            ...this.palmPrintFileInput,
            ...this.specialMark,
          ];

          fieldsToUpdate.forEach(field => {
            field.value = record[field.name || field.key] || '';
          });

          this.editingItem = fieldsToUpdate;
          // console.log("this.inputfields===", this.inputFields);
          
          
          // console.log("fieldsToUpdate", fieldsToUpdate);
          
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
          // console.log("result after delete", result);
          
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
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem) //
          this.editedIndex = -1
        })
      },

      closeDelete () {
        this.dialogDelete = false
        this.$nextTick(() => {
          this.editedItem = Object.assign({}, this.defaultItem) //
          this.editedIndex = -1
        })
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
        console.log("this.editingItem==========", this.editingItem);
        let fieldsToUpdate = [
            ...this.inputFields,
            ...this.personalInfoInputFields,
            ...this.fingerPrintsFileInput,
            ...this.fullFingersPrintFileInput,
            ...this.fullBodyPhotoFileInput,
            ...this.palmPrintFileInput,
            ...this.specialMark,
            this.formTemplate
          ];
          

        console.log("fieldsToUpdate==========", fieldsToUpdate);

        try {

          this.displaySpinner = true

          const formData = new FormData();
          fieldsToUpdate.forEach(item => {

            if (["commune", "district", "province", "pob_commune", "pob_district", "pob_province"].includes(item.key) && typeof item.value === 'object') {
              formData.append(item.key, item.value.id);
            } else {
              formData.append(item.key, item.value);
            }

            // for (var pair of formData.entries()) {
            //     console.log("===", pair[0]+ ', ' + pair[1]); 
            // }
          });
          

          const result = await updateRecordAPI(this.selectedRecord.id, formData)
          console.log("result====", result);
          
          formData.forEach((value, key) => {
            console.log(`${key}:`, value);
          });
          
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
          console.log("result====", result.data);
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
          fields.forEach(field => { field.value = "" })
        }
        resetFields(this.inputFields)
        resetFields(this.personalInfoInputFields)
        resetFields(this.fingerPrintsFileInput)
        resetFields(this.fullFingersPrintFileInput)
        resetFields(this.fullBodyPhotoFileInput)
        resetFields(this.palmPrintFileInput)
        resetFields(this.specialMark)
        this.formTemplate.value = ""
      }
    },
  }
</script>


<style scoped src="../../styles/table.scss"></style>
<style scoped src="../../styles/records.scss"></style>
<style scoped src="../../styles/form.scss"></style>