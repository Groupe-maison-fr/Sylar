import GraphQL from "../GraphQL";

export default (max) => GraphQL.mutation(`
    query {
        failedMessages(
            max: ${max}
        ) {
            id
            className
            exceptionMessage
            date
        }
    }`)
    .then((response) => response.json())
    .then((responseAsJson) => responseAsJson.data.failedMessages);
