import React from 'react';
import { styled } from '@mui/material/styles';
import { Card, CardHeader, Divider } from '@mui/material';
import clsx from 'clsx';
import Page from '../../components/Page';
import Iframe from '../../components/Iframe';

const PREFIX = 'GraphView';

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

const GraphView = ({
  graphUrlId,
  graphHeight,
}: {
  graphUrlId: string;
  graphHeight: number;
}) => {
  console.log(`/grafana/d/${graphUrlId}?orgId=1&refresh=5s&kiosk&fullscreen`);
  return (
    <Root className={classes.root}>
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
    </Root>
  );
};

export default GraphView;
