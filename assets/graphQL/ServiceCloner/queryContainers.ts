import { query } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default () =>
  query(
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
  ).then((data) => data.containers);
