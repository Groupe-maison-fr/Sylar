import GraphQL from '../GraphQL';

export default ():Promise<{name:string}[]> => GraphQL.query(`
  query {
    services {
        name
    }
  }`)
  .then((response) => response.json())
  .then((json) => json.data.services);
