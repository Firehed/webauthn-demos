import React from 'react';

import { Card, Tab, Tabs, TabId } from '@blueprintjs/core'

import {
  CreateAccount,
  LoginWithPassword,
  LoginWithWebAuthn,
  ManageCredentials,
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
          <Tab
            id={TabIds.CreateAccount}
            panel={<CreateAccount />}
            title="Create Account w/ Password"
          />
          <Tab
            id={TabIds.LoginWithPassword}
            panel={<LoginWithPassword setAccessToken={setAccessToken} />}
            title="Login w/ Password"
          />
          <Tab
            id={TabIds.RegisterCredential}
            panel={<RegisterCredential accessToken={accessToken} />}
            title="Add WebAuthn Credential"
          />
          <Tab
            id={TabIds.LoginWithWebAuthn}
            panel={<LoginWithWebAuthn setAccessToken={setAccessToken} />}
            title="Login w/ WebAuthn"
          />
          <Tab
            id={TabIds.ManageCredentials}
            panel={<ManageCredentials accessToken={accessToken} />}
            title="Manage WebAuthn Credentials"
          />
          <Logout
            accessToken={accessToken}
            setAccessToken={setAccessToken}
          />
        </Tabs>
      </Card>
      <Card>
        <DebugAccessToken token={accessToken} />
      </Card>
    </div>
  );
}

export default App;
