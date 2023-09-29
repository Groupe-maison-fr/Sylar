import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient) =>
  client
    .query(
      graphql(`
        query ServicesAndInstances {
          services {
            name
            containers {
              containerName
              instanceName
              instanceIndex
            }
          }
        }
      `),
      {},
    )
    .then((data) => data.services);
