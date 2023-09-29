import React, { useEffect, useState } from 'react';
import { styled } from '@mui/material/styles';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';
import {
  Box,
  Button,
  Card,
  CardHeader,
  Divider,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
  Tooltip,
} from '@mui/material';
import { red } from '@mui/material/colors';
import DeleteIcon from '@mui/icons-material/Delete';
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import ReplayIcon from '@mui/icons-material/Replay';
import moment from 'moment';
import mutationStopService from '../../graphQL/ServiceCloner/mutationStopService';
import mutationRestartService from '../../graphQL/ServiceCloner/mutationRestartService';
import queryContainers from '../../graphQL/ServiceCloner/queryContainers';
import EventBus from '../../components/EventBus';
import mutationForceDestroyContainer from '../../graphQL/Container/mutationForceDestroyContainer';
import ago from '../../components/ago';
import { ContainersQuery } from '../../gql/graphql';
import { useAuthenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

const PREFIX = 'Containers';

const classes = {
  root: `${PREFIX}-root`,
  actions: `${PREFIX}-actions`,
};

const StyledCard = styled(Card)(() => ({
  [`&.${classes.root}`]: {},

  [`& .${classes.actions}`]: {
    justifyContent: 'flex-end',
  },
}));

const Containers = ({ ...rest }) => {
  const { client } = useAuthenticatedClient();
  const [containers, setContainers] = useState<ContainersQuery['containers']>(
    [],
  );
  const [loading, setLoading] = useState(false);

  const loadContainers = () => {
    setLoading(true);
    return queryContainers(client).then((result) => {
      setLoading(false);
      setContainers(result);
    });
  };

  const stopService = (masterName: string, instanceName: string) => {
    setLoading(true);
    return mutationStopService(client, masterName, instanceName).then(() => {
      loadContainers();
    });
  };

  const restartService = (masterName: string, instanceName: string) => {
    setLoading(true);
    return mutationRestartService(client, masterName, instanceName).then(() => {
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
    <StyledCard className={clsx(classes.root)} {...rest}>
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
              {containers.map((container) => (
                <TableRow hover key={container.containerName}>
                  <TableCell>{container.containerName}</TableCell>
                  <TableCell>{container.masterName}</TableCell>
                  <TableCell>{container.instanceName}</TableCell>
                  <TableCell>{container.instanceIndex}</TableCell>
                  <TableCell>{container.zfsFilesystemName}</TableCell>
                  <TableCell>
                    <Tooltip title={ago(container.uptime)}>
                      <span>
                        {moment(container.time * 1000).format(
                          'DD/MM/YYYY HH:mm:ss',
                        )}
                      </span>
                    </Tooltip>
                  </TableCell>
                  <TableCell>{container.dockerState}</TableCell>
                  <TableCell>
                    {container.instanceName !== 'master' && (
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
                            mutationForceDestroyContainer(
                              client,
                              container.containerName,
                            )
                          }
                        >
                          <DeleteForeverIcon style={{ color: red[500] }} />
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
      </PerfectScrollbar>
    </StyledCard>
  );
};

export default Containers;
