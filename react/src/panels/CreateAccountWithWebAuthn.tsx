import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

import { API_HOST } from '../env'
import registerCredential from '../utils/registerCredential'

interface Params {
  setAccessToken: (token: string) => void,
}
export const CreateAccountWithWebAuthn: React.FC<Params> = ({ setAccessToken }) => {
  const [username, setUsername] = React.useState('')

  const [complete, setComplete] = React.useState(false)

  const register = async (e: React.FormEvent)  => {
    e.preventDefault()

    const request = { username }
    const response = await fetch(API_HOST + '/register', {
      method: 'POST',
      headers: {
        'Content-type': 'application/json',
      },
      body: JSON.stringify(request),
    })
    console.debug(response)

    if (response.ok) {
      const data = await response.json()
      setAccessToken(data.access_token)
      setComplete(true)
    }
  }

  if (complete) {
    return (
      <p>
        User created.
        Go to the 'Add WebAuthn Credential' tab to finish up.
        Normally that would be streamlined into this flow, but to keep the demo code simple it's left separate.
      </p>
    )
  }

  return (
    <form onSubmit={register}>
      <FormGroup label="Username">
        <InputGroup value={username} onChange={(e) => setUsername(e.target.value)} />
      </FormGroup>
      <Button type="submit" intent={Intent.PRIMARY} icon="key">Create Account</Button>
    </form>
  )
}
