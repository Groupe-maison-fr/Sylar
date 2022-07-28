import React from 'react';
import {
  Box, Card, CardHeader,
  Container, Divider, Grid,
  makeStyles
} from '@material-ui/core';
import Page from '../../components/Page';
import Grafana from "./Grafana";
import Iframe from '../../components/Iframe';
import clsx from 'clsx';

const useStyles = makeStyles((theme) => ({
  root: {
    backgroundColor: theme.palette.background.default,
    minHeight: '100%',
    paddingBottom: theme.spacing(3),
    paddingTop: theme.spacing(3)
  }
}));

const GraphView = (props) => {
  const classes = useStyles();
  console.log(`/grafana/d/${props.graphUrlId}?orgId=1&refresh=5s&kiosk&fullscreen`);
  return (
      <Page
          className={classes.root}
          title="Dashboard"
      >
        <Card className={clsx(classes.root)}>
          <CardHeader title="Grafana"/>
          <Divider/>
          <Iframe src={`/grafana/d/${props.graphUrlId}?orgId=1&refresh=5s&kiosk&fullscreen`} height={props.graphHeight}/>
        </Card>
      </Page>
  );
};

export default GraphView;
