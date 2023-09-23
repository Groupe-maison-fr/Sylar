import GraphQL from '../GraphQL';

export default (name:string) => GraphQL.query(`
    mutation {
      forceDestroyFilesystem (input:{
        name: "${name}"
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
  .then((json) => json.data.forceDestroyFilesystem);
