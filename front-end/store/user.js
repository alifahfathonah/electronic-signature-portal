export const state = () => ({
  me: null
})

export const mutations = {
  setMe (state, { me }) {
    state.me = me
  }
}

export const actions = {
  async getMe ({ commit }) {
    const response = await this.$axios.$get('api/authenticate/who-am-i')
    if (response.user) {
      commit('setMe', { me: response.user })
    }
  }
}
