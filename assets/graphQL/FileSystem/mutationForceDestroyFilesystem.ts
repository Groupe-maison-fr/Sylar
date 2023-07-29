import { mutation } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (name: string) =>
  mutation(
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
  ).then((data) => data.forceDestroyFilesystem);
