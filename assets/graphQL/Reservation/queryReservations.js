import GraphQL from '../GraphQL';

export default () => GraphQL.query(`
    query {
      reservations {
        service
        name
        index
      }
    }`
)
.then((response) => response.json())
.then((json) => {
    return json.data.reservations;
});
