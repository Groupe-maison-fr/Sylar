import React, { useState } from 'react';
import { styled } from '@mui/material/styles';
import { Container, Grid } from '@mui/material';
import Page from '../../components/Page';
import Reservations from './Reservations';
import ReservationAdd from './ReservationAdd';

const PREFIX = 'Dashboard';

const classes = {
  root: `${PREFIX}-root`,
};

const Root = styled('div')(({ theme }) => ({
  [`&.${classes.root}`]: {
    backgroundColor: theme.palette.background.default,
    minHeight: '100%',
    paddingBottom: theme.spacing(3),
    paddingTop: theme.spacing(3),
  },
}));

const Dashboard = () => {
  const [refresh, setRefresh] = useState('');
  return (
    <Root className={classes.root}>
      <Page title="Reservations">
        <Container maxWidth={false}>
          <Grid container spacing={3}>
            <Grid item lg={12} md={12} xl={12} xs={12}>
              <ReservationAdd onAdd={setRefresh} />
            </Grid>
            <Grid item lg={12} md={12} xl={12} xs={12}>
              <Reservations refresh={refresh} />
            </Grid>
          </Grid>
        </Container>
      </Page>
    </Root>
  );
};

export default Dashboard;
