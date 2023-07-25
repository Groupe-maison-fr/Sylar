import React from 'react';
import { styled } from '@mui/material/styles';
import { Container, Grid } from '@mui/material';
import Page from '../../components/Page';
import Containers from './Containers';
import Filesystems from './Filesystems';

const PREFIX = 'SystemView';

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

const SystemView = () => {
  return (
    <Root className={classes.root}>
      <Page title="Dashboard">
        <Container maxWidth={false}>
          <Grid container spacing={3}>
            <Grid item lg={12} md={12} xl={12} xs={12}>
              <Containers />
            </Grid>
            <Grid item lg={12} md={12} xl={12} xs={12}>
              <Filesystems />
            </Grid>
          </Grid>
        </Container>
      </Page>
    </Root>
  );
};

export default SystemView;
