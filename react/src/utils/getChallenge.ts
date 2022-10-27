import { API_HOST } from '../env'

export const getChallenge = async (): Promise<Uint8Array> => {

  const challengeResponse = await fetch(API_HOST + '/get-challenge', {
    method: 'GET',
    credentials: 'include',
  })
  const challengeResponseData = await challengeResponse.json()
  const challenge = atob(challengeResponseData.challengeB64) // base64-decode

  return Uint8Array.from(challenge, c => c.charCodeAt(0))
}
