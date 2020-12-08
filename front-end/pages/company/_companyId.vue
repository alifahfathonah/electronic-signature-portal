<template>
  <nuxt/>
</template>

<script>
export default {
  beforeCreate () {
    // Check that I can select this company.
    const companyId = this.$route.params.companyId
    this.$store.commit('company/selectCompany', { id: companyId })
    if (!this.$store.state.company.selectedCompany) {
      this.$toast("You can't access this company!", { color: 'error' })
      this.$router.replace('/')
    }
  }
}
</script>
