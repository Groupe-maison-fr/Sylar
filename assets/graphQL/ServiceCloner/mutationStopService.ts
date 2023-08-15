import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (
  client: authenticatedClient,
  masterName: string,
  instanceName: string,
) =>
  client
    .mutation(
      graphql(`
        mutation MutationStopService(
          $masterName: String!
          $instanceName: String!
        ) {
          stopService(
            input: { masterName: $masterName, instanceName: $instanceName }
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
      { masterName, instanceName },
    )
    .then((data) => data.stopService);
