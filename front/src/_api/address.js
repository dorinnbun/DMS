import axios from 'axios'


export const getAddresses = async () => {
  
    try {
      return await axios.get(
        `${ import.meta.env.VITE_API_BASE_URL }/address`,
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

  
export const getProvinces = async () => {

  try {
    return await axios.get(
      `${ import.meta.env.VITE_API_BASE_URL }/address/province`,
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


export const getDistricts = async (provinceId) => {

  try {
    return await axios.get(
      `${ import.meta.env.VITE_API_BASE_URL }/address/district/${ provinceId }`,
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


export const getCommunes = async (districtId) => {

  try {
    return await axios.get(
      `${ import.meta.env.VITE_API_BASE_URL }/address/communes/${ districtId }`,
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
