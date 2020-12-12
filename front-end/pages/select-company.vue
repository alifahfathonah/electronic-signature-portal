<template>
  <div>
    <v-list>
      <v-list-item v-for="company of $store.state.company.companies" :key="company.url_slug" @click="selectCompany(company.url_slug)">
        Company {{ company.url_slug }}
      </v-list-item>
    </v-list>
  </div>
</template>

<script>
export default {
  data () {
    return {}
  },
  created () {
    if (this.$store.state.company.pendingCompany !== null) {
      this.$store.dispatch('company/createCompany', { url_slug: this.$store.state.company.pendingCompany }).then(() => {
        this.getMe()
      })
    } else {
      this.getMe()
    }
  },
  methods: {
    getMe () {
      this.$store.dispatch('user/getMe').then(() => {
        if (this.$store.state.company.companies.length === 1) {
          this.selectCompany(this.$store.state.company.companies[0].url_slug)
        }
      })
    },
    selectCompany (urlSlug) {
      this.$router.push(`/company/${urlSlug}`)
    }
  }
}
</script>

<style scoped>

</style>
