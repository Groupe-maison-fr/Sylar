import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import { svgFavicon } from '@space-kit/hat';
import { SnackbarProvider } from 'notistack';
import App from './App';

import RawSvg from './components/LogoPicture';

svgFavicon(RawSvg);

ReactDOM.render(
  <BrowserRouter>
    <SnackbarProvider maxSnack={5}>
      <App />
    </SnackbarProvider>
  </BrowserRouter>,
  document.getElementById('root'),
);
