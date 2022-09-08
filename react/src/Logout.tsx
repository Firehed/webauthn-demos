import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

import { API_HOST } from './env'

interface Params {
  accessToken: string
  setAccessToken: (token: string) => void
}
const Logout: React.FC<Params> = ({ accessToken, setAccessToken }) => {
  if (accessToken === '') {
    return null
  }

  const logout = () => {
    setAccessToken('')
  }

  return (
    <form onSubmit={logout}>
      <Button
        icon="log-out"
        intent={Intent.DANGER}
        type="submit"
      >Log out</Button>
    </form>
  )

}

export default Logout
