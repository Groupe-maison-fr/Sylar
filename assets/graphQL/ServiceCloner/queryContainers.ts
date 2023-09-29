import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient) =>
  client
    .query(
      graphql(`
        query containers {
          containers {
            containerName
            masterName
            instanceName
            instanceIndex
            zfsFilesystemName
            time
            uptime
            dockerState
          }
        }
      `),
      {},
    )
    .then((data) => data.containers);
