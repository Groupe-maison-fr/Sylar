import React, {useEffect, useState} from 'react';
import clsx from 'clsx';
import PropTypes from 'prop-types';

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
import ExpandMoreIcon from '@material-ui/icons/ExpandMore'
import ExpandLessIcon from '@material-ui/icons/ExpandLess'

import queryServiceList from "../../graphQL/ServiceCloner/queryServiceList";
import mutationAddReservation from "../../graphQL/Reservation/mutationAddReservation";
import {useSnackbar} from "notistack";

const useStyles = makeStyles((theme) => ({
    form: {
        '& > *': {
            margin: theme.spacing(1),
            width: '25ch',
        },
    },
}));

const ReservationAdd = ({className,onAdd, ...rest}) => {
    const classes = useStyles();
    const [open, setOpen] = useState(true);
    const [services, setServices] = useState([]);
    const [serviceName, setServiceName] = useState('');
    const [serviceIndex, setServiceIndex] = useState(1);
    const [reservationName, setReservationName] = useState('');
    const {enqueueSnackbar} = useSnackbar();


    useEffect(() => {
        loadServices();
    }, []);

    const createService = () => {
        mutationAddReservation(
            serviceName,
            reservationName,
            serviceIndex,
        ).then((response) => {
            if(response.message){
                enqueueSnackbar(response.message);
                return;
            }
            setReservationName('');
            onAdd(`${Date.now()}`);
        })
    }

    const loadServices = () => {
        queryServiceList().then((serviceList) => {
            setServices(serviceList);
            if (serviceList.length) {
                setServiceName(serviceList[0].name);
            }
        });
    }
    return (
        <Card
            className={clsx(classes.root, className)}
            {...rest}
        >
            <CardHeader
                title="Add Reservation"
                action={
                    <IconButton onClick={() => setOpen(!open)}>
                        {open ? <ExpandLessIcon/> : <ExpandMoreIcon/>}
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
                            onChange={(event) => setServiceIndex(event.target.value)}
                            InputLabelProps={{
                                shrink: true
                            }}
                            InputProps={{
                                inputProps: {min: 1, max: 100}
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

ReservationAdd.propTypes = {
    className: PropTypes.string.isRequired,
    onAdd: PropTypes.func.isRequired,
};
ReservationAdd.defaultProps = {
    className: ''
}

export default ReservationAdd;
