import { graphql } from '../../gql/gql';
import { authenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

export default (client: authenticatedClient, name: string) =>
  client
    .mutation(
      graphql(`
        mutation MutationForceDestroyFilesystem($name: String!) {
          forceDestroyFilesystem(input: { name: $name }) {
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
      { name },
    )
    .then((data) => data.forceDestroyFilesystem);
