import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient) =>
  client
    .query(
      graphql(`
        query Filesystems {
          filesystems {
            name
            type
            origin
            mountPoint
            available
            refer
            used
            usedByChild
            usedByDataset
            usedByRefreservation
            usedBySnapshot
            creationTimestamp
          }
        }
      `),
      {},
    )
    .then((data) => data.filesystems);
