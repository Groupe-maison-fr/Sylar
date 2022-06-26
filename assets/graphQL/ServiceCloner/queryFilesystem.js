import GraphQL from '../GraphQL';

export default () => GraphQL.query(`
    query {
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
    }`)
    .then((response) => response.json())
    .then((json) => {
      return json.data.filesystems;
    });
