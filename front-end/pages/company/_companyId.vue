<template>
  <nuxt />
</template>

<script>
export default {
  beforeCreate () {
    // TODO make sure that after registration the user is allowed to access immediately.
    // Check that I can select this company.
    const companyId = this.$route.params.companyId
    this.$store.commit('company/selectCompany', { id: companyId })
    if (!this.$store.state.company.selectedCompany) {
      this.$toast('You can\'t access this company!', { color: 'error' })
      this.$router.replace('/')
    }
  }
}
</script>
