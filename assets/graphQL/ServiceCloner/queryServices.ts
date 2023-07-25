import GraphQL from '../GraphQL';

export interface Services {
  name: string;
  image: string;
  command: string;
  labels: {
    name: string;
    value: string;
  }[];
  environments: {
    name: string;
    value: string;
  }[];
  ports: {
    containerPort: string;
    hostPort: string;
    hostIp: string;
  }[];
  containers: {
    containerName: string;
    masterName: string;
    instanceName: string;
    instanceIndex: number;
    zfsFilesystemName: string;
    exposedPorts: string[];
    time: number;
    dockerState: string;
    zfsFilesystem?: {
      name: string;
      type: string;
      origin: string;
      mountPoint: string;
      available: number;
      used: number;
      usedByChild: number;
      usedByDataset: number;
      usedByRefreservation: number;
      usedBySnapshot: number;
      creationTimestamp: number;
    };
  }[];
}
export default (): Promise<Services[]> =>
  GraphQL.query(
    `
  query {
    services {
        name
        image
        command
        labels {
          name
          value
        }
        environments {
          name
          value
        }
        ports {
          containerPort
          hostPort
          hostIp
        }
        containers {
          containerName
          masterName
          instanceName
          instanceIndex
          zfsFilesystemName
          exposedPorts
          time
          dockerState
          zfsFilesystem{
            name
            type
            origin
            mountPoint
            available
            used
            usedByChild
            usedByDataset
            usedByRefreservation
            usedBySnapshot
            creationTimestamp
          }
        }
    }
  }`,
  )
    .then((response) => response.json())
    .then((json) => json.data.services);
