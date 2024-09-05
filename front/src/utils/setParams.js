export const setParams = ({ filters, keySearch, limit, page, sort, others }) => {

  const params = [];
  if (limit) {
    params.push(`limit=${ limit }`);
  }

  if (page) {
    params.push(`page=${ page }`);
  }

  if (sort) {
    params.push(`orders[${ sort.key }]=${ sort.reverse ? 'asc' : 'dsc' }`);
  }

  if (filters) {
    Object.keys(filters).forEach((key) => {
      params.push(`filters[${ key }]=${ filters[key] }`);
    });
  }

  if (keySearch) {
    Object.keys(keySearch).forEach((key) => {
      params.push(`keySearch[${ key }]=${ keySearch[key] }`);
    });
  }

  if (others) {
    Object.keys(others).forEach((key) => {
      params.push(`${ key }=${ others[key] }`);
    });
  }

  return params.join('&');
}