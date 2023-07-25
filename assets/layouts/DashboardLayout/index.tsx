import React, { useState } from 'react';
import { styled } from '@mui/material/styles';
import { Outlet } from 'react-router-dom';
import NavBar from './NavBar';
import TopBar from './TopBar';
import DrawerLoggerBar from './DrawerLoggerBar';

const PREFIX = 'DashboardLayout';

const classes = {
  root: `${PREFIX}-root`,
  wrapper: `${PREFIX}-wrapper`,
  contentContainer: `${PREFIX}-contentContainer`,
  content: `${PREFIX}-content`,
};

const Root = styled('div')(({ theme }) => ({
  [`&.${classes.root}`]: {
    backgroundColor: theme.palette.background.default,
    display: 'flex',
    height: '100%',
    overflow: 'hidden',
    width: '100%',
  },

  [`& .${classes.wrapper}`]: {
    display: 'flex',
    flex: '1 1 auto',
    overflow: 'hidden',
    paddingTop: 64,
    [theme.breakpoints.up('lg')]: {
      paddingLeft: 180,
    },
  },

  [`& .${classes.contentContainer}`]: {
    display: 'flex',
    flex: '1 1 auto',
    overflow: 'hidden',
  },

  [`& .${classes.content}`]: {
    flex: '1 1 auto',
    height: '100%',
    overflow: 'auto',
  },
}));

const DashboardLayout = () => {
  const [isMobileNavOpen, setMobileNavOpen] = useState(false);
  const [isDrawerLoggerBarOpen, setDrawerLoggerBarOpen] = useState(false);

  return (
    <Root className={classes.root}>
      <TopBar
        isDrawerLoggerBarOpen={isDrawerLoggerBarOpen}
        setDrawerLoggerBarOpen={setDrawerLoggerBarOpen}
      />
      <NavBar
        onMobileClose={() => setMobileNavOpen(false)}
        openMobile={isMobileNavOpen}
      />
      <div className={classes.wrapper}>
        <div className={classes.contentContainer}>
          <div className={classes.content}>
            <Outlet />
          </div>
        </div>
      </div>
      <DrawerLoggerBar
        onClose={setDrawerLoggerBarOpen}
        open={isDrawerLoggerBarOpen}
      />
    </Root>
  );
};

export default DashboardLayout;
