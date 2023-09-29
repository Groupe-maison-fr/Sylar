import { createContext, useContext } from 'react';

const LOCALSTORAGE_CREDENTIALS = 'AUTHENTICATION::CREDENTIALS';

export const AUTHENTICATION_ACTIONS = {
  BOOTSTRAP: 'BOOTSTRAP',
  AUTHENTICATE: 'AUTHENTICATE',
  LOGOUT: 'LOGOUT',
};

export interface AuthenticationContextData {
  isLogged: boolean;
  username: string | null;
  jwt: string | null;
}

export interface StoredAuthenticationData {
  isLogged: boolean;
  username: string | null;
  jwt: string | null;
}

const saveLocalStorageCredentials = (
  username: string | null,
  jwt: string | null,
) => {
  localStorage.setItem(
    LOCALSTORAGE_CREDENTIALS,
    JSON.stringify({
      username,
      jwt,
    }),
  );
};

const getLocalStorageCredentials = (): StoredAuthenticationData => {
  return {
    username: null,
    jwt: null,
    ...JSON.parse(localStorage.getItem(LOCALSTORAGE_CREDENTIALS) ?? '{}'),
  };
};

export const authenticationReducer = (
  previousState: AuthenticationContextData,
  action: any,
): AuthenticationContextData => {
  switch (action.type) {
    case AUTHENTICATION_ACTIONS.AUTHENTICATE:
      saveLocalStorageCredentials(action.username, action.jwt);
      return {
        ...previousState,
        isLogged: true,
        username: action.username,
        jwt: action.jwt,
      };
    case AUTHENTICATION_ACTIONS.LOGOUT:
      saveLocalStorageCredentials(null, null);
      return {
        ...previousState,
        isLogged: false,
        username: null,
        jwt: null,
      };
    default:
      return previousState;
  }
};

export const postsContextDefaultValue: AuthenticationContextData = (() => {
  const { username, jwt } = getLocalStorageCredentials();
  return {
    isLogged: username !== null || jwt !== null,
    username,
    jwt,
  };
})();

export const AuthenticationContext = createContext<{
  authenticationState: AuthenticationContextData;
  //authenticate: (data: loginData) => Promise<boolean>;
  dispatchAuthentication: React.Dispatch<any>;
}>({
  authenticationState: postsContextDefaultValue,
  //authenticate: (_data: loginData) => Promise.resolve(false),
  dispatchAuthentication: () => null,
});

export const useAuthentication = () => useContext(AuthenticationContext);
