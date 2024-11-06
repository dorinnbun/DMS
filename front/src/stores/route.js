// Utilities
import { defineStore } from 'pinia'

export const useRouteStore = defineStore('route', {
  state: () => ({
    previous: ''
  }),
  persist: true,
})
