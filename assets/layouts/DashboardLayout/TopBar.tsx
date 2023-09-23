import React, { useContext } from 'react';
import { Link as RouterLink } from 'react-router-dom';
import BrightnessHighIcon from '@material-ui/icons/BrightnessHigh';
import Brightness3Icon from '@material-ui/icons/Brightness3';
import {
  AppBar,
  Box,
  Hidden,
  IconButton,
  Toolbar,
  Typography,
} from '@material-ui/core';
import Logo from '../../components/Logo';
import { useDarkMode } from '../../Context/PrefersDarkModeContext';
import { setInitialDarkMode } from '../../components/DarkMode';

const TopBar = () => {
  // @ts-ignore
  const { prefersDarkMode, setPrefersDarkMode } = useDarkMode();
  return (
    <AppBar
      elevation={0}
    >
      <Toolbar>
        <RouterLink to="/">
          <Logo />
        </RouterLink>
        <Box flexGrow={1}>
          <Typography variant="h4" style={{ paddingLeft: 20 }}>
            Sylar Dashboard
          </Typography>
        </Box>
        {/* @ts-ignore */}
        <Hidden mdDown>
          <IconButton
            color="inherit"
            onClick={() => {
              const newDarkMode = !prefersDarkMode;
              setPrefersDarkMode(newDarkMode);
              setInitialDarkMode(newDarkMode);
            }}
          >
            {prefersDarkMode ? <Brightness3Icon /> : <BrightnessHighIcon />}
          </IconButton>
        </Hidden>
      </Toolbar>
    </AppBar>
  );
};

export default TopBar;
