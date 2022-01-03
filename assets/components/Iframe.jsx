import * as React from 'react';
import PropTypes from 'prop-types';


export default function Iframe(props) {
    const {src, height} = props;

    return <iframe
        src={src}
        frameBorder={0}
        style={{ width: '1px', minWidth: '100%',height:`${height}px`}}
    />
}

Iframe.propTypes = {
    src: PropTypes.string.isRequired,
    height: PropTypes.number.isRequired,
};
