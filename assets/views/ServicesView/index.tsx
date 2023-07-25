import React from 'react';
import { Container, Grid, makeStyles } from '@material-ui/core';
import Page from '../../components/Page';
import ServiceList from './ServiceList';
import ServiceStart from './ServiceStart';

const useStyles = makeStyles((theme) => ({
  root: {
    backgroundColor: theme.palette.background.default,
    minHeight: '100%',
    paddingBottom: theme.spacing(3),
    paddingTop: theme.spacing(3),
  },
}));

const Dashboard = () => {
  const classes = useStyles();
  return (
    <div className={classes.root}>
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
    </div>
  );
};

export default Dashboard;
