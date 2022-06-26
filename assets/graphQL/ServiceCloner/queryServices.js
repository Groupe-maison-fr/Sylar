import GraphQL from '../GraphQL';

export default () => GraphQL.query(`
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
  }`)
    .then((response) => response.json())
    .then((json) => json.data.services);
