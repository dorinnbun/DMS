import axios from 'axios'

const token = localStorage.getItem('dms-token')

export const getAllUsers = async (params) => {

  

  try {
    return await axios.get(
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
    return await axios.get(
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
    return await axios.post(
      `${ import.meta.env.VITE_API_BASE_URL }/user`,
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
    return await axios.patch(
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
    return await axios.delete(
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