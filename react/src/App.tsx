import React from 'react';
// import './App.css';


import { Card } from '@blueprintjs/core'

import CreateAccount from './CreateAccount'
import RegisterCredential from './RegisterCredential'


function App() {
  return (
    <div className="App">
      <Card>
        <CreateAccount />
      </Card>
      <Card>
        <RegisterCredential />
      </Card>
    </div>
  );
}

export default App;
