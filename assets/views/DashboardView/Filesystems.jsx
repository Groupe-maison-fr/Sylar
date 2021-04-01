import React, {useEffect,useState} from 'react';
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
import queryFilesystem from "../../graphQL/ServiceCloner/queryFilesystem";

const useStyles = makeStyles(() => ({
  root: {},
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Filesystems = ({className, ...rest}) => {
  const classes = useStyles();
  const [data, setData] = useState([]);

  useEffect(() => {
    queryFilesystem().then(setData)
  }, []);

  return (
      <Card
          className={clsx(classes.root, className)}
          {...rest}
      >
        <CardHeader title="Filesystems"/>
        <Divider/>
        <PerfectScrollbar>
          <Box minWidth={800}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>Name</TableCell>
                  <TableCell>MountPoint</TableCell>
                  <TableCell>Available</TableCell>
                  <TableCell>Used</TableCell>
                  <TableCell>Used by Dataset</TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {data.map((service) => (
                    <TableRow
                        hover
                        key={service.name}
                    >
                      <TableCell>{service.name}</TableCell>
                      <TableCell>{service.mountPoint}</TableCell>
                      <TableCell>{service.available}</TableCell>
                      <TableCell>{service.used}</TableCell>
                      <TableCell>{service.usedByDataset}</TableCell>
                    </TableRow>
                ))}
              </TableBody>
            </Table>
          </Box>
        </PerfectScrollbar>
      </Card>
  );
};

Filesystems.propTypes = {
  className: PropTypes.string.isRequired
};
Filesystems.defaultProps = {
  className: ''
}

export default Filesystems;
