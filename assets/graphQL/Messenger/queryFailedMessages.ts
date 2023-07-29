import { query } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default (max: number) =>
  query(
    graphql(`
      query FailedMessages($max: Int!) {
        failedMessages(max: $max) {
          id
          className
          exceptionMessage
          date
        }
      }
    `),
    { max },
  ).then((data) => data.failedMessages);
