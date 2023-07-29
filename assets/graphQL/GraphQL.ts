import { TypedDocumentNode } from '@graphql-typed-document-node/core';
import request, { RequestDocument, Variables } from 'graphql-request';

export const GRAPHQL_ROOT_URL: string = '/graphql/';

export const query = <T>(
  queryPayload: RequestDocument | TypedDocumentNode<T, Variables>,
  args: Variables,
) => request<T>(GRAPHQL_ROOT_URL, queryPayload, args);

export const mutation = <T>(
  queryPayload: RequestDocument | TypedDocumentNode<T, Variables>,
  args: Variables,
) => request<T>(GRAPHQL_ROOT_URL, queryPayload, args);
