import axios from 'axios'

const token = localStorage.getItem('dms-token')


export const getAllRoles = async (params) => {

  try {
    return await axios.get(
      `${ import.meta.env.VITE_API_BASE_URL }/role`,
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