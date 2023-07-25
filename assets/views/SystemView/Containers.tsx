import React, { useEffect, useState } from 'react';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';
import {
  Box,
  Button,
  Card,
  CardHeader,
  Divider,
  makeStyles,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
} from '@material-ui/core';
import { red } from '@material-ui/core/colors';
import DeleteIcon from '@material-ui/icons/Delete';
import DeleteForeverIcon from '@material-ui/icons/DeleteForever';
import ReplayIcon from '@material-ui/icons/Replay';
import moment from 'moment';
import mutationStopService from '../../graphQL/ServiceCloner/mutationStopService';
import mutationRestartService from '../../graphQL/ServiceCloner/mutationRestartService';
import queryContainers, {
  Container,
} from '../../graphQL/ServiceCloner/queryContainers';
import EventBus from '../../components/EventBus';
import mutationForceDestroyContainer from '../../graphQL/Container/mutationForceDestroyContainer';

const useStyles = makeStyles(() => ({
  root: {},
  actions: {
    justifyContent: 'flex-end',
  },
}));

const Containers = ({ ...rest }) => {
  const classes = useStyles();
  const [containers, setContainers] = useState<Container[]>([]);
  const [loading, setLoading] = useState(false);

  const loadContainers = () => {
    setLoading(true);
    return queryContainers().then((result) => {
      setLoading(false);
      setContainers(result);
    });
  };

  const stopService = (masterName: string, instanceName: string) => {
    setLoading(true);
    return mutationStopService(masterName, instanceName).then(() => {
      loadContainers();
    });
  };

  const restartService = (masterName: string, instanceName: string) => {
    setLoading(true);
    return mutationRestartService(masterName, instanceName).then(() => {
      loadContainers();
    });
  };

  useEffect(() => {
    loadContainers();
    EventBus.on('container:destroy', loadContainers);
    return () => {
      EventBus.remove('container:destroy', loadContainers);
    };
  }, []);

  return (
    <Card className={clsx(classes.root)} {...rest}>
      <CardHeader title="Containers by services" />
      <Divider />
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
                    <ReplayIcon />
                  </Button>
                </TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {containers.map((service) => (
                <TableRow hover key={service.containerName}>
                  <TableCell>{service.containerName}</TableCell>
                  <TableCell>{service.masterName}</TableCell>
                  <TableCell>{service.instanceName}</TableCell>
                  <TableCell>{service.instanceIndex}</TableCell>
                  <TableCell>{service.zfsFilesystemName}</TableCell>
                  <TableCell>
                    {moment(service.time * 1000).format('DD/MM/YYYY HH:mm:ss')}
                  </TableCell>
                  <TableCell>{service.dockerState}</TableCell>
                  <TableCell>
                    {service.instanceName !== 'master' && (
                      <>
                        <Button
                          onClick={() =>
                            stopService(
                              service.masterName,
                              service.instanceName,
                            )
                          }
                        >
                          <DeleteIcon />
                        </Button>
                        <Button
                          onClick={() =>
                            mutationForceDestroyContainer(service.containerName)
                          }
                        >
                          <DeleteForeverIcon style={{ color: red[500] }} />
                        </Button>
                        <Button
                          onClick={() =>
                            restartService(
                              service.masterName,
                              service.instanceName,
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
      </PerfectScrollbar>
    </Card>
  );
};

export default Containers;
