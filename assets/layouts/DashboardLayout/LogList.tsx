import React from 'react';
import { Box, Chip, Grid, styled } from '@mui/material';
import Paper from '@mui/material/Paper';
import { useLogList } from '../../Context/LogListContext';
import { LogColor } from '../../components/Logging';

const Item = styled(Paper)(({ theme }) => ({
  backgroundColor: theme.palette.background.default,
  ...theme.typography.body2,
  padding: theme.spacing(1),
  textAlign: 'left',
  color: theme.palette.text.secondary,
}));
const LogList = () => {
  const { logListContextData } = useLogList();
  return (
    <Grid container spacing={2}>
      {logListContextData.list.map((message) => (
        <Grid key={message.id} item xs={12}>
          <Item>
            <Chip label={message.moment.format('HH:mm:ss')} color="primary" />
            <Chip label={message.channel} color="secondary" />
            <Chip
              label={message.level_name}
              style={{
                backgroundColor:
                  LogColor[message.level_name as keyof typeof LogColor],
              }}
            />
            <Box style={{ width: '100%', overflowX: 'auto' }}>
              <pre style={{ whiteSpace: 'pre-wrap' }}>{message.message}</pre>
            </Box>
          </Item>
        </Grid>
      ))}
    </Grid>
  );
};

export default LogList;
