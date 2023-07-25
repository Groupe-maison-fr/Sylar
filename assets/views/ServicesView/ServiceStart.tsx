import React, { useEffect, useState } from 'react';

import {
  Button,
  Collapse,
  IconButton,
  ListItemText,
  makeStyles,
  MenuItem,
  TextField,
  Typography,
} from '@material-ui/core';

import Card from '@material-ui/core/Card';
import CardHeader from '@material-ui/core/CardHeader';
import CardContent from '@material-ui/core/CardContent';
import SendIcon from '@material-ui/icons/Send';
import RestartIcon from '@material-ui/icons/Loop';
import ExpandMoreIcon from '@material-ui/icons/ExpandMore';
import ExpandLessIcon from '@material-ui/icons/ExpandLess';

import queryServiceList from '../../graphQL/ServiceCloner/queryServiceList';
import mutationStartService from '../../graphQL/ServiceCloner/mutationStartService';
import EventBus from '../../components/EventBus';
import queryReservations from '../../graphQL/Reservation/queryReservations';
import mutationRestartService from '../../graphQL/ServiceCloner/mutationRestartService';

const indexRange = [...Array(30).keys()].map((index) => index + 1);

const useStyles = makeStyles((theme) => ({
  form: {
    '& > *': {
      margin: theme.spacing(1),
      width: '25ch',
    },
  },
}));
type indexesByServiceType = { [serviceAndIndex: string]: string };

const InstanceName = ({
  index,
  serviceName,
  instancesByService,
  reservationsByService,
}: {
  index: number | string;
  serviceName: string;
  instancesByService: indexesByServiceType;
  reservationsByService: indexesByServiceType;
}) => {
  if (index === 'auto') {
    return 'Auto';
  }
  const instancesByServiceElement =
    instancesByService[`${serviceName}-${index}`];
  if (instancesByServiceElement) {
    return (
      <>
        <ListItemText>
          {`[${index}] `}
          <i>{instancesByServiceElement} </i>
        </ListItemText>
        <Typography variant="body2">Running</Typography>
      </>
    );
  }
  const reservationsByServiceElement =
    reservationsByService[`${serviceName}-${index}`];
  if (reservationsByServiceElement) {
    return (
      <>
        <ListItemText>
          {`[${index}] `}
          <b>{reservationsByServiceElement}</b>
        </ListItemText>
        <Typography variant="body2">Reserved</Typography>
      </>
    );
  }

  return `[${index}]`;
};

const ServiceStart = ({ ...rest }) => {
  const classes = useStyles();
  const [open, setOpen] = useState(true);
  const [services, setServices] = useState<{ name: string }[]>([]);
  const [reservationsByService, setReservationsByService] =
    useState<indexesByServiceType>({});
  const [instancesByService, setInstancesByService] =
    useState<indexesByServiceType>({});
  const [serviceName, setServiceName] = useState('');
  const [serviceIndex, setServiceIndex] = useState<number | 'auto'>('auto');
  const [instanceName, setInstanceName] = useState('');

  const createService = () => {
    mutationStartService(
      serviceName,
      serviceIndex === 'auto' ? null : serviceIndex,
      instanceName,
    ).then(() => {
      setInstanceName('');
    });
  };

  const onChangeIndex = (indexValue: string) => {
    if (indexValue === 'auto') {
      setServiceIndex('auto');
      return;
    }
    const index = parseInt(indexValue, 10);
    setServiceIndex(index);

    if (instancesByService[`${serviceName}-${index}`]) {
      setInstanceName(instancesByService[`${serviceName}-${index}`]);
      return;
    }

    if (reservationsByService[`${serviceName}-${index}`]) {
      setInstanceName(reservationsByService[`${serviceName}-${index}`]);
      return;
    }

    setInstanceName('');
  };

  const loadServices = () => {
    Promise.all([queryServiceList(), queryReservations()]).then(
      ([serviceList, reservations]) => {
        setServices(serviceList);
        if (serviceList.length) {
          setServiceName(serviceList[0].name);
        }
        setInstancesByService(
          serviceList.reduce((accumulator: indexesByServiceType, service) => {
            service.containers.reduce((containersAccumulator, container) => {
              containersAccumulator[
                `${service.name}-${container.instanceIndex}`
              ] = container.instanceName;
              return containersAccumulator;
            }, accumulator);
            return accumulator;
          }, {}),
        );
        setReservationsByService(
          reservations.reduce(
            (accumulator: indexesByServiceType, reservation) => {
              accumulator[`${reservation.service}-${reservation.index}`] =
                reservation.name;
              return accumulator;
            },
            {},
          ),
        );
      },
    );
  };

  useEffect(() => {
    loadServices();
    EventBus.on('serviceCloner:start', loadServices);
    EventBus.on('serviceCloner:stop', loadServices);
    return () => {
      EventBus.remove('serviceCloner:start', loadServices);
      EventBus.remove('serviceCloner:stop', loadServices);
    };
  }, []);

  useEffect(() => {
    loadServices();
  }, []);

  return (
    <Card {...rest}>
      <CardHeader
        title="Service start"
        action={
          <IconButton onClick={() => setOpen(!open)}>
            {open ? <ExpandLessIcon /> : <ExpandMoreIcon />}
          </IconButton>
        }
      />
      <Collapse in={open} timeout="auto" unmountOnExit>
        <CardContent style={{ height: '7vw' }}>
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
              select
              type="number"
              value={serviceIndex}
              onChange={(event) => onChangeIndex(event.target.value)}
              InputLabelProps={{
                shrink: true,
              }}
            >
              {['auto', ...indexRange].map((index) => (
                <MenuItem
                  // disabled={!!instancesByService[`${serviceName}-${index}`]}
                  key={index}
                  value={index}
                >
                  <InstanceName
                    index={index}
                    instancesByService={instancesByService}
                    reservationsByService={reservationsByService}
                    serviceName={serviceName}
                  />
                </MenuItem>
              ))}
            </TextField>
            <TextField
              label="Instance name"
              value={instanceName}
              onChange={(event) => setInstanceName(event.target.value)}
            />
            <Button
              disabled={!(serviceName && instanceName)}
              variant="contained"
              size="small"
              endIcon={
                instancesByService[`${serviceName}-${serviceIndex}`] ? (
                  <RestartIcon />
                ) : (
                  <SendIcon />
                )
              }
              onClick={() => {
                if (instancesByService[`${serviceName}-${serviceIndex}`]) {
                  mutationRestartService(
                    serviceName,
                    instancesByService[`${serviceName}-${serviceIndex}`],
                  );
                  return;
                }
                createService();
              }}
            >
              {instancesByService[`${serviceName}-${serviceIndex}`]
                ? 'Restart'
                : 'Create'}
            </Button>
          </form>
        </CardContent>
      </Collapse>
    </Card>
  );
};

export default ServiceStart;
