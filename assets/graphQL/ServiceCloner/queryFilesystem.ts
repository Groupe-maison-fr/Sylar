import { query } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default () =>
  query(
    graphql(`
      query Filesystems {
        filesystems {
          name
          type
          origin
          mountPoint
          available
          refer
          used
          usedByChild
          usedByDataset
          usedByRefreservation
          usedBySnapshot
          creationTimestamp
        }
      }
    `),
    {},
  ).then((data) => data.filesystems);
