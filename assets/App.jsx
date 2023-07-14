import 'react-perfect-scrollbar/dist/css/styles.css';
import React, {useEffect, useState} from 'react';
import {useRoutes} from 'react-router-dom';
import './mixins/chartjs';
import GlobalStyles from './components/GlobalStyles';
import routes from "./routes";
import {createMuiTheme, ThemeProvider} from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import PrefersDarkModeContext from "./Context/PrefersDarkModeContext";
import {initialDarkMode} from "./components/DarkMode";
import EventBus from './components/EventBus';
import AppSnackbars from './AppSnackbars';

EventBus.handleEventSource('/.well-known/mercure?topic=sylar');

const App = () => {
    const routing = useRoutes(routes);

    const [prefersDarkMode, setPrefersDarkMode] = useState(initialDarkMode());
    const theme = React.useMemo(() => createMuiTheme({
            palette: {
                type: prefersDarkMode ? 'dark' : 'light',
            },
        }),[prefersDarkMode],
    );

    return (
        <PrefersDarkModeContext.Provider value={{prefersDarkMode,setPrefersDarkMode}}>
            <AppSnackbars/>
            <ThemeProvider theme={theme}>
                <CssBaseline/>
                <GlobalStyles />
                {routing}
            </ThemeProvider>
        </PrefersDarkModeContext.Provider>
    );
};

export default App;
