import 'react-perfect-scrollbar/dist/css/styles.css';
import React, { useState } from 'react';
import { useRoutes } from 'react-router-dom';

import { createMuiTheme, ThemeProvider } from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import GlobalStyles from './components/GlobalStyles';
import routes from './routes';
import PrefersDarkModeContext from './Context/PrefersDarkModeContext';
import { initialDarkMode } from './components/DarkMode';
import EventBus from './components/EventBus';
import AppSnackbars from './AppSnackbars';
import { LogListProvider } from './Context/LogListContext';

EventBus.handleEventSource('/.well-known/mercure?topic=sylar');

const App = () => {
  const routing = useRoutes(routes);

  const [prefersDarkMode, setPrefersDarkMode] = useState(initialDarkMode());
  const theme = React.useMemo(() => {
    return createMuiTheme({
      palette: {
        type: prefersDarkMode ? 'dark' : 'light',
      },
    });
  }, [prefersDarkMode],);

  return (
    // eslint-disable-next-line react/jsx-no-constructed-context-values
    <PrefersDarkModeContext.Provider value={{ prefersDarkMode, setPrefersDarkMode }}>
      <LogListProvider>
        <AppSnackbars />
        <ThemeProvider theme={theme}>
          <CssBaseline />
          <GlobalStyles />
          {routing}
        </ThemeProvider>
      </LogListProvider>
    </PrefersDarkModeContext.Provider>
  );
};

export default App;
