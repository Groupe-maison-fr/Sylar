import React, { useEffect, useState } from 'react';
import { styled } from '@mui/material/styles';
import PerfectScrollbar from 'react-perfect-scrollbar';
import moment from 'moment';

import {
  Box,
  Button,
  Card,
  CardHeader,
  Collapse,
  Divider,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  Tooltip,
  Typography,
} from '@mui/material';
import ReplayIcon from '@mui/icons-material/Replay';
import DeleteIcon from '@mui/icons-material/Delete';
import queryService, {
  Services,
} from '../../graphQL/ServiceCloner/queryServices';
import mutationStopService from '../../graphQL/ServiceCloner/mutationStopService';
import mutationRestartService from '../../graphQL/ServiceCloner/mutationRestartService';
import EventBus from '../../components/EventBus';
import ago from '../../components/ago';

const PREFIX = 'ServiceList';

const classes = {
  root: `${PREFIX}-root`,
  name: `${PREFIX}-name`,
  value: `${PREFIX}-value`,
  actions: `${PREFIX}-actions`,
};

const StyledCard = styled(Card)(() => ({
  [`& .${classes.root}`]: {},

  [`& .${classes.name}`]: {
    fontWeight: 'bold',
  },

  [`& .${classes.value}`]: {
    display: 'inline-block',
    marginLeft: '7px',
  },

  [`& .${classes.actions}`]: {
    justifyContent: 'flex-end',
  },
}));

const NameValueList = ({
  data,
}: {
  data: { name: string; value: string }[];
}) => {
  return (
    <ul style={{ padding: 0, margin: 0 }}>
      {data.map((row) => (
        <li key={row.name} style={{ lineHeight: '0.1' }}>
          <span className={classes.name}>{row.name}</span>
          <pre className={classes.value}>{row.value}</pre>
        </li>
      ))}
    </ul>
  );
};
const ServiceList = ({ ...rest }) => {
  const [data, setData] = useState<Services[]>([]);
  const [loading, setLoading] = useState(false);

  const loadServices = () => {
    setLoading(true);
    return queryService().then((result) => {
      setLoading(false);
      setData(result);
    });
  };

  const numberWithCommas = (x: number) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  };

  const stopService = (masterName: string, instanceName: string) => {
    setLoading(true);
    return mutationStopService(masterName, instanceName).then(() => {
      loadServices();
    });
  };

  const restartService = (masterName: string, instanceName: string) => {
    setLoading(true);
    return mutationRestartService(masterName, instanceName).then(() => {
      loadServices();
    });
  };

  const sortBy =
    (key: string) =>
    (valueA: { [key: string]: any }, valueB: { [key: string]: any }) =>
      valueA[key] < valueB[key] ? -1 : 1;

  useEffect(() => {
    loadServices();
    EventBus.on('serviceCloner:start', loadServices);
    EventBus.on('serviceCloner:stop', loadServices);
    return () => {
      EventBus.remove('serviceCloner:start', loadServices);
      EventBus.remove('serviceCloner:stop', loadServices);
    };
  }, []);

  return (
    <StyledCard {...rest}>
      <CardHeader title="Services" />
      <Divider />
      <PerfectScrollbar>
        <Box minWidth={800}>
          <Table size="small">
            <TableHead>
              <TableRow>
                <TableCell>{loading ? 'Loading' : 'Name'}</TableCell>
                <TableCell>Image</TableCell>
                <TableCell>Environment</TableCell>
                <TableCell>Label</TableCell>
                <TableCell>
                  <Button onClick={loadServices}>
                    <ReplayIcon />
                  </Button>
                </TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {data.map((service) => (
                <React.Fragment key={service.name}>
                  <TableRow hover>
                    <TableCell style={{ verticalAlign: 'top' }}>
                      {service.name}
                    </TableCell>
                    <TableCell style={{ verticalAlign: 'top' }}>
                      {service.image}
                    </TableCell>
                    <TableCell style={{ verticalAlign: 'top' }}>
                      <NameValueList
                        data={service.environments.sort(sortBy('name'))}
                      />
                    </TableCell>
                    <TableCell style={{ verticalAlign: 'top' }}>
                      <NameValueList
                        data={service.labels.sort(sortBy('name'))}
                      />
                    </TableCell>
                    <TableCell />
                  </TableRow>
                  <TableRow>
                    <TableCell
                      style={{ paddingBottom: 0, paddingTop: 0 }}
                      colSpan={4}
                    >
                      <Collapse in timeout="auto" unmountOnExit>
                        <Box margin={1}>
                          <Typography variant="h6" gutterBottom component="div">
                            Instances
                          </Typography>
                          <Table size="small">
                            <TableHead>
                              <TableRow>
                                <TableCell>Instance</TableCell>
                                <TableCell>Index</TableCell>
                                <TableCell>Container Name</TableCell>
                                <TableCell>Ports</TableCell>
                                <TableCell>Filesystem Name</TableCell>
                                <TableCell>Filesystem Mountpoint</TableCell>
                                <TableCell>Filesystem Used</TableCell>
                                <TableCell>Filesystem Available</TableCell>
                                <TableCell>Status</TableCell>
                                <TableCell>Time</TableCell>
                                <TableCell />
                              </TableRow>
                            </TableHead>
                            <TableBody>
                              {service.containers &&
                                service.containers
                                  .sort((container1, container2) =>
                                    container1.instanceIndex <
                                    container2.instanceIndex
                                      ? -1
                                      : 1,
                                  )
                                  .map((container) => (
                                    <TableRow
                                      hover
                                      key={container.containerName}
                                    >
                                      <TableCell>
                                        {container.instanceName}
                                      </TableCell>
                                      <TableCell>
                                        {container.instanceIndex}
                                      </TableCell>
                                      <TableCell>
                                        {container.containerName}
                                      </TableCell>
                                      <TableCell>
                                        {container.exposedPorts.join(', ')}
                                      </TableCell>
                                      <TableCell>
                                        {container.zfsFilesystem &&
                                          container.zfsFilesystem.name}
                                      </TableCell>
                                      <TableCell>
                                        {container.zfsFilesystem &&
                                          container.zfsFilesystem.mountPoint}
                                      </TableCell>
                                      <TableCell align="right">
                                        {container.zfsFilesystem &&
                                          numberWithCommas(
                                            container.zfsFilesystem.used,
                                          )}
                                      </TableCell>
                                      <TableCell align="right">
                                        {container.zfsFilesystem &&
                                          numberWithCommas(
                                            container.zfsFilesystem.available,
                                          )}
                                      </TableCell>
                                      <TableCell>
                                        {container.dockerState}
                                      </TableCell>
                                      <TableCell>
                                        {moment(container.time * 1000).format(
                                          'DD/MM/YYYY HH:mm:ss',
                                        )}
                                      </TableCell>
                                      <TableCell>
                                        {container.instanceName !==
                                          'master' && (
                                          <>
                                            <Button
                                              onClick={() =>
                                                stopService(
                                                  container.masterName,
                                                  container.instanceName,
                                                )
                                              }
                                            >
                                              <DeleteIcon />
                                            </Button>
                                            <Button
                                              onClick={() =>
                                                restartService(
                                                  container.masterName,
                                                  container.instanceName,
                                                )
                                              }
                                            >
                                              <ReplayIcon />
                                            </Button>
                                          </>
                                        )}
                                      </TableCell>
                                    </TableRow>
                                  ))}
                            </TableBody>
                          </Table>
                        </Box>
                      </Collapse>
                    </TableCell>
                  </TableRow>
                </React.Fragment>
              ))}
            </TableBody>
          </Table>
        </Box>
      </PerfectScrollbar>
    </StyledCard>
  );
};

export default ServiceList;
