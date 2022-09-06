import React from 'react'

import { Button, FormGroup, InputGroup, Intent } from '@blueprintjs/core'

const CreateAccount: React.FC = () => {
  const [userName, setUserName] = React.useState('')

  const register = (e: React.FormEvent)  => {
    e.preventDefault()
    console.info('register hit', userName)
  }

  return (
    <form onSubmit={register}>
      <FormGroup label="Username">
        <InputGroup value={userName} onChange={(e) => setUserName(e.target.value)} />
      </FormGroup>
      <Button type="submit" intent={Intent.PRIMARY}>Create Account</Button>
    </form>
  )
}

export default CreateAccount
