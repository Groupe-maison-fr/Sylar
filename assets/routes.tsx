import React from 'react';
import { Navigate } from 'react-router-dom';
import DashboardLayout from './layouts/DashboardLayout';
import MainLayout from './layouts/MainLayout';
import ServicesView from './views/ServicesView';
import NotFoundView from './views/NotFoundView';
import SystemView from './views/SystemView';
import ReservationsView from './views/ReservationsView';
import MessengerView from './views/MessengerView';
import GraphView from './views/GraphView';

const routes = [
  {
    path: 'app',
    element: <DashboardLayout />,
    children: [
      { path: 'services', element: <ServicesView /> },
      { path: 'reservations', element: <ReservationsView /> },
      { path: 'system', element: <SystemView /> },
      { path: 'messenger', element: <MessengerView /> },
      {
        path: 'graph/docker',
        element: (
          <GraphView graphUrlId="pMEd7m0Mz/dockers" graphHeight={1300} />
        ),
      },
      {
        path: 'graph/host',
        element: <GraphView graphUrlId="rYdddlPWk/host" graphHeight={1300} />,
      },
      { path: '*', element: <Navigate to="/404" /> },
    ],
  },
  {
    path: '/',
    element: <MainLayout />,
    children: [
      { path: '404', element: <NotFoundView /> },
      { path: '/', element: <Navigate to="/app/services" /> },
      { path: '*', element: <Navigate to="/404" /> },
    ],
  },
];

export default routes;
