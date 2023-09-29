import 'react-perfect-scrollbar/dist/css/styles.css';
import React, { useState } from 'react';
import { useRoutes } from 'react-router-dom';

import { ThemeProvider, createTheme } from '@mui/material/styles';
import CssBaseline from '@mui/material/CssBaseline';
import routes from './routes';
import PrefersDarkModeContext from './Context/PrefersDarkModeContext';
import { initialDarkMode } from './components/DarkMode';
import EventBus from './components/EventBus';
import AppSnackbars from './AppSnackbars';
import { LogListProvider } from './Context/LogListContext';
import { AuthenticationProvider } from './Context/Authentication/AuthenticationProvider';

EventBus.handleEventSource('/.well-known/mercure?topic=sylar');

const App = () => {
  const routing = useRoutes(routes);

  const [prefersDarkMode, setPrefersDarkMode] = useState(initialDarkMode());
  const theme = React.useMemo(() => {
    return createTheme({
      palette: {
        mode: prefersDarkMode ? 'dark' : 'light',
      },
    });
  }, [prefersDarkMode]);

  return (
    <PrefersDarkModeContext.Provider
      value={{ prefersDarkMode, setPrefersDarkMode }}
    >
      <AuthenticationProvider>
        <LogListProvider>
          <AppSnackbars />
          <ThemeProvider theme={theme}>
            <CssBaseline />
            {routing}
          </ThemeProvider>
        </LogListProvider>
      </AuthenticationProvider>
    </PrefersDarkModeContext.Provider>
  );
};

export default App;
