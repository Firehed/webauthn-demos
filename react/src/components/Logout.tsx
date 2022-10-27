import React from 'react'

import { Button, Intent } from '@blueprintjs/core'

interface Params {
  accessToken: string
  setAccessToken: (token: string) => void
}
export const Logout: React.FC<Params> = ({ accessToken, setAccessToken }) => {
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
