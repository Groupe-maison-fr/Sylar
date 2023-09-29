import * as React from 'react';
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import { FormControl } from '@mui/base';
import { TextField } from '@mui/material';
import { useForm } from 'react-hook-form';
import { useSnackbar } from 'notistack';
import { loginData, useAuthenticatedClient } from './AuthenticatedClient';

export default function AuthenticationDialog({
  open,
  onClose,
  onAuthenticate,
}: {
  open: boolean;
  onClose: () => void;
  onAuthenticate: () => void;
}) {
  const { register, handleSubmit } = useForm<loginData>();
  const { authenticate } = useAuthenticatedClient();
  const { enqueueSnackbar } = useSnackbar();
  const onSubmit = (data: loginData) => {
    authenticate(data)
      .then(() => {
        enqueueSnackbar('login', { variant: 'info' });
        onAuthenticate();
        onClose();
      })
      .catch((error) => {
        enqueueSnackbar(error.response.data.message, { variant: 'error' });
      });
  };

  return (
    <Dialog open={open} onClose={close}>
      <form onSubmit={handleSubmit(onSubmit)}>
        <DialogTitle>Authentication</DialogTitle>
        <DialogContent>
          <DialogContentText>
            <FormControl>
              <TextField
                label="Username"
                {...register('username', { required: true })}
                type="text"
              />
            </FormControl>
            <FormControl>
              <TextField
                label="Password"
                type="password"
                {...register('password', { required: true })}
              />
            </FormControl>
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button
            onClick={handleSubmit(onSubmit)}
            type={'submit'}
            color={'primary'}
            variant={'contained'}
          >
            Ok
          </Button>
          <Button onClick={onClose} color={'secondary'} variant={'contained'}>
            Cancel
          </Button>
        </DialogActions>
      </form>
    </Dialog>
  );
}
