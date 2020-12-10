<template>
  <div>
    <nuxt v-if="$store.state.user.me"/>
    <v-row v-else justify="center" align="center">
      <v-col cols="12" sm="8" md="6">
        <login-dialog-card :companySlug="$route.params.companySlug"/>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
  beforeCreate () {
    // Check that I can select this company.
    const companySlug = this.$route.params.companySlug
    this.$store.commit('company/selectCompany', { slug: companySlug })
    if (!this.$store.state.company.selectedCompany && this.$store.state.user.me) {
      // I'm logged in but don't have access to this company.
      this.$toast("You can't access this company!", { color: 'error' })
      this.$router.replace('/')
    }
  }
}
</script>
