import React, { useEffect, useState } from 'react';
import { styled } from '@mui/material/styles';
import clsx from 'clsx';
import PerfectScrollbar from 'react-perfect-scrollbar';
import ReplayIcon from '@mui/icons-material/Replay';
import {
  Box,
  Button,
  Card,
  CardHeader,
  Divider,
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableRow,
} from '@mui/material';
import { red } from '@mui/material/colors';
import DeleteForeverIcon from '@mui/icons-material/DeleteForever';
import moment from 'moment';
import queryFilesystem, {
  Filesystem,
} from '../../graphQL/ServiceCloner/queryFilesystem';
import mutationForceDestroyFilesystem from '../../graphQL/FileSystem/mutationForceDestroyFilesystem';
import EventBus from '../../components/EventBus';

const PREFIX = 'Filesystems';

const classes = {
  root: `${PREFIX}-root`,
  actions: `${PREFIX}-actions`,
};

const StyledCard = styled(Card)(() => ({
  [`&.${classes.root}`]: {},

  [`& .${classes.actions}`]: {
    justifyContent: 'flex-end',
  },
}));

const Filesystems = ({ ...rest }) => {
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
    <StyledCard className={clsx(classes.root)} {...rest}>
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
    </StyledCard>
  );
};

export default Filesystems;
