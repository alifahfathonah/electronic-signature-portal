export const state = () => ({
  containers: []
})

export const mutations = {
  setContainers (state, { containers }) {
    state.containers = containers
  }
}

export const actions = {
  async uploadVisualSignature (
    { commit, state, rootState }, { files, signers }) {
    const slug = rootState.company.selectedCompany.url_slug
    const response = await this.$axios.$post(
      `api/company/${slug}/container`, {
        signature_type: 'visual', files, signers
      })

    const containers = [...state.containers, response.container]
    commit('setContainers', { containers })
    return response.container
  },

  async uploadFiles ({ commit, state, rootState }, { files, signers }) {
    const slug = rootState.company.selectedCompany.url_slug
    const response = await this.$axios.$post(`api/company/${slug}/container`,
      { signature_type: 'crypto', files, signers })

    const containers = [...state.containers, response.container]
    commit('setContainers', { containers })
    return response.container
  }
}
