import React from 'react';

import { Card } from '@blueprintjs/core'

import CreateAccount from './CreateAccount'
import DebugAccessToken from './DebugAccessToken'
import LoginWithPassword from './LoginWithPassword'
import Logout from './Logout'
import RegisterCredential from './RegisterCredential'
import LoginWithWebAuthn from './LoginWithWebAuthn'

function App() {
  const [accessToken, setAccessToken] = React.useState('')
  return (
    <div className="App">
      <Card>
        <CreateAccount />
      </Card>
      <Card>
        <LoginWithPassword setAccessToken={setAccessToken} />
      </Card>
      <Card>
        <RegisterCredential accessToken={accessToken} />
      </Card>
      <Card>
        <LoginWithWebAuthn setAccessToken={setAccessToken} />
      </Card>
      <Card>
        <Logout accessToken={accessToken} setAccessToken={setAccessToken} />
      </Card>
      <DebugAccessToken token={accessToken} />
    </div>
  );
}

export default App;
