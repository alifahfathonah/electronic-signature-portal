export default function ({ $axios, store, $toast }) {
  $axios.onRequest(() => {
    // Clear validation errors.
    store.commit('ui/setValidationErrors', { validationErrors: {} })
  })

  $axios.onError((error) => {
    // Replace validation errors.
    const validationErrors = (
      error.response &&
      error.response.data &&
      error.response.data.errors
    ) || {}
    store.commit('ui/setValidationErrors', { validationErrors })
    const msg = (
      error.response &&
      error.response.data &&
      error.response.data.message
    ) || 'Something went wrong'
    $toast(msg, { color: 'error' })
  })
}
