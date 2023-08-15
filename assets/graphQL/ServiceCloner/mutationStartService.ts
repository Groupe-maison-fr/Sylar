import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (
  client: authenticatedClient,
  masterName: string,
  index: number | null,
  instanceName: string,
) =>
  client
    .mutation(
      graphql(`
        mutation MutationStartService(
          $masterName: String!
          $index: Int
          $instanceName: String!
        ) {
          startService(
            input: {
              masterName: $masterName
              index: $index
              instanceName: $instanceName
            }
          ) {
            ... on SuccessOutput {
              success
            }
            ... on FailedOutput {
              code
              message
            }
          }
        }
      `),
      { masterName, index, instanceName },
    )
    .then((data) => data.startService);
