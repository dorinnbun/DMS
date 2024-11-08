// Utilities
import { defineStore } from 'pinia'

export const useTableStateStore = defineStore('tableState', {
  state: () => ({
    state: {
      page: 1,
      itemsPerPage: 10,
      sortBy: [],
      keySearch: ''
    }
  }),
  persist: true,
})
