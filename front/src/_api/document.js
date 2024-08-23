import axios from 'axios'

const token = localStorage.getItem('jwt_token')


export const getAllRecords = async () => {

  try {
    return await axios.get(
      `${ import.meta.env.VITE_API_BASE_URL }/document`,
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
    return await axios.get(
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


export const createRecord = async (body) => {
  try {
    return await axios.post(
      `${ import.meta.env.VITE_API_BASE_URL }/document`,
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


export const updateRecord = async (body) => {
  try {
    return await axios.patch(
      `${ import.meta.env.VITE_API_BASE_URL }/document/${ body.id }`,
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



export const deleteRecord = async (id) => {
  try {
    return await axios.delete(
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
    return await axios.post(
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