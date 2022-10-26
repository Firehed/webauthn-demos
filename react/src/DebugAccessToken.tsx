import React from 'react'

import { Card, H3, H4 } from '@blueprintjs/core'

const DebugAccessToken: React.FC<{ token: string }> = ({ token }) => {
  if (token === '') {
    return <>No access token</>
  }

  let [header, claims, sig] = token.split('.')

  return (
      <Card>
        <H3>Access Token/JWT Debug Info</H3>
        <H4>Header</H4>
        <p>{atob(header)}</p>
        <H4>Claims</H4>
        <p>{atob(claims)}</p>
        <H4>Sig</H4>
        <p>{sig}</p>
      </Card>
  )
}

export default DebugAccessToken
