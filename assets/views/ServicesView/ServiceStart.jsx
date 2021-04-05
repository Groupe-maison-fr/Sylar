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
import mutationStartService from "../../graphQL/ServiceCloner/mutationStartService";

const useStyles = makeStyles((theme) => ({
    form: {
        '& > *': {
            margin: theme.spacing(1),
            width: '25ch',
        },
    },
}));

const ServiceStart = ({className, ...rest}) => {
    const classes = useStyles();
    const [open, setOpen] = useState(true);
    const [services, setServices] = useState([]);
    const [serviceName, setServiceName] = useState('');
    const [serviceIndex, setServiceIndex] = useState(1);
    const [instanceName, setInstanceName] = useState('');

    useEffect(() => {
        loadServices();
    }, []);

    const createService = () => {
        mutationStartService(
            serviceName,
            serviceIndex,
            instanceName
        ).then(() => {
            setInstanceName('');
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
                title="Service start"
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
                            label="Instance name"
                            value={instanceName}
                            onChange={(event) => setInstanceName(event.target.value)}
                        />
                        <Button
                            disabled={!(serviceName && instanceName)}
                            variant="contained"
                            size="small"
                            endIcon={<SendIcon/>}
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

ServiceStart.propTypes = {
    className: PropTypes.string.isRequired
};
ServiceStart.defaultProps = {
    className: ''
}

export default ServiceStart;
