import React, { JSX } from 'react';
import { Helmet } from 'react-helmet';

const Page = ({
  children,
  title = '',
  ...rest
}:{children:JSX.Element, title:string}) => (
  <div
    {...rest}
  >
    {/* @ts-ignore */}
    <Helmet>
      <title>{title}</title>
    </Helmet>
    {children}
  </div>
);

export default Page;
