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
        mutation MutationRestartService(
          $masterName: String!
          $instanceName: String!
        ) {
          restartService(
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
    .then((data) => data.restartService);
