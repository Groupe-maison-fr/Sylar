import { mutation } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (name: string) =>
  mutation(
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
  ).then((data) => data.forceDestroyContainer);
