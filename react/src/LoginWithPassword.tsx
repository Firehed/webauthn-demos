import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

import { API_HOST } from './env'

interface Params {
  setAccessToken: (token: string) => void,
}
const LoginWithPassword: React.FC<Params> = ({ setAccessToken }) => {
  const [username, setUsername] = React.useState('')
  const [password, setPassword] = React.useState('')

  const register = async (e: React.FormEvent)  => {
    e.preventDefault()

    const request = { username, password }
    const response = await fetch(API_HOST + '/login-password', {
      method: 'POST',
      headers: {
        'Content-type': 'application/json',
      },
      body: JSON.stringify(request),
    })

    if (response.ok) {
      const data = await response.json()
      setAccessToken(data.access_token)
    }
    console.debug(response)
    setUsername('')
    setPassword('')
  }

  return (
    <form onSubmit={register}>
      <FormGroup label="Username">
        <InputGroup
          autoComplete="username webauthn"
          onChange={(e) => setUsername(e.target.value)}
          value={username}
        />
      </FormGroup>
      <FormGroup label="Password">
        <InputGroup
          autoComplete="password webauthn"
          onChange={(e) => setPassword(e.target.value)}
          type="password"
          value={password}
        />
      </FormGroup>
      <Button type="submit" intent={Intent.PRIMARY}>Log In</Button>
    </form>
  )
}

export default LoginWithPassword
