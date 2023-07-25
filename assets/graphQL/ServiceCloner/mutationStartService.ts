import GraphQL from '../GraphQL';

export default (
  masterName: string,
  index: number | null,
  instanceName: string,
) =>
  GraphQL.query(
    `
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
    }`,
  )
    .then((response) => response.json())
    .then((json) => json.data.startService);
