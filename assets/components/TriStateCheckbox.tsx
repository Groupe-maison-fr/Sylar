// Partly based on https://pastebin.com/0NLGNdCq / https://codesandbox.io/s/material-demo-dwxed
// Partly based on https://github.com/TylerRick/mui-tri-state-checkbox/blob/master/src/components/mui-tri-state-checkbox/MuiTriStateCheckbox.tsx

import * as React from 'react';

import { useState, useMemo, forwardRef } from 'react';
import Checkbox from '@mui/material/Checkbox';
import { CheckboxProps as MuiCheckboxProps } from '@mui/material/Checkbox';

export type TriStateCheckboxProps = Omit<
  MuiCheckboxProps,
  'indeterminate' | 'indeterminateIcon' | 'checked'
> & {
  onChange?: (
    event: React.ChangeEvent<HTMLInputElement>,
    checked: boolean | null,
  ) => void;
  checked?: boolean | null;
};

/**
 * Tri-state checkbox built on material-ui Checkbox
 * @prop {boolean | null} checked - the state of the checkbox
 *   - `false`: means unchecked
 *   - `null`: (or undefined) means indeterminate
 *   - `true`: means checked
 * @prop {boolean | null} defaultChecked - the initial state of the checkbox
 *   (if you want to use this as an uncontrolled component)
 * @prop {(event, checked: boolean | null) => void} onChange
 *   Called whenever the state of the checkbox changes. Use the `checked`
 *   argument, since `event.target.checked` cannot be relied on as it can with
 *   a regular Checkbox.
 *
 * This component also passes all other props to the underlying Checkbox
 * *except* for `indeterminate`.
 */

export const TriStateCheckbox: React.FunctionComponent<TriStateCheckboxProps> =
  forwardRef(function Component(
    {
      checked = null,
      onChange = (_event, _checked) => {},
      ...rest
    }: TriStateCheckboxProps,
    _ref,
  ) {
    const [internalChecked, setInternalChecked] = useState<boolean | null>(
      checked ?? null,
    );
    const indeterminate = useMemo(() => {
      return internalChecked == null;
    }, [internalChecked]);

    if (checked !== undefined && internalChecked !== checked) {
      setInternalChecked(checked);
      return null;
    }
    const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
      setInternalChecked((checked) => {
        if (checked === false) {
          onChange(event, null);
          return null;
        }
        if (checked === true) {
          onChange(event, false);
          return false;
        }
        onChange(event, true);
        return true;
      });
    };

    return (
      <Checkbox
        {...rest}
        checked={internalChecked || false}
        indeterminate={indeterminate}
        onChange={handleChange}
      />
    );
  });
