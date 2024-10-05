<template>

  <div style="padding: 0 15px">
    
    <v-data-table-server
      :headers="headers"
      :items="trashItems"
      :loading="loading"
      item-value="name"
      :pagination="false"
      @update:options="loadItems"
      hide-default-footer="true"
    >
      <template v-slot:top>
        <v-toolbar flat >

          <input
            type="text"
            placeholder="ស្វែករកឯកសារ..."
            v-model="search"
            class="search"
          />

          <v-spacer></v-spacer>

          <!-- Restore record confirmation modal -->
          <v-dialog v-model="restoreDialog" max-width="40%">
            <v-card>
              <v-card-title style="padding: 30px 0 30px 30px;" class="text-h5">តើអ្នកប្រាកដថាចង់រក្សាទុកឯកសារនេះវិញទេ?</v-card-title>
              <v-card-actions style="padding-bottom: 30px">
                <v-spacer></v-spacer>
                <v-btn class="danger-btn" variant="text" @click="closeRestore">បោះបង់</v-btn>
                <v-btn class="primary-btn" variant="text" @click="restoreItemConfirm">រក្សាទុកឯកសារ</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>

          <!-- Hard delete record confirmation modal -->
          <v-dialog v-model="hardDeleteConfirmationDialog" max-width="40%">
            <v-card>
              <v-card-title style="padding: 30px 0 30px 30px;" class="text-h5">តើអ្នកប្រាកដថាចង់លុបឯកសារ(hard delete)នេះទេ?</v-card-title>
              <v-card-actions style="padding-bottom: 30px">
                <v-spacer></v-spacer>
                <v-btn class="danger-btn" variant="text" @click="closeHardDeleteDialog">បោះបង់</v-btn>
                <v-btn class="primary-btn" variant="text" @click="hardDeleteItemConfirm">លុប(hard delete)</v-btn>
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

            <template v-if="header.key === 'deleted_by'">
              {{ item [ header.key ].name }} ({{ item [ header.key ].role }})
            </template>
            
            <template v-else>
              {{ item [ header.key ] }}
            </template>
          </td>

          <td>
            <v-icon small color="green" @click.stop="restoreItem(item)">mdi-file-restore</v-icon>
            <v-icon small color="red" @click.stop="hardDeleteItem(item)" v-if="userRole === 'admin'">mdi-delete</v-icon>
          </td>

        </tr>
      </template>
  
    </v-data-table-server>

  </div>
</template>

<script>

  import { 
    getAllDeletedRecords as getAllDeletedRecordsAPI, 
    restoreRecord as restoreRecordAPI,
    hardDeleteRecord as hardDeleteRecordAPI
  } from '@/_api/document'

  import { useUserStore } from '@/stores/user'

  export default {
    data: () => ({

      search: '',
      trashItems: [],
      loading: true,

      hardDeleteConfirmationDialog: false,
      restoreDialog: false,
      selectedItem: {},

      currentParams: {
        keySearch: {}
      },
      headers: [
        { title: 'លេខរៀង', key: 'id', sortable: false },
        { title: 'លេខសៀវភៅ', key: 'book_id' },
        { title: 'នាមគោត្តនាម', align: 'start', key: 'full_name' },
        { title: 'ត្រូវបានលុបដោយ', key: 'deleted_by', sortable: false },
        { title: '', key: 'actions', sortable: false },
      ],
      restoredIndex: -1
    }),

    watch: {
      hardDeleteConfirmationDialog (val) {
        val || this.closeHardDeleteDialog()
      },
      restoreDialog (val) {
        val || this.closeRestore()
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
      if (this.userRole === 'user') {
        this.$router.push('/')
      }
    },

    computed : {
      userRole () {
        return useUserStore().user.role
      }
    },

    methods: {
      initialize () {
        this.trashItems = []
      },

      async fetchDeletedRecordData (params) {
        
        try {
          const { data: { data } } = await getAllDeletedRecordsAPI(params)
          return data.item

        } catch (error) {
          console.log("error", error);
        }
      },


      async loadItems(params = {}) {
        this.loading = true;
        
        try {
          this.currentParams = {
            ...this.currentParams,
            ...params
          }; 
          const data = await this.fetchDeletedRecordData(this.currentParams)
          this.loading = false
          this.trashItems = data

        } catch (error) {
          console.log("error", error);
          this.loading = false
        }
      },

      restoreItem (item) {
        this.restoreDialog = true
        this.selectedItem = item
      },

      async restoreItemConfirm () {
        try {
          const result = await restoreRecordAPI(this.selectedItem.id)
          console.log("result", result);
          this.loadItems()

        } catch (error) {
          console.log(error);
        }
        this.restoreDialog = false
      },

      hardDeleteItem (item) {
        this.hardDeleteConfirmationDialog = true
        this.selectedItem = item
      },

      async hardDeleteItemConfirm () {
        try {
          const result = await hardDeleteRecordAPI(this.selectedItem.id)
          console.log("result", result);
          
          this.loadItems()

        } catch (error) {
          console.log(error);
        }
        this.hardDeleteConfirmationDialog = false
      },

      closeHardDeleteDialog () {
        this.hardDeleteConfirmationDialog = false
        this.$nextTick(() => {
          this.restoredIndex = -1
        })
      },

      closeRestore () {
        this.restoreDialog = false
        this.$nextTick(() => {
          this.restoredIndex = -1
        })
      },

      viewRecord (item) {
        const uuid = item.uuid;
        // this.$router.push("/records/" + uuid)
      }
    },
  }
</script>


<style scoped src="../../styles/table.scss"></style>