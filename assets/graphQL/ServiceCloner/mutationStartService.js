import GraphQL from '../GraphQL';

export default (masterName, index, instanceName) => GraphQL.query(`
    mutation {
      startService (input:{
        masterName: "${masterName}"
        index: ${index}
        instanceName: "${instanceName}"
      }){ 
        ... on SuccessOutput{
          success
        } 
        ... on FailedOutput{
          code
          message
        } 
      } 
    }`)
    .then((response) => response.json())
    .then((json) => json.data.startService);
