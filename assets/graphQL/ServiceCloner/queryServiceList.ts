import { graphql } from '../../gql/gql';
import { query } from '../GraphQL';

export default () =>
  query(
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
  ).then((data) => data.services);
