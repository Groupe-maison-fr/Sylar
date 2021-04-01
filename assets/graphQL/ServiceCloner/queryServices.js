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
        environments{
          name
          value
        }
        ports{
          containerPort
          hostPort
          hostIp
        }
      }
    }`)
    .then((response) => response.json())
    .then((json) => {
      return json.data.services;
    });
