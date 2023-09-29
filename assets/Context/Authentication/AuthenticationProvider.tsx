import * as React from 'react';
import {
  AuthenticationContext,
  authenticationReducer,
  postsContextDefaultValue,
} from './AuthenticationContext';
import { useReducer } from 'react';

export const AuthenticationProvider = ({ children }: any) => {
  const [state, dispatchAuthentication] = useReducer(
    authenticationReducer,
    postsContextDefaultValue,
  );

  return (
    <AuthenticationContext.Provider
      value={{
        authenticationState: state,
        dispatchAuthentication,
      }}
    >
      {children}
    </AuthenticationContext.Provider>
  );
};
