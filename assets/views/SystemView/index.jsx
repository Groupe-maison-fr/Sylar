import React from 'react';
import {
  Box,
  Container, Grid,
  makeStyles
} from '@material-ui/core';
import Page from '../../components/Page';
import Containers from "./Containers";
import Filesystems from "./Filesystems";

const useStyles = makeStyles((theme) => ({
  root: {
    backgroundColor: theme.palette.background.default,
    minHeight: '100%',
    paddingBottom: theme.spacing(3),
    paddingTop: theme.spacing(3)
  }
}));

const SettingsView = () => {
  const classes = useStyles();
  return (
      <Page
          className={classes.root}
          title="Dashboard"
      >
        <Container maxWidth={false}>
          <Grid container spacing={3}>
            <Grid item lg={12} md={12} xl={12} xs={12}>
              <Containers/>
            </Grid>
            <Grid item lg={12} md={12} xl={12} xs={12}>
              <Filesystems/>
            </Grid>
          </Grid>
        </Container>
      </Page>
  );
};

export default SettingsView;
