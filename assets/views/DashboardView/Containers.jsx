import React, {useEffect, useState} from 'react';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';
import PropTypes from 'prop-types';

import {
  Box,
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
import Filesystems from "./Filesystems";


const useStyles = makeStyles(() => ({
  root: {},
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Containers = ({className, ...rest}) => {
  const classes = useStyles();
  const [data, setData] = useState([]);

  useEffect(() => {
    queryContainers().then(setData)
  }, []);

  return (
      <Card
          className={clsx(classes.root, className)}
          {...rest}
      >
        <CardHeader title="Containers"/>
        <Divider/>
        <PerfectScrollbar>
          <Box minWidth={800}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>Name</TableCell>
                  <TableCell>Master</TableCell>
                  <TableCell>Instance</TableCell>
                  <TableCell>Index</TableCell>
                  <TableCell>Filesystem</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {data.map((service) => (
                    <TableRow
                        hover
                        key={service.containerName}
                    >
                      <TableCell>{service.containerName}</TableCell>
                      <TableCell>{service.masterName}</TableCell>
                      <TableCell>{service.instanceName}</TableCell>
                      <TableCell>{service.instanceIndex}</TableCell>
                      <TableCell>{service.zfsFilesystemName}</TableCell>
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
};
Containers.defaultProps = {
  className: ''
}

export default Containers;
