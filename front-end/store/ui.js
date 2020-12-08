export const state = () => ({
  validationErrors: {}
})

export const mutations = {
  setValidationErrors (state, { validationErrors }) {
    state.validationErrors = validationErrors
  }
}
