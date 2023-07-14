import React, {useState} from 'react';
import {Container, Grid, makeStyles} from '@material-ui/core';
import Page from '../../components/Page';
import Reservations from './Reservations';
import ReservationAdd from "./ReservationAdd";

const useStyles = makeStyles((theme) => ({
  root: {
    backgroundColor: theme.palette.background.default,
    minHeight: '100%',
    paddingBottom: theme.spacing(3),
    paddingTop: theme.spacing(3)
  }
}));

const Dashboard = () => {
  const classes = useStyles();
  const [refresh,setRefresh] = useState('');
  return (
    <Page
      className={classes.root}
      title="Reservations"
    >
      <Container maxWidth={false}>
        <Grid container spacing={3}>
          <Grid item lg={12} md={12} xl={12} xs={12}>
            <ReservationAdd onAdd={setRefresh}/>
          </Grid>
          <Grid item lg={12} md={12} xl={12} xs={12}>
            <Reservations refresh={refresh}/>
          </Grid>
        </Grid>
      </Container>
    </Page>
  );
};

export default Dashboard;
