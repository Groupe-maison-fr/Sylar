import React, { useEffect, useState } from 'react';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';

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
import ReplayIcon from '@material-ui/icons/Replay';
import DeleteIcon from '@material-ui/icons/Delete';
import queryReservations, { Reservation } from '../../graphQL/Reservation/queryReservations';
import mutationDeleteReservation from '../../graphQL/Reservation/mutationDeleteReservation';

const useStyles = makeStyles(() => ({
  root: {},
  value: {
    display: 'inline-block'
  },
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Reservations = ({ refresh, ...rest }:{refresh: string}) => {
  const classes = useStyles();
  const [data, setData] = useState<Reservation[]>([]);
  const [loading, setLoading] = useState(false);

  const loadReservations = () => {
    setLoading(true);
    return queryReservations().then((result) => {
      setData(result);
      setLoading(false);
    });
  };

  const deleteReservation = (reservation:Reservation) => {
    setLoading(true);
    return mutationDeleteReservation(reservation.service, reservation.name, reservation.index).then(() => {
      setLoading(false);
      loadReservations().then(() => {});
    });
  };

  useEffect(() => {
    loadReservations().then(() => {});
  }, []);

  useEffect(() => {
    loadReservations().then(() => {});
  }, [refresh]);

  return (
    <Card
      className={clsx(classes.root)}
      {...rest}
    >
      <CardHeader title="Reservations" />
      <Divider />
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
                  <Button onClick={() => deleteReservation(reservation)}>
                    <DeleteIcon />
                  </Button>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </Box>
      </PerfectScrollbar>
    </Card>
  );
};

export default Reservations;
