import React, {useEffect, useState} from 'react';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';
import PropTypes from 'prop-types';
import {
  Box, Button,
  Card,
  CardHeader,
  Divider,
  makeStyles,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow
} from '@material-ui/core';
import queryContainers from "../../graphQL/ServiceCloner/queryContainers";
import { red } from '@material-ui/core/colors';
import DeleteIcon from '@material-ui/icons/Delete';
import DeleteForeverIcon from '@material-ui/icons/DeleteForever';
import mutationStopService from "../../graphQL/ServiceCloner/mutationStopService";
import mutationRestartService from "../../graphQL/ServiceCloner/mutationRestartService";
import ReplayIcon from "@material-ui/icons/Replay";
import moment from 'moment';
import EventBus from '../../components/EventBus';
import mutationForceDestroyContainer from '../../graphQL/Container/mutationForceDestroyContainer';

const useStyles = makeStyles(() => ({
  root: {},
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Containers = ({className, ...rest}) => {
  const classes = useStyles();
  const [containers, setContainers] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadContainers();
    EventBus.on('container:destroy', loadContainers);
    return () => {
      EventBus.remove('container:destroy', loadContainers);
    }

  }, []);

  const loadContainers = () => {
    setLoading(true);
    return queryContainers().then((result)=>{
      setLoading(false);
      setContainers(result);
    });
  }

  const stopService = (masterName, instanceName) =>{
    setLoading(true);
    return mutationStopService(masterName, instanceName).then(() => {
      loadContainers();
    });
  }

  const restartService = (masterName, instanceName) =>{
    setLoading(true);
    return mutationRestartService(masterName, instanceName).then(() => {
      loadContainers();
    });
  }

  return (
      <Card
          className={clsx(classes.root, className)}
          {...rest}
      >
        <CardHeader title="Containers by services"/>
        <Divider/>
        <PerfectScrollbar>
          <Box minWidth={800}>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>{loading ? 'Loading' : 'Name'}</TableCell>
                  <TableCell>Master</TableCell>
                  <TableCell>Instance</TableCell>
                  <TableCell>Index</TableCell>
                  <TableCell>Filesystem</TableCell>
                  <TableCell>Time</TableCell>
                  <TableCell>Status</TableCell>
                  <TableCell>
                      <Button onClick={loadContainers}>
                        <ReplayIcon/>
                      </Button>
                  </TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {containers.map((service) => (
                    <TableRow
                        hover
                        key={service.containerName}
                    >
                      <TableCell>{service.containerName}</TableCell>
                      <TableCell>{service.masterName}</TableCell>
                      <TableCell>{service.instanceName}</TableCell>
                      <TableCell>{service.instanceIndex}</TableCell>
                      <TableCell>{service.zfsFilesystemName}</TableCell>
                      <TableCell>{moment(service.time * 1000).format('DD/MM/YYYY HH:mm:ss')}</TableCell>
                      <TableCell>{service.dockerState}</TableCell>
                      <TableCell>
                        {service.instanceName !=="master" && (
                            <>
                              <Button onClick={() => stopService(service.masterName, service.instanceName)}>
                                <DeleteIcon />
                              </Button>
                              <Button onClick={() => mutationForceDestroyContainer(service.containerName)}>
                                <DeleteForeverIcon style={{ color: red[500] }}/>
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
        </PerfectScrollbar>
      </Card>
  );
};

Containers.propTypes = {
  className: PropTypes.string.isRequired
}

Containers.defaultProps = {
  className: ''
}

export default Containers;
