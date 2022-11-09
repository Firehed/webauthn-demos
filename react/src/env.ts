interface EnvVars {
  [key: string]: string
}

// @ts-ignore this value is set in public/index.html
const INJECTED_ENV_DATA: EnvVars = window.ENV || {}
const BUILDTIME_ENV_DATA = process.env || {}

// Attempt to follow the same naming conventions and restrictions imposed by
// CRA (i.e. the two magic values + anything starting with REACT_APP). This
// won't stop you from setting inappriate values, but it slims the surface area
const SANITIZED_INJECTED_ENV_DATA = Object.keys(INJECTED_ENV_DATA)
  .filter(key => key === 'NODE_ENV' || key === 'PUBLIC_URL' || key.startsWith('REACT_APP_'))
  .reduce((obj, key) => {
    const val = INJECTED_ENV_DATA[key]
    // Remove anything that didn't get substituted or is blank
    if (val !== '$' + key && val !== '') {
      obj[key] = val
    }
    return obj
  }, {} as EnvVars)

const combined = { ...BUILDTIME_ENV_DATA, ...SANITIZED_INJECTED_ENV_DATA }

// Actual config follows

export const API_HOST = combined.REACT_APP_API_HOST
