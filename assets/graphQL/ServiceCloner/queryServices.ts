import { query } from '../GraphQL';
import { graphql } from '../../gql/gql';

export default () =>
  query(
    graphql(`
      query Services {
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
            uptime
            dockerState
            zfsFilesystem {
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
      }
    `),
    {},
  ).then((data) => data.services);
