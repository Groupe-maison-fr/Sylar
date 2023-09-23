import React from 'react';
import {
  Card,
  CardHeader,
  Divider,
  makeStyles
} from '@material-ui/core';
import clsx from 'clsx';
import Page from '../../components/Page';
import Iframe from '../../components/Iframe';

const useStyles = makeStyles((theme) => ({
  root: {
    backgroundColor: theme.palette.background.default,
    minHeight: '100%',
    paddingBottom: theme.spacing(3),
    paddingTop: theme.spacing(3)
  }
}));

const GraphView = ({ graphUrlId, graphHeight }:{graphUrlId:string, graphHeight:number}) => {
  const classes = useStyles();
  console.log(
    `/grafana/d/${graphUrlId}?orgId=1&refresh=5s&kiosk&fullscreen`
  );
  return (
    <div className={classes.root}>
      <Page title="Dashboard">
        <Card className={clsx(classes.root)}>
          <CardHeader title="Grafana" />
          <Divider />
          <Iframe
            src={`/grafana/d/${graphUrlId}?orgId=1&refresh=5s&kiosk&fullscreen`}
            height={graphHeight}
          />
        </Card>
      </Page>
    </div>
  );
};

export default GraphView;
