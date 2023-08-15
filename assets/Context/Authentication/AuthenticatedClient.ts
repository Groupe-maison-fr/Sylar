import { useContext } from 'react';
import axios from 'axios';
import { RequestDocument, Variables } from 'graphql-request';
import { TypedDocumentNode } from '@graphql-typed-document-node/core';
import { GraphQLClient } from 'graphql-request';
import { GraphQLClientResponse } from 'graphql-request/src/types';
import { EnqueueSnackbar, useSnackbar } from 'notistack';
import {
  AUTHENTICATION_ACTIONS,
  AuthenticationContext,
} from './AuthenticationContext';

export const GRAPHQL_ROOT_URL: string = '/graphql/';
export const API_LOGIN_CHECK_URL: string = '/api/login_check';

const hasError = (response: Error | GraphQLClientResponse<unknown> | any) => {
  const errors = [
    ...(response?.response?.extensions?.warnings ?? []),
    ...(response?.response?.extensions?.errors ?? []),
    ...(response?.response?.errors ?? []),
  ]
    .map((warning: any) => `${warning.message} ${warning?.path.join(',')}`)
    .join(',');
  return errors.length > 0 ? errors : null;
};

const graphqlClient = (
  context: { jwt: string | null },
  enqueueSnackbar: EnqueueSnackbar,
) =>
  new GraphQLClient(GRAPHQL_ROOT_URL, {
    errorPolicy: 'all',
    requestMiddleware: (request) => {
      return context.jwt
        ? {
            ...request,
            headers: {
              ...request.headers,
              Authorization: `Bearer ${context.jwt}`,
            },
          }
        : request;
    },
    responseMiddleware: (response) => {
      const error = hasError(response);
      if (error) {
        enqueueSnackbar(error, { variant: 'error' });
      }
    },
  });

export interface loginData {
  username: string;
  password: string;
}

export const useAuthenticatedClient = () => {
  const { enqueueSnackbar } = useSnackbar();
  const context = useContext(AuthenticationContext);
  const isLogged = context.authenticationState.isLogged;
  const authenticationState = {
    isLogged: isLogged,
    username: isLogged ? context.authenticationState.username : null,
    jwt: isLogged ? context.authenticationState.jwt : null,
  };
  return {
    client: {
      query: <T>(
        queryPayload: RequestDocument | TypedDocumentNode<T, Variables>,
        args: Variables,
      ) =>
        graphqlClient(authenticationState, enqueueSnackbar).request<T>(
          queryPayload,
          args,
        ),
      mutation: <T>(
        queryPayload: RequestDocument | TypedDocumentNode<T, Variables>,
        args: Variables,
      ) =>
        graphqlClient(authenticationState, enqueueSnackbar).request<T>(
          queryPayload,
          args,
        ),
    },
    authenticationState,
    authenticate: async (data: loginData): Promise<boolean> => {
      const result = await axios.request({
        method: 'POST',
        url: API_LOGIN_CHECK_URL,
        data,
      });
      context.dispatchAuthentication({
        type: AUTHENTICATION_ACTIONS.AUTHENTICATE,
        username: data.username,
        jwt: result.data.token,
      });
      return true;
    },
    dispatchAuthentication: (action: any) => {
      context.dispatchAuthentication(action);
    },
  };
};

export type authenticatedClient = ReturnType<
  typeof useAuthenticatedClient
>['client'];
