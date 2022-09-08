import React from 'react'

import { Card } from '@blueprintjs/core'

const DebugAccessToken: React.FC<{ token: string }> = ({ token }) => {
  if (token === '') {
    return <>No access token</>
  }

  let [header, claims, sig] = token.split('.')

  return <Card>
    {/* <h4>Access token</h4> */}
    {/* <p>{token}</p> */}
    <h4>Header</h4>
    <p>{atob(header)}</p>
    <h4>Claims</h4>
    <p>{atob(claims)}</p>
    <h4>Sig</h4>
    <p>{sig}</p>
  </Card>
}

export default DebugAccessToken
