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
import queryService from "../../graphQL/ServiceCloner/queryServices";
import Containers from "./Containers";

const useStyles = makeStyles(() => ({
  root: {},
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Services = ({className, ...rest}) => {
  const classes = useStyles();
  const [data, setData] = useState([]);
  useEffect(() => {
    queryService().then(setData)
  }, []);
  return (
      <Card
          className={clsx(classes.root, className)}
          {...rest}
      >
        <CardHeader title="Services"/>
        <Divider/>
        <PerfectScrollbar>
          <Box minWidth={800}>
            <Table>
              <TableHead>
                <TableRow>
                  <TableCell>
                    Name
                  </TableCell>
                  <TableCell>
                    Image
                  </TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {data.map((service) => (
                    <TableRow
                        hover
                        key={service.name}
                    >
                      <TableCell>
                        {service.name}
                      </TableCell>
                      <TableCell>
                        {service.image}
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

Services.propTypes = {
  className: PropTypes.string.isRequired
};
Services.defaultProps = {
  className: ''
}

export default Services;
