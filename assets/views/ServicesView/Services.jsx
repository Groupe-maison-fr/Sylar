import React, {useEffect, useState} from 'react';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';
import PropTypes from 'prop-types';
import moment from 'moment';

import {
  Box, Button,
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
  Typography
} from '@material-ui/core';
import queryService from "../../graphQL/ServiceCloner/queryServices";
import ReplayIcon from "@material-ui/icons/Replay";
import DeleteIcon from "@material-ui/icons/Delete";
import mutationStopService from '../../graphQL/ServiceCloner/mutationStopService';
import mutationRestartService from '../../graphQL/ServiceCloner/mutationRestartService';
import EventBus from '../../components/EventBus';

const useStyles = makeStyles(() => ({
  root: {},
  value: {
    display: 'inline-block'
  },
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Services = ({className, ...rest}) => {
  const classes = useStyles();
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadServices();
    EventBus.on('serviceCloner:start', loadServices);
    EventBus.on('serviceCloner:stop', loadServices);
    return () =>{
      EventBus.remove('serviceCloner:start', loadServices);
      EventBus.remove('serviceCloner:stop', loadServices);
    }
  }, []);

  const loadServices = () =>{
    setLoading(true);
    return queryService().then((result)=>{
      setLoading(false);
      setData(result);
    })
  }

  const numberWithCommas = (x) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  const stopService = (masterName, instanceName) =>{
    setLoading(true);
    return mutationStopService(masterName, instanceName).then(() => {
      loadServices();
    })
  }

  const restartService = (masterName, instanceName) =>{
    setLoading(true);
    return mutationRestartService(masterName, instanceName).then(() => {
      loadServices();
    })
  }

  const sortBy = (key) => (valueA, valueB) => valueA[key] < valueB[key] ? -1 : 1;

  return (
      <Card
          className={clsx(classes.root, className)}
          {...rest}
      >
        <CardHeader title="Services"/>
        <Divider/>
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
                      <ReplayIcon/>
                    </Button>
                  </TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {data.map((service) => (<React.Fragment key={service.name}>
                    <TableRow
                        hover
                    >
                      <TableCell style={{ verticalAlign: 'top' }}>{service.name}</TableCell>
                      <TableCell style={{ verticalAlign: 'top' }}>{service.image}</TableCell>
                      <TableCell style={{ verticalAlign: 'top' }}>
                        <ul>
                          {service.environments.sort(sortBy('name')).map((environment) => <li key={environment.name}>{environment.name}: <pre className={classes.value}>{environment.value}</pre></li>)}
                        </ul>
                      </TableCell>
                      <TableCell style={{ verticalAlign: 'top' }}>
                        <ul>
                          {service.labels.sort(sortBy('name')).map((label) => <li key={label.name}>{label.name}: <pre className={classes.value}>{label.value}</pre></li>)}
                        </ul>
                      </TableCell>
                      <TableCell></TableCell>
                    </TableRow>
                    <TableRow>
                      <TableCell style={{ paddingBottom: 0, paddingTop: 0 }} colSpan={4}>
                        <Collapse in={true} timeout="auto" unmountOnExit>
                          <Box margin={1}>
                            <Typography variant="h6" gutterBottom component="div">
                              Services
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
                                  <TableCell>
                                  </TableCell>
                                </TableRow>
                              </TableHead>
                              <TableBody>
                                {service.containers && service.containers.map((service) => (
                                    <TableRow
                                        hover
                                        key={service.containerName}
                                    >
                                      <TableCell>{service.instanceName}</TableCell>
                                      <TableCell>{service.instanceIndex}</TableCell>
                                      <TableCell>{service.containerName}</TableCell>
                                      <TableCell>{service.exposedPorts.join(', ')}</TableCell>
                                      <TableCell>{service.zfsFilesystem && service.zfsFilesystem.name}</TableCell>
                                      <TableCell>{service.zfsFilesystem && service.zfsFilesystem.mountPoint}</TableCell>
                                      <TableCell align="right">{service.zfsFilesystem && numberWithCommas(service.zfsFilesystem.used)}</TableCell>
                                      <TableCell align="right">{service.zfsFilesystem && numberWithCommas(service.zfsFilesystem.available)}</TableCell>
                                      <TableCell>{service.dockerState}</TableCell>
                                      <TableCell>moment(service.time * 1000).format('DD/MM/YYYY HH:mm:ss')}</TableCell>
                                      <TableCell>
                                        {service.instanceName !=="master" && (
                                            <>
                                              <Button onClick={() => stopService(service.masterName, service.instanceName)}>
                                                <DeleteIcon />
                                              </Button>
                                              <Button onClick={() => restartService(service.masterName, service.instanceName)}>
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
                </React.Fragment>))}
              </TableBody>
            </Table>
          </Box>
        </PerfectScrollbar>
      </Card>
  );
};

Services.propTypes = {
  className: PropTypes.string.isRequired
};
Services.defaultProps = {
  className: ''
}

export default Services;
