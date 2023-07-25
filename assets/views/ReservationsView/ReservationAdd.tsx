import React, { useEffect, useState } from 'react';

import { styled } from '@mui/material/styles';

import {
  Button,
  Collapse,
  IconButton,
  MenuItem,
  TextField,
} from '@mui/material';

import Card from '@mui/material/Card';
import CardHeader from '@mui/material/CardHeader';
import CardContent from '@mui/material/CardContent';
import SendIcon from '@mui/icons-material/Send';
import ExpandMoreIcon from '@mui/icons-material/ExpandMore';
import ExpandLessIcon from '@mui/icons-material/ExpandLess';

import { useSnackbar } from 'notistack';
import queryServiceList from '../../graphQL/ServiceCloner/queryServiceList';
import mutationAddReservation from '../../graphQL/Reservation/mutationAddReservation';

const PREFIX = 'ReservationAdd';

const classes = {
  form: `${PREFIX}-form`,
};

const StyledCard = styled(Card)(({ theme }) => ({
  [`& .${classes.form}`]: {
    '& > *': {
      margin: theme.spacing(1),
      width: '25ch',
    },
  },
}));

const ReservationAdd = ({
  onAdd,
  ...rest
}: {
  onAdd: (id: string) => void;
}) => {
  const [open, setOpen] = useState(true);
  const [services, setServices] = useState<{ name: string }[]>([]);
  const [serviceName, setServiceName] = useState('');
  const [serviceIndex, setServiceIndex] = useState(1);
  const [reservationName, setReservationName] = useState('');
  const { enqueueSnackbar } = useSnackbar();

  const createService = () => {
    mutationAddReservation(serviceName, reservationName, serviceIndex).then(
      (response) => {
        if (response.message) {
          enqueueSnackbar(response.message);
          return;
        }
        setReservationName('');
        onAdd(`${Date.now()}`);
      },
    );
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
    <StyledCard {...rest}>
      <CardHeader
        title="Add Reservation"
        action={
          <IconButton onClick={() => setOpen(!open)} size="large">
            {open ? <ExpandLessIcon /> : <ExpandMoreIcon />}
          </IconButton>
        }
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
              onChange={(event) =>
                setServiceIndex(parseInt(event.target.value, 10))
              }
              InputLabelProps={{
                shrink: true,
              }}
              InputProps={{
                inputProps: { min: 1, max: 100 },
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
    </StyledCard>
  );
};

export default ReservationAdd;
