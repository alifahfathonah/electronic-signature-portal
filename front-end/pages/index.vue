<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          Electronic signature portal
        </v-card-title>
        <v-card-text>
          <p>Here's some of that sweet talk that we're so well known for.</p>
          <p>It's a pleasure to give in to temptations when these temptations actually make your life better. That's what we do here.</p>
          <p>Give in to the temptation, join the forces of freedom. You can have it all - legally binding signatures AND control over your data.</p>
          <v-text-field
            v-model="url_slug"
            placeholder="your-company-name"
            :prefix="origin"
            :error-messages="$store.state.ui.validationErrors['url_slug']"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            nuxt
            :disabled="creatingCompany"
            @click="checkSlug"
          >
            <template v-if="!creatingCompany">
              Create your company!
            </template>
            <template v-else>
              Creating company...
            </template>
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
    <v-dialog
      v-model="showLoginDialog"
      persistent
      max-width="400"
      :fullscreen="$vuetify.breakpoint.xsOnly"
    >
      <login-dialog-content @loggedIn="loggedIn"/>
    </v-dialog>
  </v-row>
</template>

<script>
import LoginDialogContent from '@/components/login/LoginDialogContent'

export default {
  components: { LoginDialogContent },
  data: () => ({
    url_slug: '',
    showLoginDialog: false,
    origin: window.location.origin + '/',
    creatingCompany: false
  }),
  methods: {
    async checkSlug () {
      try {
        // If no validation errors are returned then this slug is available.
        await this.$axios.get('api/company/check-slug-availability', {
          params: {
            url_slug: this.url_slug
          }
        })
      } catch (e) {
        // If slug is taken, validation error is thrown and we return.
        return
      }

      // If we are logged in, attempt to register slug.
      if (this.$store.state.user.me) {
        this.createCompany()
      } else {
        this.showLoginDialog = true
      }
    },
    loggedIn () {
      this.showLoginDialog = false
      this.createCompany()
    },
    async createCompany () {
      this.creatingCompany = true

      await this.$store.dispatch('company/createCompany', {
        url_slug: this.url_slug
      })

      this.$router.push(`company/${this.url_slug}/config`)
    }
  }
}
</script>
