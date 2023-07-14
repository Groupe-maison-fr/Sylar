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
import queryReservations from "../../graphQL/Reservation/queryReservations";
import mutationRestartService from '../../graphQL/ServiceCloner/mutationRestartService';
import EventBus from '../../components/EventBus';
import ReplayIcon from "@material-ui/icons/Replay";

const useStyles = makeStyles(() => ({
  root: {},
  value: {
    display: 'inline-block'
  },
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Reservations = ({className,refresh, ...rest}) => {
  const classes = useStyles();
  const [data, setData] = useState([]);
  const [loading, setLoading] = useState(false);

  const loadReservations = () =>{
    setLoading(true);
    return queryReservations().then((result)=>{
      setData(result);
      setLoading(false);
    });
  }

  useEffect(()=>{
    loadReservations();
  },[]);
  useEffect(()=>{
    loadReservations();
  },[refresh]);

  return (
      <Card
          className={clsx(classes.root, className)}
          {...rest}
      >
        <CardHeader title="Reservations"/>
        <Divider/>
        <PerfectScrollbar>
          <Box minWidth={800}>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>{loading ? 'Loading' : 'Service'}</TableCell>
                  <TableCell>Name</TableCell>
                  <TableCell>Index</TableCell>
                  <TableCell>
                    <Button onClick={loadReservations}>
                      <ReplayIcon />
                    </Button>
                  </TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {data.map((reservation) => (
                    <TableRow hover key={`${reservation.service}-${reservation.name}`}>
                      <TableCell style={{ verticalAlign: 'top' }}>{reservation.service}</TableCell>
                      <TableCell style={{ verticalAlign: 'top' }}>{reservation.name}</TableCell>
                      <TableCell style={{ verticalAlign: 'top' }}>{reservation.index}</TableCell>
                    </TableRow>
                ))}
              </TableBody>
            </Table>
          </Box>
        </PerfectScrollbar>
      </Card>
  );
};

Reservations.propTypes = {
  className: PropTypes.string.isRequired,
  refresh: PropTypes.string.isRequired
};
Reservations.defaultProps = {
  className: '',
  refresh: ''
}

export default Reservations;
