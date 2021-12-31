import GraphQL from "../GraphQL";

export default (id) => GraphQL.mutation(`
    query {
        failedMessage(
            id: ${id}
        ) {
            id
            className
            message
            exceptionMessage
            backtrace {
              namespace
              short_class
              class
              type
              function
              file
              line
              arguments
            }
            flattenException{
                message
                code
                previous{
                    message
                    code
                    class
                    statusCode
                    statusText
                    headers
                    file
                    line
                }
                traceAsString
                class
                statusCode
                statusText
                headers
                file
                line
            }
            date
        }
    }`)
    .then((response) => response.json())
    .then((responseAsJson) => responseAsJson.data.failedMessage);
