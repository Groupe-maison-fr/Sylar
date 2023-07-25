import React, { useEffect, useState } from 'react';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';
import ReplayIcon from '@material-ui/icons/Replay';
import {
  Box,
  Button,
  Card,
  CardHeader,
  Divider,
  makeStyles,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
} from '@material-ui/core';
import { red } from '@material-ui/core/colors';
import DeleteForeverIcon from '@material-ui/icons/DeleteForever';
import moment from 'moment';
import queryFilesystem, {
  Filesystem,
} from '../../graphQL/ServiceCloner/queryFilesystem';
import mutationForceDestroyFilesystem from '../../graphQL/FileSystem/mutationForceDestroyFilesystem';
import EventBus from '../../components/EventBus';

const useStyles = makeStyles(() => ({
  root: {},
  actions: {
    justifyContent: 'flex-end',
  },
}));

const Filesystems = ({ ...rest }) => {
  const classes = useStyles();
  const [fileSystems, setFileSystems] = useState<Filesystem[]>([]);
  const [loading, setLoading] = useState(false);

  const loadFilesystem = () => {
    setLoading(true);
    queryFilesystem().then((result) => {
      setLoading(false);
      setFileSystems(result);
    });
  };

  const numberWithCommas = (x: number) => {
    return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
  };

  useEffect(() => {
    loadFilesystem();
    EventBus.on('filesystem:destroy', loadFilesystem);
    return () => {
      EventBus.remove('filesystem:destroy', loadFilesystem);
    };
  }, []);

  return (
    <Card className={clsx(classes.root)} {...rest}>
      <CardHeader title="Filesystems" />
      <Divider />
      <PerfectScrollbar>
        <Box minWidth={800}>
          <Table size="small">
            <TableHead>
              <TableRow>
                <TableCell align="left">
                  {loading ? 'Loading' : 'Name'}
                </TableCell>
                <TableCell align="left">MountPoint</TableCell>
                <TableCell align="right">Available</TableCell>
                <TableCell align="right">Used</TableCell>
                <TableCell align="right">Used by Dataset</TableCell>
                <TableCell align="right">Creation time</TableCell>
                <TableCell align="right">
                  <Button onClick={loadFilesystem}>
                    <ReplayIcon />
                  </Button>
                </TableCell>
              </TableRow>
            </TableHead>
            <TableBody>
              {fileSystems.map((filesystem) => (
                <TableRow hover key={filesystem.name}>
                  <TableCell align="left">{filesystem.name}</TableCell>
                  <TableCell align="left">{filesystem.mountPoint}</TableCell>
                  <TableCell align="right">
                    {numberWithCommas(filesystem.available)}
                  </TableCell>
                  <TableCell align="right">
                    {numberWithCommas(filesystem.used)}
                  </TableCell>
                  <TableCell align="right">
                    {numberWithCommas(filesystem.usedByDataset)}
                  </TableCell>
                  <TableCell align="right">
                    {moment(filesystem.creationTimestamp * 1000).format(
                      'DD/MM/YYYY HH:mm:ss',
                    )}
                  </TableCell>
                  <TableCell>
                    {filesystem.origin !== '-' && (
                      <Button
                        onClick={() =>
                          mutationForceDestroyFilesystem(filesystem.name)
                        }
                      >
                        <DeleteForeverIcon style={{ color: red[500] }} />
                      </Button>
                    )}
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

export default Filesystems;
