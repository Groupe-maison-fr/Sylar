import React from 'react';
import { SvgIcon } from '@material-ui/core';

const Logo = (props) => {
  return (
      <SvgIcon {...props}  enableBackground="new 0 0 400 400" viewBox="0 0 400 400" width="400" height="400">
          <g id="Landing" stroke="none"  fill="none">
              <circle fill="white" cx="200" cy="200" r="200" />
              <g transform="scale(0.2,-0.2)  translate(00,-1600)" >
                  <path fill="#3f51b5" d="M1640 1006q-99 0 -138 94q-144 0 -316 -70q-107 -44 -144 -81t-39.5 -58t20.5 -35.5t64.5 -27.5t96.5 -26q288 -67 365 -93q153 -51 229 -112q72 -61 72 -166q0 -87 -53.5 -147t-141.5 -107t-196.5 -83.5t-229 -62.5t-237.5 -43q-229 -32 -391 -32q-295 0 -455 71
q-60 27 -98 135q-30 89 -30 146.5t10 63.5t41 0.5t81 -18.5l120 -32q279 -70 590 -70q229 0 388 41q94 25 100 56q4 27 -148 67q-69 19 -160 40q-441 107 -532 138t-118.5 55t-45.5 53q-33 57 -33 108.5t5.5 79.5t15.5 52q21 49 53 67q257 144 755 253q443 98 729 98
q58 0 87 -20q50 -37 44 -79t-25.5 -77.5t-48.5 -64t-64.5 -50t-73.5 -35.5q-76 -28 -148 -28z" />
              </g>
          </g>
      </SvgIcon>
  );
};

export default Logo;
