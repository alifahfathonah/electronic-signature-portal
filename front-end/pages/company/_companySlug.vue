<template>
  <nuxt/>
</template>

<script>
export default {
  beforeCreate () {
    // Check that I can select this company.
    const companySlug = this.$route.params.companySlug
    this.$store.commit('company/selectCompany', { slug: companySlug })
    if (!this.$store.state.company.selectedCompany) {
      this.$toast("You can't access this company!", { color: 'error' })
      this.$router.replace('/')
    }
  }
}
</script>
