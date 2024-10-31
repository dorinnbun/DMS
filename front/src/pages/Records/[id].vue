<script>
import { defineComponent } from 'vue'
import { useRoute } from 'vue-router'
import { getRecord as getRecordAPI } from '../../_api/document'
import { 
    getProvinces as getProvincesAPI,  
    getDistricts as getDistrictsAPI,
    getCommunes as getCommunesAPI
  } from '@/_api/address'

export default defineComponent({
  name: 'RecordDetail',
  data: () => ({
    openFormTemplatePreview: false,
    docId: null,
    record: {},
    formName: 'សលាកប័ត្រឯកកត្តជន',
    inputFields: [
        { key: "number",           label: "លេខ",        type: "text",  value: "" },
        { key: "identity_photo",   label: "រូបថត 4x6",    type: "file",  value: "" },
        { key: "book_id",          label: "លេខសៀវភៅ",   type: "text",  value: "" },
        { key: "madeAt",           label: "ធ្វេីនៅ",        type: "text",  value: "" }
      ],

    personalInfoInputFields: [
      { key: "formula",                     label: "រូបមន្ត",              type: "text",   col: 6,      value: "", offset: 6 },
      { key: "full_name",                   label: "គោត្តនាមនាម",         type: "text",   col: 6,      value:  "" },
      { key: "nickname",                    label: "ឈ្មោះហៅក្រៅ",        type: "text",   col: 6,      value:  "" },
      { key: "dob",                         label: "ថ្ងៃខែឆ្នាំកំណេីត",        type: "date",   col: 6,      value:  "" },
      { key: "pob",                         label: "ភូមិកំណេីត",           type: "text",   col: 6,      value:  "" },
      { key: "ethnicity",                   label: "ជនជាតិ",             type: "text",   col: 4,      value:  "" },
      { key: "nationality",                 label: "សញ្ជាតិ",             type: "text",   col: 4,      value:  "" },
      { key: "religion",                    label: "សាសនា",            type: "text",   col: 4,      value:  "" },
      { key: "previous_occupation",         label: "មុខរបរធ្លាប់ធ្វេីពីមុន",      type: "text",   col: 12,     value:  "" },
      { key: "occupation",                  label: "មុខរបរបច្ចុប្បន្ន",         type: "text",   col: 12,     value:  "" },
      { key: "current_address",             label: "អាស័យដ្ឋានបច្ចុប្បន្ន",      type: "text",   col: 12,     value:  "" },
      { key: "identity",                    label: "ភិនភាគ",             type: "text",   col: 8,      value:  "" },
      { key: "height",                      label: "កម្ពស់(ម៉ែត្រ)",         type: "text",   col: 4,      value: "" },
      { key: "spouse",                      label: "ប្តី ឬ ប្រពន្ធ",          type: "text",   col: 6,      value: "" },
      { key: "spouse_address",              label: "នៅ",               type: "text",   col: 6,      value:  "" },
      { key: "father_name",                 label: "ឪពុកឈ្មោះ",          type: "text",   col: 12,     value:  "" },
      { key: "father_address",              label: "នៅ",               type: "text",   col: 12,     value:  "" },
      { key: "mother_name",                 label: "ម្តាយឈ្មោះ",          type: "text",   col: 12,     value:  "" },
      { key: "mother_address",              label: "នៅ",               type: "text",   col: 12,     value:  "" },
      { key: "private_certificate_officer", label: "មន្ត្រីធ្វេីឯកសារ",        type: "text",   col: 12,     value:  "" },
      { key: "supervision_officer",         label: "មន្ត្រីបែងចែកត្រួតពិនិត្យ",  type: "text",   col: 12,      value:  "" },
      { key: "scheduling_research_officer", label: "មន្ត្រីស្រាវជ្រាវរៀបតារាង", type: "text",    col: 12,     value: "" }
    ],

    fingerPrintsFileInput: [
      { key: "right_thumb_print",   label: "មេដៃស្តាំ",     type: "file",  value: "" },
      { key: "right_index_print",   label: "ចង្អុលដៃស្តាំ",   type: "file", value: "" },
      { key: "right_middle_print",  label: "ដៃកណ្តាលស្តាំ", type: "file", value: "" },
      { key: "right_ring_print",    label: "នាងដៃស្តាំ",    type: "file", value: "" },
      { key: "right_pinky_print",   label: "កូនដៃស្តាំ",    type: "file", value: "" },
      { key: "left_thumb_print",    label: "មេដៃឆ្វេង",    type: "file", value: "" },
      { key: "left_index_print",    label: "ចង្អុលដៃឆ្វេង",  type: "file", value: "" },
      { key: "left_middle_print",   label: "ដៃកណ្តាលឆ្វេង", type: "file", value: "" },
      { key: "left_ring_print",     label: "នាងដៃឆ្វេង",    type: "file", value: "" },
      { key: "left_pinky_print",    label: "កូនដៃឆ្វេង",    type: "file", value: "" },
    ],

    fullBodyPhotoFileInput: [
      { key: "front_body_photo",    label: "រូបមួយជំហរ",    type: "file", value: "" },
      { key: "right_profile_photo", label: "រូបចំហៀងស្តាំ", type: "file", value: "" },
      { key: "left_profile_photo",  label: "រូបចំហៀងឆ្វេង", type: "file", value: "" },
    ],

    fullFingersPrintFileInput: [
      { key: "four_left_fingers_print",  label: "ផ្តិតម្រាមដៃឆ្វេងទាំងបួន", type: "file", value: "" },
      { key: "left_thumb_print01",       label: "មេដៃឆ្វេង",         type: "file", value: "" },
      { key: "right_thumb_print01",      label: "មេដៃស្តាំ",          type: "file", value: "" },
      { key: "four_right_fingers_print", label: "ផ្តិតម្រាមដៃស្តាំទាំងបួន",  type: "file", value: "" },
    ],

    palmPrintFileInput: [
      { key: "left_palm_print",   label: "បាតដៃឆ្វេង", type: "file", value: "" },
      { key: "right_palm_print",  label: "បាតដៃស្តាំ",  type: "file", value: "" },
    ],

    specialMark: [
      { key: "special_mark1", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "" },
      { key: "special_mark2", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "" },
      { key: "special_mark3", label: "ស្លាកសញ្ញាពិសេស", type: "file", value: "" },
    ]
  }),

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

    getFieldDisplayValue(field) {
      if (this.record) {
      switch (field.key) {
        case 'full_name': 
        return `${ this.record?.last_name } ${ this.record?.first_name }`;

        case 'pob':
        return `<strong>ខេត្ត/ក្រុង៖</strong>${ this.record?.pob_province?.name } <strong>ស្រុក/ខណ្ឌ៖</strong>${ this.record?.pob_district?.name } <strong>ភូមិ/សង្កាត់៖</strong>${ this.record?.pob_commune?.name }`;

        case 'current_address':
        return `${ this.record?.current_address } <strong>ខេត្ត/ក្រុង៖</strong>${ this.record?.province?.name } <strong>ស្រុក/ខណ្ឌ៖</strong>${ this.record?.district?.name } <strong>ភូមិ/សង្កាត់៖</strong>${ this.record?.commune?.name }`;
        
        default:
        return field.value;
      }
      }
    },

    async getRecord () {
      try {
        const result = await getRecordAPI(this.docId)
        const record = result?.data?.data?.item
        this.record = record

        if (record) {
          [
            this.inputFields,
            this.personalInfoInputFields, 
            this.fingerPrintsFileInput, 
            this.fullBodyPhotoFileInput, 
            this.fullFingersPrintFileInput, 
            this.palmPrintFileInput, 
            this.specialMark
          ].forEach(field => {
            field.forEach(f => {
              f.value = record?.[f.key] ?? f.value
            })
          })
        } else {
          this.$router.push("/records/")
        }

      } catch (error) {
        console.error('Error fetching record:', error)
      }
    },
  },

  async created() {
    const route = useRoute()
    this.docId = route.params.id
    if (this.docId) {
      this.getRecord()
    }
    window.scrollTo(0, 0);
  },
})
</script>

<template>

  <div class="form-container">
    <h1 class="form-view-title">{{ formName }}</h1>

    <v-container fluid>
      <v-row>

        <!-- Form Details -->
        <v-col cols="12" sm="3" md="2">
          <template v-for="field in inputFields" :key="field.key">
            <div class="field-container">
              <strong class="field-label">{{ field.label }}:</strong>
              <div v-if="field.type === 'file'">
                <img v-if="isImage(field.value)" :src="field.value" alt="file" class="field-image">
                <iframe v-else-if="isPdf(field.value)" :src="field.value" class="file-viewer-pdf"></iframe>
                <div v-else-if="isGif(field.value)">
                  <v-img :src="field.value" class="file-viewer-image"></v-img>
                </div>
              </div>
              <span v-else>{{ field.value }}</span>
            </div>
          </template>
        </v-col>

        <!-- Personal Information -->
        <v-col cols="12" sm="9" md="10">
          <v-row>
            <template v-for="field in personalInfoInputFields.slice(0, 15)" :key="field.key">
              <v-col cols="12" :sm="field.col" :offset-sm="field.offset || 0" style="padding: 0;">
                <div class="field-container">
                  <strong class="field-label">{{ field.label }}:</strong>
                  <span class="field-value" v-html="getFieldDisplayValue(field)"></span>
                </div>
              </v-col>
            </template>
          </v-row>
        </v-col>

        <!-- Fingerprint Files --> 
        <v-col
          v-for="field in fingerPrintsFileInput"
          :key="field.key"
          cols="12"
          sm="6"
          class="fingerprint-container column-5"
        >
          <div class="field-container center-align">
            <p><strong class="field-label">{{ field.label }}:</strong></p>
            <img v-if="isImage(field.value)" :src="field.value" alt="file" class="field-image">
            <iframe v-else-if="isPdf(field.value)" :src="field.value" class="file-viewer-pdf"></iframe>
            <div v-else-if="isGif(field.value)">
              <v-img :src="field.value" class="file-viewer-image"></v-img>
            </div>
          </div>
        </v-col>

        <!-- Full fingers prints file input -->
        <v-col cols="12" sm="4">
          <div class="field-container center-align">
            <p><strong class="field-label">{{ fullFingersPrintFileInput[0].label }}:</strong></p>
            <img v-if="isImage(fullFingersPrintFileInput[0].value)" :src="fullFingersPrintFileInput[0].value" alt="file" class="field-image">
            <iframe v-else-if="isPdf(fullFingersPrintFileInput[0].value)" :src="fullFingersPrintFileInput[0].value" class="file-viewer-pdf"></iframe>
            <div v-else-if="isGif(fullFingersPrintFileInput[0].value)">
              <v-img :src="fullFingersPrintFileInput[0].value" class="file-viewer-image"></v-img>
            </div>
          </div>
        </v-col>

        <!-- each thumbs -->
        <v-col cols="12" sm="4" style="border: 1px solid grey; border-top: 0;">
          <p style="text-align: center; margin-bottom: 12px;">
            ផ្តិតមេដៃទាំងពីរ
          </p>
          <v-row>

            <v-col cols="12" sm="6" style="border-top: 1px solid grey; border-right: 1px solid grey; margin: 0;">
              <div class="field-container center-align" style="margin: 0;">
                <p>
                  <strong class="field-label">{{ fullFingersPrintFileInput[1].label }}:</strong>
                </p>
                <img v-if="isImage(fullFingersPrintFileInput[1].value)" :src="fullFingersPrintFileInput[1].value" alt="file" class="field-image">
                <iframe v-else-if="isPdf(fullFingersPrintFileInput[1].value)" :src="fullFingersPrintFileInput[1].value" class="file-viewer-pdf"></iframe>
                <div v-else-if="isGif(fullFingersPrintFileInput[1].value)">
                  <v-img :src="fullFingersPrintFileInput[1].value" class="file-viewer-image"></v-img>
                </div>
              </div>
            </v-col>

            <v-col cols="12" sm="6" style="border-top: 1px solid grey">
              <div class="field-container center-align">
                <p>
                  <strong class="field-label">{{ fullFingersPrintFileInput[2].label }}:</strong>
                </p>
                <img v-if="isImage(fullFingersPrintFileInput[2].value)" :src="fullFingersPrintFileInput[2].value" alt="file" class="field-image">
                <iframe v-else-if="isPdf(fullFingersPrintFileInput[2].value)" :src="fullFingersPrintFileInput[2].value" class="file-viewer-pdf"></iframe>
                <div v-else-if="isGif(fullFingersPrintFileInput[2].value)">
                  <v-img :src="fullFingersPrintFileInput[2].value" class="file-viewer-image"></v-img>
                </div>
              </div>
            </v-col>

          </v-row>
        </v-col>

        <v-col cols="12" sm="4">
          <div class="field-container center-align">
            <p><strong class="field-label">{{ fullFingersPrintFileInput[3].label }}:</strong></p>
            <img v-if="isImage(fullFingersPrintFileInput[3].value)" :src="fullFingersPrintFileInput[3].value" alt="file" class="field-image">
            <iframe v-else-if="isPdf(fullFingersPrintFileInput[3].value)" :src="fullFingersPrintFileInput[3].value" class="file-viewer-pdf"></iframe>
            <div v-else-if="isGif(fullFingersPrintFileInput[3].value)">
              <v-img :src="fullFingersPrintFileInput[3].value" class="file-viewer-image"></v-img>
            </div>
          </div>
        </v-col>


        <!-- Full body photo file input -->
        <v-row justify="center" style="margin: 1px 0">
          <v-col
            v-for="field in fullBodyPhotoFileInput"
            :key="field.key"
            cols="12" sm="3"
            style="border: 1px solid lightgray;"
            class="full-body-image"
          >
            <div class="center-align">
              <strong>{{ field.label }}</strong>
              <div>
                <img v-if="isImage(field.value)" :src="field.value" alt="file" class="full-body-image">
                <iframe v-else-if="isPdf(field.value)" :src="field.value" class="file-viewer-pdf"></iframe>
                <div v-else-if="isGif">
                  <v-img :src="field.value" class="file-viewer-image"></v-img>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>

        <!-- Divider -->
        <v-divider></v-divider>

        <!-- Parents Info and Palm Print -->
        <v-col cols="12" sm="5">
          <div v-for="field in personalInfoInputFields.slice(15, 19)" :key="field.key" class="field-container">
            <strong class="field-label">{{ field.label }}:</strong>
            <span class="field-value">{{ field.value }}</span>
          </div>
          <div class="palm-print-container">
            <strong>{{ palmPrintFileInput[0].label }}</strong>
            <div>
              <img v-if="isImage(palmPrintFileInput[0].value)" :src="palmPrintFileInput[0].value" alt="file" class="palm-print-image">
              <iframe v-else-if="isPdf(palmPrintFileInput[0].value)" :src="palmPrintFileInput[0].value" class="file-viewer-pdf"></iframe>
              <div v-else-if="isGif(palmPrintFileInput[0].value)">
                <v-img :src="palmPrintFileInput[0].value" class="file-viewer-image"></v-img>
              </div>
            </div>
          </div>
        </v-col>

        <!-- Vertical Divider -->
        <v-col cols="12" sm="1" class="divider-container">
          <div class="vertical-divider"></div>
        </v-col>

        <!-- Officers in Charge and Palm Print -->
        <v-col cols="12" sm="5">
          <div v-for="field in personalInfoInputFields.slice(19, 22)" :key="field.key" class="field-container">
            <strong class="field-label">{{ field.label }}:</strong>
            <span class="field-value">{{ field.value }}</span>
          </div>
          <div style="visibility: hidden;">
            content
          </div>
          <div class="palm-print-container">
            <strong>{{ palmPrintFileInput[1].label }}</strong>
            <div>
              <img v-if="isImage(palmPrintFileInput[1].value)" :src="palmPrintFileInput[1].value" alt="file" class="palm-print-image">
              <iframe v-else-if="isPdf(palmPrintFileInput[1].value)" :src="palmPrintFileInput[1].value" class="file-viewer-pdf"></iframe>
              <div v-else-if="isGif(palmPrintFileInput[1].value)">
                <v-img :src="palmPrintFileInput[1].value" class="file-viewer-image"></v-img>
              </div>
            </div>
          </div>
        </v-col>

        <!-- Special Mark Files -->
        <v-row justify="center" class="special-mark-container">
          <v-col
            v-for="field in specialMark"
            :key="field.key"
            cols="12"
            sm="3"
            class="special-mark-item"
          >
            <div class="center-align">
              <strong>{{ field.label }}</strong>
              <div>
                <img v-if="isImage(field.value)" :src="field.value" alt="file" class="field-image">
                <iframe v-else-if="isPdf(field.value)" :src="field.value" class="file-viewer-pdf"></iframe>
                <div v-else-if="isGif(field.value)">
                  <v-img :src="field.value" class="file-viewer-image"></v-img>
                </div>
              </div>
            </div>
          </v-col>
        </v-row>
        
        <v-container v-if="record?.form_template" class="form-image-container">
          <p>ឯកសារ</p>
          
            <template v-if="isPdf(record?.form_template)">
              <v-btn @click="openFormTemplatePreview = true" style="margin-top: 6px;">មេីលឯកសារ</v-btn>
            </template>

            <template v-else>
              <v-img
                :src="record?.form_template ?? ''"
                @click="openFormTemplatePreview = true"
                max-width="50"
                class="my-2 cursor-pointer"
              ></v-img>
            </template>

          <v-dialog v-model="openFormTemplatePreview" :class="isPdf(record.form_template) ? 'pdfPreview' : 'imgFormTemplatePreview'">
            <v-card>
              <template v-if="isPdf(record.form_template)">
                <iframe :src="record.form_template" width="100%" height="1000px"></iframe>
              </template>
              <template v-else>
                <v-img :src="record.form_template ?? ''"></v-img>
              </template>
            </v-card>
          </v-dialog>
        </v-container>

      </v-row>
    </v-container>
  </div>
</template>


<style scoped src="../../styles/details.scss"></style>

<style lang="css" scoped>
  .pdfPreview {
    max-width: 100%;
    height: 100%;
  }

  .imgFormTemplatePreview {
    max-width: 100%;
  }
</style>