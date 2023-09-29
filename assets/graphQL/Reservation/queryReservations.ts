import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient) =>
  client
    .query(
      graphql(`
        query reservations {
          reservations {
            service
            name
            index
          }
        }
      `),
      {},
    )
    .then((data) => data.reservations);
