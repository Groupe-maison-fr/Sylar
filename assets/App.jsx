import 'react-perfect-scrollbar/dist/css/styles.css';
import React, {useState} from 'react';
import {useRoutes} from 'react-router-dom';
import './mixins/chartjs';
import GlobalStyles from "./components/GlobalStyles";
import routes from "./routes";
import {createMuiTheme, ThemeProvider} from '@material-ui/core/styles';
import CssBaseline from '@material-ui/core/CssBaseline';
import PrefersDarkModeContext from "./Context/PrefersDarkModeContext";
import {initialDarkMode} from "./components/DarkMode";

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
            <ThemeProvider theme={theme}>
                <CssBaseline/>
                <GlobalStyles />
                {routing}
            </ThemeProvider>
        </PrefersDarkModeContext.Provider>
    );
};

export default App;
