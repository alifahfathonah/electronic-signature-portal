<template>
  <div>
    <v-list>
      <v-list-item v-for="company of companies" :key="company.url_slug" @click="selectCompany(company.url_slug)">
        {{ company.name }}
      </v-list-item>
    </v-list>
  </div>
</template>

<script>
export default {
  data () {
    return {
      companies: []
    }
  },
  created () {
    this.$store.dispatch('user/getMe').then(() => {
      if (this.$store.state.company.companies.length === 1) {
        this.selectCompany(this.$store.state.company.companies[0].url_slug)
      }
    })
  },
  methods: {
    selectCompany (urlSlug) {
      this.$router.push(`/company/${urlSlug}`)
    }
  }
}
</script>

<style scoped>

</style>
