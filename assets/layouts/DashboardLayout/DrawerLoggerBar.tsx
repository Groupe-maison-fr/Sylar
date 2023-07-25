import React from 'react';
import { styled } from '@mui/material/styles';
import { Drawer } from '@mui/material';
import LogList from './LogList';

const PREFIX = 'DrawerLoggerBar';

const classes = {
  desktopDrawer: `${PREFIX}-desktopDrawer`,
};

const StyledDrawer = styled(Drawer)(() => ({
  [`& .${classes.desktopDrawer}`]: {
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
  return (
    <StyledDrawer
      anchor="right"
      classes={{ paper: classes.desktopDrawer }}
      onClose={onClose}
      open={open}
      variant="persistent"
    >
      <LogList />
    </StyledDrawer>
  );
};

export default DrawerLoggerBar;
