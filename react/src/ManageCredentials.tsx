import React from 'react'

import { API_HOST } from './env'

interface Params {
  accessToken: string
}
const ManageCredentials: React.FC<Params> = ({ accessToken }) => {
  const [creds, setCreds] = React.useState<{id: string, nickname: string}[]>([])
  React.useEffect(() => {
    if (accessToken === '') {
      return
    }
    const fetchData = async () => {
      const response = await fetch(API_HOST + '/my-credentials', {
        method: 'GET',
        headers: {
          Authorization: 'Bearer ' + accessToken,
        },
      })
      const data = await response.json()
      setCreds(data.credentials)
    }

    fetchData()
  }, [accessToken, setCreds])

  if (accessToken === '') {
    return <p>Log in to see credentials</p>
  }

  return (
    <ul>
      {creds.map(cred => (
        <li key={cred.id}>
          {cred.nickname}
        </li>
      ))}
    </ul>
  )
}

export default ManageCredentials
