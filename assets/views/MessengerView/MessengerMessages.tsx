import * as React from 'react';
import Checkbox from '@material-ui/core/Checkbox';
import TableCell from '@material-ui/core/TableCell';
import Paper from '@material-ui/core/Paper';
import Table from '@material-ui/core/Table';
import TableBody from '@material-ui/core/TableBody';
import TableContainer from '@material-ui/core/TableContainer';
import Typography from '@material-ui/core/Typography';
import TableHead from '@material-ui/core/TableHead';
import TableRow from '@material-ui/core/TableRow';
import DeleteForeverIcon from '@material-ui/icons/DeleteForever';
import ChevronRightIcon from '@material-ui/icons/ChevronRight';
import ChevronLeftIcon from '@material-ui/icons/ChevronLeft';
// @ts-ignore
import { MuiTriStateCheckbox } from 'mui-tri-state-checkbox';
import {
  Button,
  ButtonGroup,
  Container,
  Grid, makeStyles,
  TextField
} from '@material-ui/core';
import Refresh from '@material-ui/icons/Refresh';

import { useEffect, useState } from 'react';
import queryFailedMessages, { FailedMessageSummary } from '../../graphQL/Messenger/queryFailedMessages';
import queryFailedMessage, {
  FailedMessage,
} from '../../graphQL/Messenger/queryFailedMessage';
import mutationRejectFailedMessage from '../../graphQL/Messenger/mutationRejectFailedMessage';
import mutationRetryFailedMessage from '../../graphQL/Messenger/mutationRetryFailedMessage';
import EventBus from '../../components/EventBus';
import BackTraceDisplay from './BackTraceDisplay';
import FlattenException from './FlattenException';

const useStyles = makeStyles(() => ({
  table: {
    width: '100%',
    overflow: 'auto',
  },
}));

const MessengerMessages = () => {
  const classes = useStyles();
  const [messages, setMessages] = useState<FailedMessageSummary[]>([]);
  const [message, setMessage] = useState<FailedMessage|null>(null);
  const [showDetail, setShowDetail] = useState(false);
  const [filter, setFilter] = useState('');
  const [lowerFilter, setLowerFilter] = useState('');

  const reload = () => {
    queryFailedMessages(50)
      .then((_messages) => {
        setMessages(_messages.map((_message) => {
          _message.checked = false;
          return _message;
        }));
      }).then(() => null);
  };

  const reject = (ids:number[]) => {
    return mutationRejectFailedMessage(
      ids
    ).then(reload);
  };

  const retry = (id:number) => {
    return mutationRetryFailedMessage(id)
      .then(reload);
  };

  const loadMessage = (id: number) => {
    if (message && message.id === id) {
      return Promise.resolve(false);
    }
    return queryFailedMessage(id)
      .then((newMessage) => {
        setShowDetail(true);
        setMessage(newMessage);
      });
  };

  useEffect(() => {
    if (messages.length) {
      loadMessage(messages[0].id);
    }
  }, [messages]);

  useEffect(() => {
    setLowerFilter(filter.toLowerCase());
  }, [filter]);

  useEffect(() => {
    reload();
    EventBus.on('failedMessage:new', reload);
  }, []);

  const numberOfMessagesSelected = messages.filter((_message) => _message.checked).length;
  const allMessageAreChecked = numberOfMessagesSelected === Object.keys(messages).length;
  const oneOfMessageIsChecked = numberOfMessagesSelected > 0 && !allMessageAreChecked;

  const changeCheckAll = () => {
    setMessages(messages.map((_message) => {
      _message.checked = oneOfMessageIsChecked ? false : !allMessageAreChecked;
      return _message;
    }, {}));
  };

  return (
    <Container>
      <Grid container spacing={1}>
        <Grid item xs={showDetail ? 6 : 12}>
          <TableContainer component={Paper} style={{ overflow: 'auto' }}>
            <Table className={classes.table} stickyHeader size="small" aria-label="simple table">
              <TableHead>
                <TableRow>
                  <TableCell colSpan={2}>
                    <Button
                      onClick={reload}
                    >
                      <Refresh />
                    </Button>
                    <MuiTriStateCheckbox
                      edge="start"
                      tabIndex={-1}
                      checked={oneOfMessageIsChecked ? null : allMessageAreChecked}
                      color="primary"
                      onClick={(event:Event) => {
                        changeCheckAll();
                        event.preventDefault();
                      }}
                    />
                    <Button
                      disabled={messages.filter((_message) => _message.checked).length === 0}
                      onClick={() => reject(messages.filter((_message) => _message.checked).map((_message) => _message.id))}
                    >
                      <DeleteForeverIcon />
                    </Button>
                    <Button onClick={() => setShowDetail(!showDetail)}>
                      {showDetail ? <ChevronRightIcon /> : <ChevronLeftIcon />}
                    </Button>
                    <TextField
                      label="Class"
                      style={{ margin: 8 }}
                      onChange={(event) => {
                        setFilter(event.target.value);
                      }}
                    />
                  </TableCell>
                  {!showDetail && <TableCell align="left">Date</TableCell>}
                  {!showDetail && <TableCell align="left">Exception</TableCell>}
                  {!showDetail && <TableCell align="left" />}
                </TableRow>
              </TableHead>
              <TableBody>
                {(filter === '' ? messages : messages.filter((item) => {
                  return (item.exceptionMessage.toLowerCase().indexOf(lowerFilter) !== -1)
                    || (item.className.toLowerCase().indexOf(lowerFilter) !== -1);
                })).map((_message, index) => (
                  <TableRow key={_message.id}>
                    <TableCell align="center">
                      <Button onClick={() => loadMessage(_message.id)}>
                        {_message.id}
                      </Button>
                      <Checkbox
                        size="small"
                        checked={_message.checked}
                        onChange={() => {
                          messages[index].checked = !messages[index].checked;
                          console.log(messages[index].checked);
                          setMessages([...messages]);
                        }}
                      />
                    </TableCell>
                    <TableCell
                      align="left"
                      onClick={() => {
                        if (showDetail) {
                          setShowDetail(false);
                          return;
                        }
                        setShowDetail(true);
                        loadMessage(_message.id);
                      }}
                    >
                      <div style={{ width: '100%', overflow: 'auto' }}>
                        {showDetail ? _message.className.split('\\').pop() : _message.className}
                        {showDetail && <pre>{_message.exceptionMessage}</pre>}
                      </div>
                    </TableCell>
                    {!showDetail && <TableCell align="left">{_message.date}</TableCell>}
                    {!showDetail && <TableCell align="left">{_message.exceptionMessage}</TableCell>}
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </TableContainer>
        </Grid>
        <Grid item xs={showDetail ? 6 : false} style={{ overflow: 'auto' }}>
          {message && (
            <>
              <ButtonGroup color="primary" aria-label="outlined primary button group">
                <Button onClick={() => retry(message.id)}>Retry</Button>
                <Button onClick={() => reject([message.id])}>Delete</Button>
                <Button onClick={() => setShowDetail(false)}>Close</Button>
                <Typography variant="h6" component="div">
                  [
                  {message.id}
                  ]
                  {message.date}
                </Typography>
              </ButtonGroup>
              <Typography variant="h6" component="div">
                {message.className}
              </Typography>
              <Typography variant="h6" component="div">
                <pre>{message.message}</pre>
              </Typography>
              <Typography variant="h6" component="div">
                {message.exceptionMessage}
              </Typography>
              <BackTraceDisplay backtrace={message.backtrace} />
              <FlattenException exception={message.flattenException} message={message} />
            </>
          )}
        </Grid>
      </Grid>
    </Container>
  );
};

export default MessengerMessages;
