import GraphQL from '../GraphQL';

export default (id:number) => GraphQL.mutation(`
    mutation {
        retryFailedMessage(input:{
            id: ${JSON.stringify(id)}
        }) {
            success
        }
}`)
  .then((response) => response.json())
  .then((responseAsJson) => responseAsJson.data.retryFailedMessage);
