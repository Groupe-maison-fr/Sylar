import React from 'react';
import { styled } from '@mui/material/styles';
import { Container, Grid } from '@mui/material';
import Page from '../../components/Page';
import ServiceList from './ServiceList';
import ServiceStart from './ServiceStart';

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
  return (
    <Root className={classes.root}>
      <Page title="Services">
        <Container maxWidth={false}>
          <Grid container spacing={3}>
            <Grid item lg={12} md={12} xl={12} xs={12}>
              <ServiceStart />
            </Grid>
            <Grid item lg={12} md={12} xl={12} xs={12}>
              <ServiceList />
            </Grid>
          </Grid>
        </Container>
      </Page>
    </Root>
  );
};

export default Dashboard;
