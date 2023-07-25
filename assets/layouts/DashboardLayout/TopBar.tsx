import React from 'react';
import { Link as RouterLink } from 'react-router-dom';
import BrightnessHighIcon from '@mui/icons-material/BrightnessHigh';
import Brightness3Icon from '@mui/icons-material/Brightness3';
import LoggerOpenedIcon from '@mui/icons-material/BorderVerticalOutlined';
import LoggerClosedIcon from '@mui/icons-material/BorderVertical';
import {
  AppBar,
  Badge,
  Box,
  Hidden,
  IconButton,
  Toolbar,
  Typography,
} from '@mui/material';
import Logo from '../../components/Logo';
import { useDarkMode } from '../../Context/PrefersDarkModeContext';
import { setInitialDarkMode } from '../../components/DarkMode';
import { useLogList } from '../../Context/LogListContext';

const TopBar = ({
  isDrawerLoggerBarOpen,
  setDrawerLoggerBarOpen,
}: {
  setDrawerLoggerBarOpen: (open: boolean) => void;
  isDrawerLoggerBarOpen: boolean;
}) => {
  // @ts-ignore
  const { prefersDarkMode, setPrefersDarkMode } = useDarkMode();
  const { logListContextData } = useLogList();
  return (
    <AppBar elevation={0} style={{ backgroundColor: 'rgb(25, 118, 210)' }}>
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
        <Hidden lgDown>
          <IconButton
            color="inherit"
            onClick={() => {
              const newDarkMode = !prefersDarkMode;
              setPrefersDarkMode(newDarkMode);
              setInitialDarkMode(newDarkMode);
            }}
            size="large"
          >
            {prefersDarkMode ? <Brightness3Icon /> : <BrightnessHighIcon />}
          </IconButton>
          <IconButton
            color="inherit"
            onClick={() => {
              setDrawerLoggerBarOpen(!isDrawerLoggerBarOpen);
            }}
            size="large"
          >
            <Badge
              badgeContent={logListContextData.list.length}
              color="secondary"
            >
              {isDrawerLoggerBarOpen ? (
                <LoggerOpenedIcon />
              ) : (
                <LoggerClosedIcon />
              )}
            </Badge>
          </IconButton>
        </Hidden>
      </Toolbar>
    </AppBar>
  );
};

export default TopBar;
