export const state = () => ({
  containers: []
})

export const mutations = {
  setContainers (state, { containers }) {
    state.containers = containers
  }
}

export const actions = {
  async uploadFiles ({ commit, state, rootState }, { files, people }) {
    const slug = rootState.company.selectedCompany.url_slug
    const response = await this.$axios.$post(`api/company/${slug}/container`, { files, people })

    const containers = [...state.containers, response.container]
    commit('setContainers', { containers })
    return response.container
  }
}
