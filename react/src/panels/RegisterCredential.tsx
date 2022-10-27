import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

import registerCredential from '../utils/registerCredential'

import { WebAuthnUnsupported } from '../components'

interface Params {
  accessToken: string
}
export const RegisterCredential: React.FC<Params> = ({ accessToken }) => {
  const [nickname, setNickname] = React.useState('')

  if (accessToken === '') {
    return <p>Log in w/ password first</p>
  }

  if (!window.PublicKeyCredential) {
    return <WebAuthnUnsupported />
  }

  const startWebAuthnRegister = async (e: React.FormEvent) => {
    e.preventDefault()
    const result = await registerCredential(accessToken, nickname)

    console.debug(result)
  }

  return (
    <form onSubmit={startWebAuthnRegister}>
      <FormGroup label="Nickname">
        <InputGroup value={nickname} onChange={(e) => setNickname(e.target.value)} />
      </FormGroup>

      <Button intent={Intent.PRIMARY} type="submit">Add passkey</Button>
    </form>
  )

}
