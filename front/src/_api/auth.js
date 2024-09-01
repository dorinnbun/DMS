import axiosInstance from '../../axiosConfig';


export const login = async (body) => {

  try {
    return await axiosInstance.post(
      `${ import.meta.env.VITE_API_BASE_URL }/auth/login`,
      body,
      {
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )
  } catch (error) {
    console.error(error)
  }
}


export const logout = async (body) => {

  try {
    return await axiosInstance.get(
      `${ import.meta.env.VITE_API_BASE_URL }/auth/logout`,
      {
        headers: {
          'Content-Type': 'application/json'
        }
      }
    )
  } catch (error) {
    console.error(error)
  }
}