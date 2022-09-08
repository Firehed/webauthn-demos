import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

import { API_HOST } from './env'

interface Params {
  setAccessToken: (token: string) => void,
}
const LoginWithWebAuthn: React.FC<Params> = ({ setAccessToken }) => {
  const [username, setUsername] = React.useState('')

  if (!window.PublicKeyCredential) {
    return <p>WebAuthn not supported by your browser</p>
  }

  const startWebAuthnLogin = async (e: React.FormEvent)  => {
    e.preventDefault()


    const challengeResponse = await fetch(API_HOST + '/get-challenge', {
      method: 'GET',
      credentials: 'include',
    })
    const challengeResponseData = await challengeResponse.json()
    const challenge = atob(challengeResponseData.challengeB64) // base64-decode

    const credentialsResponse = await fetch(API_HOST + '/get-credentials', {
      method: 'POST',
      headers: {
        'Content-type': 'application/json',
      },
      body: JSON.stringify({ username }),
    })
    const credentialData = await credentialsResponse.json()

    // const request = await fetch(API_HOST + '/login-webauthn', {
    //   method: 'POST',
    //   credentials: 'include',
    // })

    // Format for WebAuthn API
    const getOptions = {
      publicKey: {
        challenge: Uint8Array.from(challenge, c => c.charCodeAt(0)),
        // allowCredentials: credentialData.credentialIds.map((id: string) => ({
        //   id: Uint8Array.from(atob(id), c => c.charCodeAt(0)),
        //   type: 'public-key',
        // }))
      },
    }

    // Similar to registration step 2

    // Call the WebAuthn browser API and get the response. This may throw, which you
    // should handle. Example: user cancels or never interacts with the device.
    const credential = await navigator.credentials.get(getOptions) as PublicKeyCredential
    const credentialResponse = credential.response as AuthenticatorAssertionResponse

    // Format the credential to send to the server. This must match the format
    // handed by the ResponseParser class. The formatting code below can be used
    // without modification.
    const dataForResponseParser = {
      rawId: Array.from(new Uint8Array(credential.rawId)),
      type: credential.type,
      authenticatorData: Array.from(new Uint8Array(credentialResponse.authenticatorData)),
      clientDataJSON: Array.from(new Uint8Array(credential.response.clientDataJSON)),
      signature: Array.from(new Uint8Array(credentialResponse.signature)),
      userHandle: Array.from(new Uint8Array(credentialResponse.userHandle!)),
    }

    console.debug(getOptions, dataForResponseParser)

    // Send this to your endpoint - adjust to your needs.
    const response = await fetch(API_HOST + '/login-webauthn', {
      body: JSON.stringify({
        username,
        assertion: dataForResponseParser,
      }),
      headers: {
        'Content-type': 'application/json',
      },
      credentials: 'include',
      method: 'POST',
    })
    const data = await response.json()
    console.debug(data)
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

export default LoginWithWebAuthn
