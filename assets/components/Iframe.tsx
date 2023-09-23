import * as React from 'react';

export default (props: {
  src: string
  height: number
}) => {
  const { src, height } = props;

  return (
    <iframe
      title=""
      src={src}
      frameBorder={0}
      style={{ width: '1px', minWidth: '100%', height: `${height}px` }}
    />
  );
};
