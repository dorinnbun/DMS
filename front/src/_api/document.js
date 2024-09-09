import axiosInstance from '../../axiosConfig';
import {setParams} from '../utils/setParams.js';

const token = localStorage.getItem('dms-token')


export const getAllRecords = async (params) => {

  try {
    return await axiosInstance.get(
      `${ import.meta.env.VITE_API_BASE_URL }/document${ params ? '?' + setParams(params) : '' }`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    console.error(error)
  }
}


export const getRecord = async (id) => {

  try {
    return await axiosInstance.get(
      `${ import.meta.env.VITE_API_BASE_URL }/document/${ id }`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    console.error(error)
  }
}


export const createRecord = async (formData) => {
  try {
    return await axiosInstance.post(
      `${ import.meta.env.VITE_API_BASE_URL }/document`,
      formData,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': `Bearer ${ token }`
        }
      }
    )

  } catch (error) {
    console.error(error)
  }
}


export const updateRecord = async (id, body) => {
  
  try {
    return await axiosInstance.post(
      `${ import.meta.env.VITE_API_BASE_URL }/document/${ id }`,
      body,
      {
        headers: {
          'Content-Type': 'multipart/form-data',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    console.error(error)
  }
}



export const deleteRecord = async (id) => {
  try {
    return await axiosInstance.delete(
      `${ import.meta.env.VITE_API_BASE_URL }/document/${ id }`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    console.error(error)
  }
}


export const restoreRecord = async (id) => {
  try {
    return await axiosInstance.get(
      `${ import.meta.env.VITE_API_BASE_URL }/document/restore/${ id }`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    console.error(error)
  }
}

export const getAllDeletedRecords = async (params) => {
  try {
    return await axiosInstance.get(
      `${ import.meta.env.VITE_API_BASE_URL }/document/getTrashList${ params ? '?' + setParams(params) : '' }`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    console.error(error)
  }
}