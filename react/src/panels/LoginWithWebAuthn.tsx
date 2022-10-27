import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

import loginWithCredential from '../utils/loginWithCredential'

interface Params {
  setAccessToken: (token: string) => void,
}
export const LoginWithWebAuthn: React.FC<Params> = ({ setAccessToken }) => {
  const [username, setUsername] = React.useState('')

  if (!window.PublicKeyCredential) {
    return <p>WebAuthn not supported by your browser</p>
  }

  const startWebAuthnLogin = async (e: React.FormEvent)  => {
    e.preventDefault()

    const data = await loginWithCredential(username)
    setAccessToken(data.access_token)
  }



//     const request = { username, password }
//     const response = await fetch(API_HOST + '/login-password', {
//       method: 'POST',
//       headers: {
//         'Content-type': 'application/json',
//       },
//       body: JSON.stringify(request),
//     })

//     if (response.ok) {
//       const data = await response.json()
//       setAccessToken(data.access_token)
//     }
//     console.debug(response)
//   }

  return (
    <form onSubmit={startWebAuthnLogin}>
      <FormGroup label="Username">
        <InputGroup value={username} onChange={(e) => setUsername(e.target.value)} />
      </FormGroup>
      <Button type="submit" icon="key" intent={Intent.PRIMARY}>Log In</Button>
    </form>
  )
}
