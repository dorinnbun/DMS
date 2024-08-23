<template>
  <div class="form-container">
    <h1 class="form-title">{{ formName }}</h1>

    <v-container fluid>
      <v-row>

        <!-- Form Details -->
        <v-col cols="12" sm="2">
          <template v-for="field in inputFields" :key="field.key">
            <div class="field-container">
              <strong class="field-label">{{ field.label }}:</strong>
              <div v-if="field.type === 'file'">
                <img :src="field.value" alt="file" class="field-image">
              </div>
              <span v-else>{{ field.value }}</span>
            </div>
          </template>
        </v-col>

        <!-- Personal Information -->
        <v-col cols="12" sm="10">
          <v-row>
            <template v-for="field in personalInfoInputFields.slice(0, 14)" :key="field.key">
              <v-col cols="12" :sm="field.col" :offset-md="field.offset || 0">
                <div class="field-container">
                  <strong class="field-label">{{ field.label }}:</strong>
                  <span class="field-value">{{ field.value }}</span>
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
            <strong class="field-label">{{ field.label }}:</strong>
            <div v-if="field.type === 'file'">
              <img :src="field.value" alt="file" class="field-image">
            </div>
            <span v-else>{{ field.value }}</span>
          </div>
        </v-col>

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
              <img :src="palmPrintFileInput[0].value" alt="file" class="palm-print-image">
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
          <div class="palm-print-container">
            <strong>{{ palmPrintFileInput[1].label }}</strong>
            <div>
              <img :src="palmPrintFileInput[1].value" alt="file" class="palm-print-image">
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
                <img :src="field.value" alt="file" class="field-image">
              </div>
            </div>
          </v-col>
        </v-row>

      </v-row>
    </v-container>
  </div>

  <button onclick="window.print()">Print</button>
</template>


<script>
export default {
  data: () => ({
    formName: 'សលាកប័ត្រឯកកត្តជន',
    inputFields: [
      {
        key: "id",
        label: "លេខ",
        type: "text",
        value: "12345"
      }, {
        key: "identityPhoto",
        label: "រូបថត ៤x៦",
        type: "file",
        value: "../../../public/john-doe.jpg"
      }, {
        key: "bookID",
        label: "លេខសៀវភៅ",
        type: "text",
        value: "BK-2024-001"
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
      value: "AB123"
    }, {
      key: "full_name", // 
      label: "គោត្តនាមនាម",
      type: "text",
      col: 6,
      value: "John Doe"
    }, {
      key: "nickname",
      label: "ឈ្មោះហៅក្រៅ",
      type: "text",
      col: 6,
      value: "Johnny"
    }, {
      key: "dob",
      label: "ថ្ងៃខែឆ្នាំកំណេីត",
      type: "date",
      col: 6,
      value: "1990-01-01"
    }, {
      key: "pob",
      label: "ភូមិកំណេីត",
      type: "date",
      col: 6,
      value: "សង្កាត់បឹងកេងកង ខណ្ឌចំការមន រាជធានីភ្នំពេញ"
    },
    {
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
      value: "Buddhism"
    }, {
      key: "previous_occupation",
      label: "មុខរបរធ្លាប់ធ្វេីពីមុន",
      type: "text",
      col: 12,
      value: "Teacher"
    }, {
      key: "occupation",
      label: "មុខរបរបច្ចុប្បន្ន",
      type: "text",
      col: 12,
      value: "Engineer"
    }, {
      key: "current_address",
      label: "អាស័យដ្ឋានបច្ចុប្បន្ន",
      type: "text",
      col: 12,
      value: "123 Street, Phnom Penh"
    },
    {
      key: "identity",
      label: "ភិនភាគ",
      type: "text",
      col: 10,
      value: "N/A"
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
      value: "123 Street, Phnom Penh"
    },

    // parents info
    {
      key: "father_name",
      label: "ឪពុកឈ្មោះ",
      type: "text",
      col: 12,
      value: "John Doe Sr."
    }, {
      key: "father_address",
      label: "នៅ",
      type: "text",
      col: 12,
      value: "456 Avenue, Phnom Penh"
    }, {
      key: "mother_name",
      label: "ម្តាយឈ្មោះ",
      type: "text",
      col: 12,
      value: "Jane Doe"
    }, {
      key: "mother_address",
      label: "នៅ",
      type: "text",
      col: 12,
      value: "456 Avenue, Phnom Penh"
    },

    // officers in charge
    {
      key: "private_certificate_officer",
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
      { key: "rightThumbPrint", label: "មេដៃស្តាំ", type: "file", value: "../../../public/finger-print.jpg" },
      { key: "rightIndexPrint", label: "ចង្អុលដៃស្តាំ", type: "file", value: "../../../public/finger-print.jpg" },
      { key: "rightMiddlePrint", label: "ដៃកណ្តាលស្តាំ", type: "file", value: "../../../public/finger-print.jpg" },
      { key: "rightRingPrint", label: "នាងដៃស្តាំ", type: "file", value: "../../../public/finger-print.jpg" },
      { key: "rightPinkyPrint", label: "កូនដៃស្តាំ", type: "file", value: "../../../public/finger-print.jpg" },

      { key: "leftThumbPrint", label: "មេដៃឆ្វេង", type: "file", value: "../../../public/finger-print.jpg" },
      { key: "leftIndexPrint", label: "ចង្អុលដៃឆ្វេង", type: "file", value: "../../../public/finger-print.jpg" },
      { key: "leftMiddlePrint", label: "ដៃកណ្តាលឆ្វេង", type: "file", value: "../../../public/finger-print.jpg" },
      { key: "leftRingPrint", label: "នាងដៃឆ្វេង", type: "file", value: "../../../public/finger-print.jpg" },
      { key: "leftPinkyPrint", label: "កូនដៃឆ្វេង", type: "file", value: "../../../public/finger-print.jpg" },
    ],

    palmPrintFileInput: [
      { key: "leftPalmPrint", label: "បាតដៃឆ្វេង", type: "file", value: "../../../public/palm-print.jpeg" },
      { key: "rightPalmPrint", label: "បាតដៃស្តាំ", type: "file", value: "../../../public/palm-print.jpeg" },
    ],

    specialMark: [
      { "key": "specialMark1", "label": "ស្លាកសញ្ញាពិសេស", "type": "file", "value": "../../../public/finger-print.jpg" },
      { "key": "specialMark2", "label": "ស្លាកសញ្ញាពិសេស", "type": "file", "value": "../../../public/finger-print.jpg" },
      { "key": "specialMark3", "label": "ស្លាកសញ្ញាពិសេស", "type": "file", "value": "../../../public/finger-print.jpg" },
    ]
  })
}
</script>


<style scoped src="../../styles/records.scss"></style>
<style scoped src="../../styles/details.scss"></style>
