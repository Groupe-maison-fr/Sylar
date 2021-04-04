import * as React from 'react';
import {withStyles} from '@material-ui/core/styles';
import {Container, Grid} from "@material-ui/core";
import HistogramDisplay from "./HistogramDisplay.jsx";
import {addClient, removeClient} from "../../components/WebsocketPool";

const styles = (theme) => ({
    table: {
        minWidth: 450,
    },
});

class DockerStatistics extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            stats: {
                keys: [],
                cpuUsage: [],
                memUsage: [],
            },
        }
        this._ismounted = null;
        this.myRef = React.createRef();
        const urlParts = window.location.href.split("/");
        [this.socketId, this.socket] = addClient(`ws://${urlParts[2]}/monitor/containers/`, (message) => {
            this.onMessage(message)
        })
    }

    onMessage(message) {
        if (!this._ismounted) {
            return;
        }
        if (message.type === 'dockerStatistics') {
            this.setState({stats: message.dockerStatistics});
        }
    }

    componentDidMount() {
        this._ismounted = true;
    }

    componentWillUnmount() {
        this._ismounted = false;
        removeClient(this.socketId);
    }

    render() {
        const {classes} = this.props;
        const {stats} = this.state;

        return (
            <Container maxWidth={false} className={classes.container}>
                <Grid container spacing={3}>
                    <Grid item xs={6} sm={6} lg={6} style={{width: '100%', height: '300px'}}>
                        <HistogramDisplay
                            data={stats}
                            dataKey={'cpuUsage'}
                        />
                    </Grid>
                    <Grid item xs={6} sm={6} lg={6} style={{width: '100%', height: '300px'}}>
                        <HistogramDisplay
                            data={stats}
                            dataKey={'memUsage'}
                        />
                    </Grid>
                </Grid>
            </Container>
        );
    }
}

export default withStyles(styles)(DockerStatistics);
