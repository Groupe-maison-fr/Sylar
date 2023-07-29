import { mutation } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (
  masterName: string,
  index: number | null,
  instanceName: string,
) =>
  mutation(
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
  ).then((data) => data.startService);
