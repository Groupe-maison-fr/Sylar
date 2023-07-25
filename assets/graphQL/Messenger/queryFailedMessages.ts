import GraphQL from '../GraphQL';

export interface FailedMessageSummary {
  id: number;
  className: string;
  exceptionMessage: string;
  date: string;
  checked: boolean;
}
export default (max: number): Promise<FailedMessageSummary[]> =>
  GraphQL.query(
    `
    query {
        failedMessages(
            max: ${max}
        ) {
            id
            className
            exceptionMessage
            date
        }
    }`,
  )
    .then((response) => response.json())
    .then((responseAsJson) =>
      responseAsJson.data.failedMessages.map((failedMessage: any) => {
        return { ...failedMessage, checked: false };
      }),
    );
