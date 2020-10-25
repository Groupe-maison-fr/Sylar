import GraphQL from '../GraphQL';

export default () => GraphQL.query(`
    query {
      containers {
        containerName
        masterName
        instanceName
        instanceIndex
        zfsFilesystemName
        time
      }
    }`)
    .then((response) => response.json())
    .then((json) => {
      return json.data.containers;
    });
