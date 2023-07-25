import GraphQL from '../GraphQL';

export interface Reservation {
  service: string;
  name: string;
  index: number;
}
export default (): Promise<Reservation[]> =>
  GraphQL.query(
    `
    query {
      reservations {
        service
        name
        index
      }
    }`,
  )
    .then((response) => response.json())
    .then((json) => {
      return json.data.reservations;
    });
