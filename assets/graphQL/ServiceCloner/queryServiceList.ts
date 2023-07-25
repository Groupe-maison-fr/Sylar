import GraphQL from '../GraphQL';

export interface ServiceAndInstance {
  name: string;
  containers: {
    containerName: string;
    instanceName: string;
    instanceIndex: number;
  }[];
}

export default (): Promise<ServiceAndInstance[]> =>
  GraphQL.query(
    `
  query {
    services {
        name
        containers {
          containerName
          instanceName
          instanceIndex
        }
    }
  }`,
  )
    .then((response) => response.json())
    .then((json) => json.data.services);
