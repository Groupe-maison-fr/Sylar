import GraphQL from '../GraphQL';

export default (service, name, index) => GraphQL.query(`
mutation {
    addReservation(input:{
        service: "${service}"
        name: "${name}"
        index: ${index}
    }) {
        ...on SuccessOutput{
            success
        }
        ...on FailedOutput{
            message
        }
    }
}
`)
    .then((response) => response.json())
    .then((json) => json.data.addReservation);