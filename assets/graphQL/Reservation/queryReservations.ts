import { query } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default () =>
  query(
    graphql(`
      query reservations {
        reservations {
          service
          name
          index
        }
      }
    `),
    {},
  ).then((data) => data.reservations);
