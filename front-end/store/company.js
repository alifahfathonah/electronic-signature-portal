export const state = () => ({
  companies: [],
  selectedCompany: null,
  pendingCompany: null
})

export const mutations = {
  setPendingCompany (state, { slug }) {
    state.pendingCompany = slug
  },
  setCompanies (state, { companies }) {
    state.companies = companies
  },
  selectCompany (state, { slug }) {
    state.selectedCompany = state.companies.find(c => c.url_slug === slug)
  }
}

export const actions = {
  async createCompany ({ commit }, properties) {
    const response = await this.$axios.$post('api/company/', properties)
    commit('setPendingCompany', { slug: null })

    commit('setCompanies', { companies: response.companies })
  }
}
