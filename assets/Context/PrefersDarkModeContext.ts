import React, { useContext } from 'react';

// @ts-ignore
const DarkModeContext = React.createContext();

export const useDarkMode = () => useContext(DarkModeContext);

export default DarkModeContext;
