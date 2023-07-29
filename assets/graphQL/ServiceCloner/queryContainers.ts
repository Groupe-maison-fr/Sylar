import GraphQL from '../GraphQL';

export interface Container {
  containerName: string;
  masterName: string;
  instanceName: string;
  instanceIndex: number;
  zfsFilesystemName: string;
  time: number;
  uptime: number;
  dockerState: string;
}
export default (): Promise<Container[]> =>
  GraphQL.query(
    `
    query {
      containers {
        containerName
        masterName
        instanceName
        instanceIndex
        zfsFilesystemName
        time
        uptime
        dockerState
      }
    }`
  )
    .then((response) => response.json())
    .then((json) => {
      return json.data.containers;
    });
