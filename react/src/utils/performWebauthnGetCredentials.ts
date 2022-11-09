export const performWebauthnGetCredentials = async (getOptions: CredentialRequestOptions) => {
  // Similar to registration step 2

  // Call the WebAuthn browser API and get the response. This may throw, which you
  // should handle. Example: user cancels or never interacts with the device.
  const credential = await navigator.credentials.get(getOptions) as PublicKeyCredential
  const credentialResponse = credential.response as AuthenticatorAssertionResponse

  // Format the credential to send to the server. This must match the format
  // handed by the server-side ResponseParser class. The formatting code below
  // can be used without modification.
  const dataForResponseParser = {
    rawId: Array.from(new Uint8Array(credential.rawId)),
    type: credential.type,
    authenticatorData: Array.from(new Uint8Array(credentialResponse.authenticatorData)),
    clientDataJSON: Array.from(new Uint8Array(credentialResponse.clientDataJSON)),
    signature: Array.from(new Uint8Array(credentialResponse.signature)),
    userHandle: Array.from(new Uint8Array(credentialResponse.userHandle!)),
  }

  console.debug(getOptions, dataForResponseParser)

  return dataForResponseParser
}
