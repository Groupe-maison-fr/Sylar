import React, { useEffect, useState } from 'react';

import {
  Button,
  Collapse,
  IconButton,
  makeStyles,
  MenuItem,
  TextField,
} from '@material-ui/core';

import Card from '@material-ui/core/Card';
import CardHeader from '@material-ui/core/CardHeader';
import CardContent from '@material-ui/core/CardContent';
import SendIcon from '@material-ui/icons/Send';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import ExpandLessIcon from '@material-ui/icons/ExpandLess';

import { useSnackbar } from 'notistack';
import queryServiceList from '../../graphQL/ServiceCloner/queryServiceList';
import mutationAddReservation from '../../graphQL/Reservation/mutationAddReservation';

const useStyles = makeStyles((theme) => ({
  form: {
    '& > *': {
      margin: theme.spacing(1),
      width: '25ch',
    },
  },
}));

const ReservationAdd = ({ onAdd, ...rest }:{onAdd:(id: string)=>void}) => {
  const classes = useStyles();
  const [open, setOpen] = useState(true);
  const [services, setServices] = useState<{name: string}[]>([]);
  const [serviceName, setServiceName] = useState('');
  const [serviceIndex, setServiceIndex] = useState(1);
  const [reservationName, setReservationName] = useState('');
  const { enqueueSnackbar } = useSnackbar();

  const createService = () => {
    mutationAddReservation(
      serviceName,
      reservationName,
      serviceIndex,
    ).then((response) => {
      if (response.message) {
        enqueueSnackbar(response.message);
        return;
      }
      setReservationName('');
      onAdd(`${Date.now()}`);
    });
  };

  const loadServices = () => {
    queryServiceList().then((serviceList) => {
      setServices(serviceList);
      if (serviceList.length) {
        setServiceName(serviceList[0].name);
      }
    });
  };

  useEffect(() => {
    loadServices();
  }, []);

  return (
    <Card
      {...rest}
    >
      <CardHeader
        title="Add Reservation"
        action={(
          <IconButton onClick={() => setOpen(!open)}>
            {open ? <ExpandLessIcon /> : <ExpandMoreIcon />}
          </IconButton>
                  )}
      />
      <Collapse in={open} timeout="auto" unmountOnExit>
        <CardContent>
          <form className={classes.form} noValidate autoComplete="off">
            <TextField
              label="Service"
              select
              value={serviceName}
              onChange={(event) => setServiceName(event.target.value)}
            >
              {services.map((service) => (
                <MenuItem key={service.name} value={service.name}>
                  {service.name}
                </MenuItem>
              ))}
            </TextField>
            <TextField
              label="Index"
              type="number"
              value={serviceIndex}
              onChange={(event) => setServiceIndex(parseInt(event.target.value, 10))}
              InputLabelProps={{
                shrink: true
              }}
              InputProps={{
                inputProps: { min: 1, max: 100 }
              }}
            />
            <TextField
              label="Reservation name"
              value={reservationName}
              onChange={(event) => setReservationName(event.target.value)}
            />
            <Button
              disabled={!(serviceName && reservationName)}
              variant="contained"
              size="small"
              endIcon={<SendIcon />}
              onClick={createService}
            >
              Create
            </Button>
          </form>
        </CardContent>
      </Collapse>
    </Card>
  );
};

export default ReservationAdd;
