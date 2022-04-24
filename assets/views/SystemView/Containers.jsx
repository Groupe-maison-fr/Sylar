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
import DeleteIcon from '@material-ui/icons/Delete';
import mutationStopService from "../../graphQL/ServiceCloner/mutationStopService";
import mutationRestartService from "../../graphQL/ServiceCloner/mutationRestartService";
import ReplayIcon from "@material-ui/icons/Replay";

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
        <CardHeader title="Containers"/>
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
                  <TableCell>Status</TableCell>
                  <TableCell>Time</TableCell>
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
                      <TableCell>{service.dockerState}</TableCell>
                      <TableCell>{service.time}</TableCell>
                      <TableCell>{service.zfsFilesystemName}</TableCell>
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
