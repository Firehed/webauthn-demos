import { API_HOST } from '../env'

import { getChallenge } from './'

const loginWithCredential = async (username: string) => {


  const challenge = await getChallenge()

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
  const getOptions: CredentialRequestOptions = {
    publicKey: {
      challenge,
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

  return data
}

export default loginWithCredential
