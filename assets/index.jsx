import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import { svgFavicon } from '@space-kit/hat';
import RawSvg from './components/LogoPicture';
import { SnackbarProvider } from 'notistack';

svgFavicon(RawSvg);

ReactDOM.render((
  <BrowserRouter>
      <SnackbarProvider maxSnack={5}>
          <App />
      </SnackbarProvider>
  </BrowserRouter>
), document.getElementById('root'));
