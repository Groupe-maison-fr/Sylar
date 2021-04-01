import GraphQL from '../GraphQL';

export default (masterName, instanceName) => GraphQL.query(`
    mutation {
      stopService (input:{
        masterName: "${masterName}"
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
    .then((json) => {
      return json.data.stopService;
    });
