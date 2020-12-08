export const state = () => ({
  companies: [],
  selectedCompany: null
})

export const mutations = {
  setCompanies (state, { companies }) {
    state.companies = companies
  },
  selectCompany (state, { id }) {
    state.selectedCompany = state.companies.find(c => c.id === parseInt(id))
  }
}
