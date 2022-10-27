import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

import { API_HOST } from '../env'

export const CreateAccount: React.FC = () => {
  const [username, setUsername] = React.useState('')
  const [password, setPassword] = React.useState('')

  const register = async (e: React.FormEvent)  => {
    e.preventDefault()

    const request = { username, password }
    const response = await fetch(API_HOST + '/register', {
      method: 'POST',
      headers: {
        'Content-type': 'application/json',
      },
      body: JSON.stringify(request),
    })
    console.debug(response)
  }

  return (
    <form onSubmit={register}>
      <FormGroup label="Username">
        <InputGroup value={username} onChange={(e) => setUsername(e.target.value)} />
      </FormGroup>
      <FormGroup label="Password">
        <InputGroup value={password} type="password" onChange={(e) => setPassword(e.target.value)} />
      </FormGroup>
      <Button type="submit" intent={Intent.PRIMARY}>Create Account</Button>
    </form>
  )
}
