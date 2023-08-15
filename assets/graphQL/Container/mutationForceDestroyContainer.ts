import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient, name: string) =>
  client
    .mutation(
      graphql(`
        mutation MutationForceDestroyContainer($name: String!) {
          forceDestroyContainer(input: { name: $name }) {
            ... on SuccessOutput {
              success
            }
            ... on FailedOutput {
              code
            }
          }
        }
      `),
      { name },
    )
    .then((data) => data.forceDestroyContainer);
