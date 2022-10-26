import { API_HOST } from '../env'

const registerCredential = async (accessToken: string, nickname: string) => {

  console.debug('start webauth dance')

  const meResponse = await fetch(API_HOST + '/me', {
    headers: {
      Authorization: 'Bearer ' + accessToken,
    },
  })
  const meData = await meResponse.json()
  const userInfo = meData.user

  const challengeResponse = await fetch(API_HOST + '/get-challenge', {
    credentials: 'include',
    method: 'GET',
  })
  const responseData = await challengeResponse.json()
  const challenge = atob(responseData.challengeB64) // base64-decode

  const createOptions = {
    publicKey: {
      rp: {
        name: 'My website',
      },
      user: {
        name: userInfo.name,
        displayName: userInfo.name,
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
  const request = new Request(API_HOST + '/add-credential', {
    body: JSON.stringify({
      nickname,
      credential: dataForResponseParser,
    }),
    credentials: 'include',// push cookies too for session
    headers: {
      Authorization: 'Bearer ' + accessToken,
      'Content-type': 'application/json',
    },
    method: 'POST',
  })
  const result = await fetch(request)
  return result
}

export default registerCredential
