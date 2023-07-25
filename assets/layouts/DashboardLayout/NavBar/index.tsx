import React, { useEffect } from 'react';
import { useLocation } from 'react-router-dom';
import {
  Box,
  Drawer,
  Hidden,
  List,
  Typography,
  makeStyles,
} from '@material-ui/core';
import {
  BarChart as BarChartIcon,
  Settings as SettingsIcon,
  ZapOff as FrammerIcon,
  BarChart2 as BarChart2Icon,
  Bookmark as BookmarkIcon,
} from 'react-feather';
import NavItem from './NavItem';

const items = [
  {
    href: '/app/services',
    icon: BarChartIcon,
    title: 'Services',
  },
  {
    href: '/app/reservations',
    icon: BookmarkIcon,
    title: 'Reservations',
  },
  {
    href: '/app/system',
    icon: SettingsIcon,
    title: 'System',
  },
  {
    href: '/app/messenger',
    icon: FrammerIcon,
    title: 'Errors',
  },
  {
    href: '/app/graph/docker',
    icon: BarChart2Icon,
    title: 'Dockers',
  },
  {
    href: '/app/graph/host',
    icon: BarChart2Icon,
    title: 'Host',
  },
];

const useStyles = makeStyles(() => ({
  mobileDrawer: {
    width: 256,
  },
  desktopDrawer: {
    width: 256,
    top: 64,
    height: 'calc(100% - 64px)',
  },
  avatar: {
    cursor: 'pointer',
    width: 64,
    height: 64,
  },
}));

const NavBar = ({
  onMobileClose,
  openMobile,
}: {
  onMobileClose: () => void;
  openMobile: boolean;
}) => {
  const classes = useStyles();
  const location = useLocation();

  useEffect(() => {
    if (openMobile && onMobileClose) {
      onMobileClose();
    }
  }, [location.pathname]);

  const content = (
    <Box height="100%" display="flex" flexDirection="column">
      <Box p={2}>
        <List>
          {items.map((item) => (
            <NavItem
              href={item.href}
              key={item.title}
              title={item.title}
              icon={item.icon}
            />
          ))}
        </List>
      </Box>
      <Box flexGrow={1} />
      <Box p={2} m={2} bgcolor="background.dark">
        <Typography align="center" gutterBottom variant="h4">
          &nbsp;
        </Typography>
      </Box>
    </Box>
  );

  return (
    <>
      {/* @ts-ignore */}
      <Hidden lgUp>
        <Drawer
          anchor="left"
          classes={{ paper: classes.mobileDrawer }}
          onClose={onMobileClose}
          open={openMobile}
          variant="temporary"
        >
          {content}
        </Drawer>
      </Hidden>
      {/* @ts-ignore */}
      <Hidden mdDown>
        <Drawer
          anchor="left"
          classes={{ paper: classes.desktopDrawer }}
          open
          variant="persistent"
        >
          {content}
        </Drawer>
      </Hidden>
    </>
  );
};

export default NavBar;
