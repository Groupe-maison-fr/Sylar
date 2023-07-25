import React from 'react';
import { Drawer, makeStyles } from '@material-ui/core';
import LogList from './LogList';

const useStyles = makeStyles(() => ({
  desktopDrawer: {
    width: 400,
    top: 64,
    height: 'calc(100% - 64px)',
  },
}));

const DrawerLoggerBar = ({
  onClose,
  open,
}: {
  onClose: (_open: boolean) => void;
  open: boolean;
}) => {
  const classes = useStyles();

  return (
    <Drawer
      anchor="right"
      classes={{ paper: classes.desktopDrawer }}
      onClose={onClose}
      open={open}
      variant="persistent"
    >
      <LogList />
    </Drawer>
  );
};

export default DrawerLoggerBar;
