import React from 'react';
import { SvgIcon } from '@material-ui/core';

const Logo = (props) => {
  return (
      <img
          alt="Logo"
          src="/static/logo.svg"
          {...props}
      />
  );
};

export default Logo;
