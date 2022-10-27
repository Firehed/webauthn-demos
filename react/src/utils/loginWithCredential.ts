import { API_HOST } from '../env'

import { getChallenge , performWebauthnGetCredentials } from './'

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

  const assertion = await performWebauthnGetCredentials(getOptions)

  // Send this to your endpoint - adjust to your needs.
  const response = await fetch(API_HOST + '/login-webauthn', {
    body: JSON.stringify({
      username,
      assertion,
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
