import {ResponsiveBarCanvas} from "@nivo/bar";
import * as React from "react";
import {makeStyles} from "@material-ui/core";
import { useTheme } from '@material-ui/core/styles';
const useStyles = makeStyles((theme) => ({
    root: {},
    value: {
        display: 'inline-block'
    },
    actions: {
        justifyContent: 'flex-end'
    }
}));

const HistogramDisplay = (props) => {
    const theme = useTheme();
    const chartTheme = (theme) => {
        return {
            grid: {
                line: {
                    stroke: "rgba(0,0,0,0.05)",
                }
            },
            axis: {
                legend: {
                    text: {
                        fill: theme.palette.primary.dark,
                        fontSize: 12,
                    }
                },
                ticks: {
                    text: {
                        fill: theme.palette.primary.dark,
                        fontSize: 12,
                    },
                    line: {
                        stroke: theme.palette.primary.dark,
                        strokeWidth: 1,
                    }
                },
                domain: {
                    line: {
                        stroke: theme.palette.primary.dark,
                        strokeWidth: 1,
                    }
                },
            },
            crosshair: {
                line: {
                    stroke: theme.palette.primary.dark,
                    strokeWidth: 1,
                    strokeOpacity: 0.35,
                },
            }
        }
    };
    return <ResponsiveBarCanvas
        data={props.data[props.dataKey]}
        keys={props.data.keys}
        theme={chartTheme(theme)}
        indexBy={'time'}
        margin={{top: 10, right: 190, bottom: 50, left: 90}}
        padding={0.3}
        valueScale={{type: 'linear'}}
        indexScale={{type: 'band', round: true}}
        axisTop={null}
        axisRight={null}
        axisBottom={{
            tickSize: 5,
            tickPadding: 5,
            tickRotation: 0,
            legend: 'time',
            legendPosition: 'middle',
            legendOffset: 32
        }}
        axisLeft={{
            tickSize: 5,
            tickPadding: 5,
            tickRotation: 0,
            tickValues: 5,
            legend: props.dataKey,
            legendPosition: 'middle',
            legendOffset: -40
        }}
        labelSkipWidth={12}
        labelSkipHeight={12}
        labelTextColor="theme"
        legends={[
            {
                dataFrom: 'keys',
                anchor: 'bottom-right',
                direction: 'column',
                justify: false,
                translateX: 120,
                translateY: 0,
                itemsSpacing: 2,
                itemWidth: 100,
                itemHeight: 20,
                itemDirection: 'left-to-right',
                itemOpacity: 0.85,
                symbolSize: 20,
                effects: [
                    {
                        on: 'hover',
                        style: {
                            itemOpacity: 1
                        }
                    }
                ]
            }
        ]}
        animate={true}
        motionStiffness={90}
        motionDamping={15}
    />
}

export default HistogramDisplay;
