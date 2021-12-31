import * as React from 'react';
import {withStyles} from '@material-ui/core/styles';
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
import { MuiTriStateCheckbox } from 'mui-tri-state-checkbox'
import {
    Button,
    ButtonGroup,
    Container,
    Grid,
    TextField
} from "@material-ui/core";
import Refresh from "@material-ui/icons/Refresh";
import queryFailedMessages from "../../graphql/Messenger/queryFailedMessages.js";
import queryFailedMessage from "../../graphql/Messenger/queryFailedMessage.js";
import mutationRejectFailedMessage from "../../graphql/Messenger/mutationRejectFailedMessage.js";
import mutationRetryFailedMessage from "../../graphql/Messenger/mutationRetryFailedMessage.js";

const styles = theme => ({
    table: {
        width: '100%',
        overflow: 'auto',
    },
    backtraceNamespace: {
        color:'red'
    },
    backtraceShortClass: {
        color:'cyan',
        whiteSpace: 'nowrap'
    },
    backtraceFunction: {
        color:'green'
    },
    backtraceLine: {
        color:'purple'
    }
});

class MessengerMessages extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            messages: [],
            message: null,
            showDetail:false,
            filter:'',
        }
        this.reload = this.reload.bind(this)
        this.loadMessage = this.loadMessage.bind(this)
        this.reject = this.reject.bind(this)
    }

    componentDidMount() {
        this.reload();
    }

    reload() {
        return queryFailedMessages( 50)
            .then((messages) => {
                return this.setState({
                    messages: messages.map((message)=>{
                        message.checked=false;
                        return message;
                    })
                },()=>{
                    if (messages.length) {
                        this.loadMessage(messages[0].id);
                    }
                });
            })
    }

    reject(ids) {
        return mutationRejectFailedMessage(
            ids
        ).then(() => {
            return this.reload();
        });
    }

    retry(id) {
        return mutationRetryFailedMessage(
            id
        ).then(() => {
            return this.reload();
        });
    }

    loadMessage(id) {
        const {message} = this.state;
        console.log(message && message.id, id,message && (message.id === id));
        if(message && message.id === id){
            return Promise.resolve(false);
        }
        return queryFailedMessage(id)
            .then((message) => {
                this.setState({
                    showDetail: true,
                    message
                });
            })
    }

    render() {
        const {classes} = this.props;
        const {messages, message, showDetail, filter} = this.state;
        const lowerFilter = filter.toLowerCase();
        const filteredMessages = filter === '' ? messages : messages.filter((item) => {
            return (item.exceptionMessage.toLowerCase().indexOf(lowerFilter) !== -1) ||
            (item.className.toLowerCase().indexOf(lowerFilter) !== -1)
        });

        const numberOfMessagesSelected = Object.keys(filteredMessages).filter((messageId) => filteredMessages[messageId].checked).length;
        const allMessageAreChecked = numberOfMessagesSelected === Object.keys(filteredMessages).length ;
        const oneOfMessageIsChecked = numberOfMessagesSelected > 0 && !allMessageAreChecked;

        const changeCheckAll = () =>{
            this.setState({
                filteredMessages: filteredMessages.map((message) => {
                    message.checked = oneOfMessageIsChecked ? false : !allMessageAreChecked;
                    return message;
                }, {})
            });
        }

        const BackTraceArgument = (props) => {
            if (props.argument === undefined) {
                return 'undefined';
            }
            if (props.argument.map) {
                return <ul>{props.argument.map((argument, index) => <BackTraceArgument
                    key={`_$${index}`}
                    argument={argument}
                />)}</ul>
            }
            return <li>
                {props.argument.value || '?'} ({props.argument.type})
            </li>
        }

        const FunctionCall = (props) => {
            const call = props.call;
            if (call.namespace === '' && call.short_class === '' && call.function === '' && call.type === '') {
                return `${call.file.split('/').pop()} (${call.line})`;
            }
            return <>
                {call.namespace && <span className={classes.backtraceNamespace}>{call.namespace}\</span>}
                {call.short_class && <span className={classes.backtraceShortClass}>{call.short_class}&nbsp;</span>}
                {call.function && <span className={classes.backtraceFunction}>{call.function}</span>}&nbsp;<span className={classes.backtraceLine}>({call.line})</span>
            </>
        }

        const BackTrace = (props) => {
            if (!props.backtrace) {
                return null;
            }
            return <ul>{props.backtrace.map((call, index) => <li key={`_${index}`}>
                <a target="_blank" href={`${call.file}:${call.line}`}>
                    <FunctionCall call={call}/>
                </a>
                <BackTraceArgument argument={JSON.parse(call.arguments)}/>
            </li>)}</ul>
        }

        const FlattenException = (props) => {
            if (props.exception === null) {
                return null;
            }
            return <ul>
                {props.exception.messagefullWidth && props.exception.message !== message.exceptionMessage &&
                <li>Message: {props.exception.message}</li>}
                {props.exception.className && props.exception.class !== message.className &&
                <li>Class: {props.exception.class}</li>}
                {props.exception.headers && <li>Headers: {props.exception.headers}</li>}
                {props.exception.file && <li>File: {props.exception.file}</li>}
                {props.exception.line && <li>Line: {props.exception.line}</li>}
                {props.exception.code !== '' && <li>Code: {props.exception.code}</li>}
                {props.exception.statusCode && <li>StatusCode: {props.exception.statusCode}</li>}
                {props.exception.statusText && <li>StatusText: {props.exception.statusText}</li>}
                {props.exception.traceAsString && <li>TraceAsString: <pre>{props.exception.traceAsString}</pre></li>}
                {props.exception && <li>Previous: <FlattenException exception={props.exception.previous}/></li>}
            </ul>
        }

        return <Container className={classes.container}>
            <Grid container spacing={1}>
                <Grid item xs={showDetail?6:12}>
                    <TableContainer component={Paper} style={{overflow: 'auto'}}>
                        <Table className={classes.table} stickyHeader size="small" aria-label="simple table">
                            <TableHead>
                                <TableRow>
                                    <TableCell colSpan={2}>
                                        <Button
                                            onClick={this.reload}><Refresh/>
                                        </Button>
                                        <MuiTriStateCheckbox
                                            edge="start"
                                            tabIndex={-1}
                                            checked={oneOfMessageIsChecked ? null : allMessageAreChecked}
                                            color="primary"
                                            onClick={(event) => {
                                                changeCheckAll()
                                                event.preventDefault();
                                            }}
                                        />
                                        <Button disabled={messages.filter((message)=>message.checked).length === 0}
                                            onClick={()=>this.reject(messages.filter((message)=>message.checked).map((message)=>message.id))}><DeleteForeverIcon/>
                                        </Button>
                                        <Button onClick={()=>this.setState({showDetail:!showDetail})}>
                                            {showDetail?<ChevronRightIcon/>:<ChevronLeftIcon/>}
                                        </Button>
                                        <TextField
                                            label="Class"
                                            style={{margin: 8}}
                                            onChange={(event) => {
                                                this.setState({
                                                    filter: event.target.value
                                                });
                                            }}
                                        />
                                    </TableCell>
                                    {!showDetail && <TableCell align="left">Date</TableCell>}
                                    {!showDetail && <TableCell align="left">Exception</TableCell>}
                                    {!showDetail && <TableCell align="left"></TableCell>}
                                </TableRow>
                            </TableHead>
                            <TableBody>
                                {filteredMessages.length>0 && filteredMessages.map((message,index) => (
                                    <TableRow key={message.id}>
                                        <TableCell align="center">
                                            <Button onClick={() => this.loadMessage(message.id)}>
                                                {message.id}
                                            </Button>
                                            <Checkbox size="small" checked={message.checked} onChange={()=>{
                                                messages[index].checked=!messages[index].checked;
                                                this.setState({
                                                    messages
                                                })
                                            }} />
                                        </TableCell>
                                        <TableCell align="left" onClick={() => this.loadMessage(message.id)}>
                                            <div style={{width: '100%', overflow: 'auto'}}>
                                            {showDetail?message.className.split('\\').pop():message.className}
                                            {showDetail && <pre>{message.exceptionMessage}</pre>}
                                            </div>
                                        </TableCell>
                                        {!showDetail && <TableCell align="left">{message.date}</TableCell>}
                                        {!showDetail && <TableCell align="left">{message.exceptionMessage}</TableCell>}
                                        {!showDetail && <TableCell align="right">
                                            <ButtonGroup variant="contained" color="primary"
                                                         aria-label="contained primary button group">
                                                <Button disabled={message.statename === 'RUNNING'} onClick={() => {
                                                    start(message)
                                                }}>Start</Button>
                                            </ButtonGroup>
                                        </TableCell>}
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </TableContainer>
                </Grid>
                <Grid item xs={showDetail?6:false} style={{overflow: 'auto'}}>
                    {message && (<>
                        <ButtonGroup color="primary" aria-label="outlined primary button group">
                            <Button onClick={()=>this.retry(message.id)}>Retry</Button>
                            <Button onClick={()=>this.reject([message.id])}>Delete</Button>
                            <Button onClick={()=>this.setState({showDetail:false})}>Close</Button>
                            <Typography variant="h6" className={classes.title} component="div">
                                [{message.id}] {message.date}
                            </Typography>
                        </ButtonGroup>
                        <Typography variant="h6" className={classes.title} component="div">
                            {message.className}
                        </Typography>
                        <Typography variant="h6" className={classes.title} component="div">
                            <pre>{message.message}</pre>
                        </Typography>
                        <Typography variant="h6" className={classes.title} component="div">
                            {message.exceptionMessage}
                        </Typography>
                        <BackTrace backtrace={message.backtrace}/>
                        <FlattenException exception={message.flattenException}/>
                    </>)}
                </Grid>
            </Grid>
        </Container>;
    }
}

export default withStyles(styles)(MessengerMessages);
