import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

import { API_HOST } from './env'

const RegisterCredential: React.FC = () => {
  if (!window.PublicKeyCredential) {
    return <p>WebAuthn not supported by your browser</p>
  }

  const startWebAuthnRegister = async (e: React.FormEvent) => {
    e.preventDefault()
    console.debug('start webauth dance')

    // FIXME Where is this from?
    const username = 'Firehed'

    const response = await fetch(API_HOST + '/readmeRegisterStep1.php', {
      method: 'POST',
      body: 'username=' + username,
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
      },
    })
    const responseData = await response.json()
    const challenge = atob(responseData.challengeB64) // base64-decode
    const userInfo = responseData.user

    const createOptions = {
      publicKey: {
        rp: {
          name: 'My website',
        },
        user: {
          name: userInfo.name,
          displayName: 'User Name',
          id: Uint8Array.from(userInfo.id, (c: string) => c.charCodeAt(0)),
        },
        challenge: Uint8Array.from(challenge, c => c.charCodeAt(0)),
        pubKeyCredParams: [
          {
            alg: -7, // ES256
            type: "public-key" as const,
          },
        ],
      },
      attestation: 'direct',
    }

    // Call the WebAuthn browser API and get the response. This may throw, which you
    // should handle. Example: user cancels or never interacts with the device.
    const credential = await navigator.credentials.create(createOptions) as PublicKeyCredential
    const credentialResponse = credential.response as AuthenticatorAttestationResponse

    // Format the credential to send to the server. This must match the format
    // handed by the ResponseParser class. The formatting code below can be used
    // without modification.
    const dataForResponseParser = {
      rawId: Array.from(new Uint8Array(credential.rawId)),
      type: credential.type,
      attestationObject: Array.from(new Uint8Array(credentialResponse.attestationObject)),
      clientDataJSON: Array.from(new Uint8Array(credentialResponse.clientDataJSON)),
    }

    // Send this to your endpoint - adjust to your needs.
    const request = new Request(API_HOST + '/readmeRegisterStep3.php', {
      body: JSON.stringify(dataForResponseParser),
      headers: {
        'Content-type': 'application/json',
      },
      method: 'POST',
    })
    const result = await fetch(request)
  }

  return (
    <form onSubmit={startWebAuthnRegister}>
      <Button intent={Intent.PRIMARY} type="submit">Add passkey</Button>
    </form>
  )

}

export default RegisterCredential
