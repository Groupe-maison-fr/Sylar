import React from 'react';
import { styled } from '@mui/material/styles';
import { Link as RouterLink } from 'react-router-dom';
import clsx from 'clsx';
import { AppBar, Toolbar } from '@mui/material';
import Logo from '../../components/Logo';

const PREFIX = 'TopBar';

const classes = {
  root: `${PREFIX}-root`,
  toolbar: `${PREFIX}-toolbar`,
};

const StyledAppBar = styled(AppBar)({
  [`&.${classes.root}`]: {},
  [`& .${classes.toolbar}`]: {
    height: 64,
  },
});

const TopBar = ({ ...rest }) => {
  return (
    <StyledAppBar className={clsx(classes.root)} elevation={0} {...rest}>
      <Toolbar className={classes.toolbar}>
        <RouterLink to="/">
          <Logo />
        </RouterLink>
      </Toolbar>
    </StyledAppBar>
  );
};

export default TopBar;
