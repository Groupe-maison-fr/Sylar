import React, {useEffect,useState} from 'react';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';
import PropTypes from 'prop-types';
import ReplayIcon from '@material-ui/icons/Replay';
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
import queryFilesystem from "../../graphQL/ServiceCloner/queryFilesystem";
import Iframe from '../../components/Iframe';

const useStyles = makeStyles(() => ({
  root: {},
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Grafana = ({className, ...rest}) => {
  const classes = useStyles();
  const [loading, setLoading] = useState(false);

  useEffect(() => {
  }, []);

  return (
      <Card
          className={clsx(classes.root, className)}
          {...rest}
      >
        <CardHeader title="Grafana"/>
        <Divider/>

      </Card>
  );
};

Grafana.propTypes = {
  className: PropTypes.string.isRequired
};
Grafana.defaultProps = {
  className: ''
}

export default Grafana;
