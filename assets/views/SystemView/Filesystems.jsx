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

const useStyles = makeStyles(() => ({
  root: {},
  actions: {
    justifyContent: 'flex-end'
  }
}));

const Filesystems = ({className, ...rest}) => {
  const classes = useStyles();
  const [fileSystems, setFileSystems] = useState([]);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    loadFilesystem();
  }, []);

  const loadFilesystem = () => {
    setLoading(true);
    queryFilesystem().then((result) => {
      setLoading(false);
      setFileSystems(result);
    })
  }

  const numberWithCommas = (x) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  }

  return (
      <Card
          className={clsx(classes.root, className)}
          {...rest}
      >
        <CardHeader title="Filesystems"/>
        <Divider/>
        <PerfectScrollbar>
          <Box minWidth={800}>
            <Table size="small">
              <TableHead>
                <TableRow>
                  <TableCell>Name</TableCell>
                  <TableCell>MountPoint</TableCell>
                  <TableCell>Available</TableCell>
                  <TableCell>Used</TableCell>
                  <TableCell>Used by Dataset</TableCell>
                  <TableCell>
                    <Button onClick={loadFilesystem}>
                      <ReplayIcon/>
                    </Button>
                  </TableCell>
                </TableRow>
              </TableHead>
              <TableBody>
                {loading && (
                    <TableRow
                        hover
                    >
                      <TableCell colSpan={6}>loading</TableCell>
                    </TableRow>
                )}
                {!loading && fileSystems.map((service) => (
                    <TableRow
                        hover
                        key={service.name}
                    >
                      <TableCell align="left">{service.name}</TableCell>
                      <TableCell align="left">{service.mountPoint}</TableCell>
                      <TableCell align="right">{numberWithCommas(service.available)}</TableCell>
                      <TableCell align="right">{numberWithCommas(service.used)}</TableCell>
                      <TableCell align="right">{numberWithCommas(service.usedByDataset)}</TableCell>
                      <TableCell align="right">&nbsp;</TableCell>
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
