import GraphQL from '../GraphQL';

export default (masterName, instanceName) => GraphQL.query(`
    mutation {
      restartService (input:{
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
    .then((json) => json.data.restartService);
