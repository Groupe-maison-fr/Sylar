import React from 'react';
import { Link as RouterLink } from 'react-router-dom';
import BrightnessHighIcon from '@mui/icons-material/BrightnessHigh';
import Brightness3Icon from '@mui/icons-material/Brightness3';
import LoggerOpenedIcon from '@mui/icons-material/BorderVerticalOutlined';
import LoggerClosedIcon from '@mui/icons-material/BorderVertical';
import jwt_decode from 'jwt-decode';
import {
  AppBar,
  Badge,
  Box,
  Button,
  Divider,
  IconButton,
  Menu,
  MenuItem,
  Toolbar,
  Typography,
} from '@mui/material';
import Logo from '../../components/Logo';
import { useDarkMode } from '../../Context/PrefersDarkModeContext';
import { setInitialDarkMode } from '../../components/DarkMode';
import { useLogList } from '../../Context/LogListContext';
import { AUTHENTICATION_ACTIONS } from '../../Context/Authentication/AuthenticationContext';
import AuthenticationDialog from '../../Context/Authentication/AuthenticationDialog';
import { useNavigate } from 'react-router';
import { useAuthenticatedClient } from '../../Context/Authentication/AuthenticatedClient';

const TopBar = ({
  isDrawerLoggerBarOpen,
  setDrawerLoggerBarOpen,
}: {
  setDrawerLoggerBarOpen: (open: boolean) => void;
  isDrawerLoggerBarOpen: boolean;
}) => {
  const navigate = useNavigate();
  // @ts-ignore
  const { prefersDarkMode, setPrefersDarkMode } = useDarkMode();
  const { logListContextData } = useLogList();
  const [authenticationDialog, setAuthenticationDialog] =
    React.useState<boolean>(false);
  const { authenticationState, dispatchAuthentication } =
    useAuthenticatedClient();
  const [anchorEl, setAnchorEl] = React.useState<null | HTMLElement>(null);
  const open = Boolean(anchorEl);
  const handleClick = (event: React.MouseEvent<HTMLButtonElement>) => {
    setAnchorEl(event.currentTarget);
  };
  const handleClose = () => {
    setAnchorEl(null);
  };
  const decodedJwt = authenticationState.jwt
    ? jwt_decode<{ iat: number; username: string; roles: string[] }>(
        authenticationState.jwt,
      )
    : null;

  return (
    <AppBar elevation={0} style={{ backgroundColor: 'rgb(25, 118, 210)' }}>
      <Toolbar>
        <RouterLink to="/">
          <Logo />
        </RouterLink>
        <Box flexGrow={1}>
          <Typography variant="h4" style={{ paddingLeft: 20 }}>
            Sylar Dashboard
          </Typography>
        </Box>
        <IconButton
          color="inherit"
          onClick={() => {
            const newDarkMode = !prefersDarkMode;
            setPrefersDarkMode(newDarkMode);
            setInitialDarkMode(newDarkMode);
          }}
          size="large"
        >
          {prefersDarkMode ? <Brightness3Icon /> : <BrightnessHighIcon />}
        </IconButton>
        <IconButton
          color="inherit"
          onClick={() => {
            setDrawerLoggerBarOpen(!isDrawerLoggerBarOpen);
          }}
          size="large"
        >
          <Badge
            badgeContent={logListContextData.list.length}
            color="secondary"
          >
            {isDrawerLoggerBarOpen ? (
              <LoggerOpenedIcon />
            ) : (
              <LoggerClosedIcon />
            )}
          </Badge>
        </IconButton>
        {!authenticationState.isLogged && (
          <Button
            style={{ paddingLeft: 20 }}
            onClick={() => setAuthenticationDialog(true)}
          >
            Login
          </Button>
        )}
        {authenticationState.isLogged && (
          <Button
            style={{ paddingLeft: 20 }}
            aria-controls={open ? 'basic-menu' : undefined}
            aria-expanded={open ? 'true' : undefined}
            onClick={handleClick}
          >
            {authenticationState.username}
          </Button>
        )}
        <Menu anchorEl={anchorEl} open={open} onClose={handleClose}>
          {decodedJwt?.roles?.map((role: string) => (
            <MenuItem key={role} disabled={true}>
              {role}
            </MenuItem>
          ))}

          <MenuItem disabled={true}>
            <Divider />
          </MenuItem>

          {authenticationState.isLogged && (
            <MenuItem
              onClick={() => {
                dispatchAuthentication({
                  type: AUTHENTICATION_ACTIONS.LOGOUT,
                });
              }}
            >
              Logout
            </MenuItem>
          )}
        </Menu>
        <AuthenticationDialog
          open={authenticationDialog}
          onClose={() => setAuthenticationDialog(false)}
          onAuthenticate={() => navigate('/app/services', { replace: true })}
        />
      </Toolbar>
    </AppBar>
  );
};

export default TopBar;
