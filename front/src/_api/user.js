import axiosInstance from '../../axiosConfig';
import { setParams } from '../utils/setParams.js';

const token = localStorage.getItem('dms-token')


export const getAllUsers = async (params) => {

  try {
    return await axiosInstance.get(
      `${ import.meta.env.VITE_API_BASE_URL }/user${ params ? '?' + setParams(params) : '' }`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    throw error
  }
}


export const getUser = async (id) => {

  try {
    return await axiosInstance.get(
      `${ import.meta.env.VITE_API_BASE_URL }/user/${ id }`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    throw error
  }
}


export const createUser = async (body) => {
  try {
    return await axiosInstance.post(
      `${ import.meta.env.VITE_API_BASE_URL }/auth/register`,
      body,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    throw error
  }
}


export const updateUser = async (body) => {
  try {
    return await axiosInstance.patch(
      `${ import.meta.env.VITE_API_BASE_URL }/user/${ body.id }`,
      body,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    throw error
  }
}


export const deleteUser = async (id) => {
  try {
    return await axiosInstance.delete(
      `${ import.meta.env.VITE_API_BASE_URL }/user/${ id }`,
      {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${ token }`
        }
      }
    )
  } catch (error) {
    throw error
  }
}

export const sendResetPasswordEmail = async (email) => {
  try {
    return await axiosInstance.post(
      `${ import.meta.env.VITE_API_BASE_URL }/user/forget_password`,
      { email },
      {
        headers: { 'Content-Type': 'application/json' }
      }
    ) 
  } catch (error) {
    throw error
  }
}


export const verifyOTP = async (body, uuid) => {
  try {
    return await axiosInstance.post(
      `${ import.meta.env.VITE_API_BASE_URL }/user/verify_otp/${ uuid }`,
      body,
      {
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )
  } catch (error) {
    throw error
  }
}

export const resetPassword = async (body, uuid) => {
  try {
    return await axiosInstance.post(
      `${ import.meta.env.VITE_API_BASE_URL }/user/resetPassword/${ uuid }`,
      body,
      {
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )
  } catch (error) {
    throw error
  }
}