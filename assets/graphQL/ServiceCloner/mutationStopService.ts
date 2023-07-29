import { mutation } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (masterName: string, instanceName: string) =>
  mutation(
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
  ).then((data) => data.stopService);
