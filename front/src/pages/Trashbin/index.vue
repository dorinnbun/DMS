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

          <!-- Restore record confirmation modal -->
          <v-dialog v-model="restoreDialog" max-width="500px">
            <v-card>
              <v-card-title class="text-h5">តើអ្នកប្រាកដថាចង់រក្សាទុកឯកសារនេះវិញទេ?</v-card-title>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeRestore">បោះបង់</v-btn>
                <v-btn color="red" variant="text" @click="restoreItemConfirm">លុបចោល</v-btn>
                <v-spacer></v-spacer>
              </v-card-actions>
            </v-card>
          </v-dialog>
          
        </v-toolbar>
      </template>

      <template v-slot:item="{ item, index }">
        <tr @click="viewRecord(item)" style="cursor: pointer;">

          <td v-for="key in Object.keys(item)">
            {{ item[key] }}
          </td>

          <td>
            <v-icon small color="green" @click.stop="restoreItem(item)">mdi-file-restore</v-icon>
          </td>

        </tr>
      </template>
  
    </v-data-table-server>

  </div>
</template>

<script>

  const fetchDeletedData = {
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
      restoreDialog: false,
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
          title: '', 
          key: 'actions', 
          sortable: false 
        },
      ],
      restoredIndex: -1
    }),

    watch: {
      dialog (val) {
        val || this.close()
      },
      restoreDialog (val) {
        val || this.closeRestore()
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
            id: 159,
            bookID: 88,
            name: 'David Lee'
          },
          {
            id: 237,
            bookID: 848,
            name: 'Daniel Lee'
          },
          {
            id: 262,
            bookID: 888,
            name: 'Neary Lee'
          },
          {
            id: 305,
            bookID: 188,
            name: 'Bopha Lee'
          },
          {
            id: 356,
            bookID: 89,
            name: 'Dyna Lee'
          }
        ]
      },

      loadItems ({ page, itemsPerPage, sortBy }) {
        this.loading = true
        // fetchDeletedData.fetch({ page, itemsPerPage, sortBy }).then(({ items, total }) => {
        //   this.serverItems = items
        //   console.log("in loaditem", this.serverItems);
          
        //   this.totalItems = total
        //   this.loading = false
        // })
        this.loading = false
      },

      restoreItem (item) {
        this.restoredIndex = this.serverItems.indexOf(item)
        console.log("this.restoredIndex", this.restoredIndex);
        this.restoreDialog = true
      },

      restoreItemConfirm () {
        this.serverItems.splice(this.restoredIndex, 1)
        this.closeRestore()
      },

      close () {
        this.dialog = false
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
        const id = item.id;
        console.log("id", id);
      }
    },
  }
</script>


<style scoped src="../../styles/table.scss"></style>