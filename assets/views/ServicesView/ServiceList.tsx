import React, { useEffect, useState } from 'react';
import PerfectScrollbar from 'react-perfect-scrollbar';
import moment from 'moment';

import {
  Box,
  Button,
  Card,
  CardHeader,
  Collapse,
  Divider,
  makeStyles,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  Typography,
} from '@material-ui/core';
import ReplayIcon from '@material-ui/icons/Replay';
import DeleteIcon from '@material-ui/icons/Delete';
import queryService, {
  Services,
} from '../../graphQL/ServiceCloner/queryServices';
import mutationStopService from '../../graphQL/ServiceCloner/mutationStopService';
import mutationRestartService from '../../graphQL/ServiceCloner/mutationRestartService';
import EventBus from '../../components/EventBus';

const useStyles = makeStyles(() => ({
  root: {},
  value: {
    display: 'inline-block',
  },
  actions: {
    justifyContent: 'flex-end',
  },
}));

const ServiceList = ({ ...rest }) => {
  const classes = useStyles();
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
    <Card {...rest}>
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
                      <ul>
                        {service.environments
                          .sort(sortBy('name'))
                          .map((environment) => (
                            <li key={environment.name}>
                              {environment.name}:{' '}
                              <pre className={classes.value}>
                                {environment.value}
                              </pre>
                            </li>
                          ))}
                      </ul>
                    </TableCell>
                    <TableCell style={{ verticalAlign: 'top' }}>
                      <ul>
                        {service.labels.sort(sortBy('name')).map((label) => (
                          <li key={label.name}>
                            {label.name}:{' '}
                            <pre className={classes.value}>{label.value}</pre>
                          </li>
                        ))}
                      </ul>
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
    </Card>
  );
};

export default ServiceList;
