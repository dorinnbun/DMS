// Utilities
import { defineStore } from 'pinia'

export const useTmpStore = defineStore('tmp', {
  state: () => ({
    userUUID: '',
    tmpObject: {},
  }),
})
