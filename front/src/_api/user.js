import axiosInstance from '../../axiosConfig';

const token = localStorage.getItem('dms-token')

export const getAllUsers = async (params) => {

  try {
    return await axiosInstance.get(
      `${ import.meta.env.VITE_API_BASE_URL }/user`,
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
    console.error(error)
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
    console.error(error)
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
    console.error(error)
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
    console.error(error)
  }
}