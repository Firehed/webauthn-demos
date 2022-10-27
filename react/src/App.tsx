import React from 'react';

import { Card, Tab, Tabs, TabId } from '@blueprintjs/core'

import ManageCredentials from './ManageCredentials'
import LoginWithWebAuthn from './LoginWithWebAuthn'
import {
  CreateAccount,
  LoginWithPassword,
  RegisterCredential,
} from './panels'

import {
  DebugAccessToken,
  Logout,
} from './components'

enum TabIds {
  CreateAccount,
  LoginWithPassword,
  LoginWithWebAuthn,
  RegisterCredential,
  ManageCredentials,
}

function App() {
  const [accessToken, setAccessToken] = React.useState('')
  const [selectedTabId, setSelectedTabId] = React.useState<TabId>(TabIds.CreateAccount)

  return (
    <div className="App">
      <Card>
        <Tabs
          onChange={setSelectedTabId}
          selectedTabId={selectedTabId}
          vertical
        >
          <Tab title="Create Account" id={TabIds.CreateAccount} panel={<CreateAccount />} />
          <Tab title="Login w/ Password" id={TabIds.LoginWithPassword} panel={<LoginWithPassword setAccessToken={setAccessToken} />} />
          <Tab title="Add WebAuthn Credential" id={TabIds.RegisterCredential} panel={<RegisterCredential accessToken={accessToken} />} />
          <Tab title="Login w/ WebAuthn" id={TabIds.LoginWithWebAuthn} panel={<LoginWithWebAuthn setAccessToken={setAccessToken} />} />
          <Tab title="Manage WebAuthn Credentials" id={TabIds.ManageCredentials} panel={<ManageCredentials accessToken={accessToken} />} />
          <Logout accessToken={accessToken} setAccessToken={setAccessToken} />
        </Tabs>
      </Card>
      <Card>
        <DebugAccessToken token={accessToken} />
      </Card>
    </div>
  );
}

export default App;
