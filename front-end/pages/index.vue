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
            placeholder="your-company-name"
            :prefix="origin"
            v-model="slug"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            nuxt
            @click="checkSlug"
          >
            Let's go!
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
      <login-dialog-content/>
    </v-dialog>
  </v-row>
</template>

<script>
import LoginDialogContent from '@/components/login/LoginDialogContent'

export default {
  components: { LoginDialogContent },
  data: () => ({
    slug: '',
    showLoginDialog: false,
    origin: window.location.origin + '/'
  }),
  methods: {
    async checkSlug () {
      try {
        // If no validation errors are returned then this slug is available.
        await this.$axios.get('api/company/check-slug-availability', {
          query: {
            slug: this.slug
          }
        })

        // If we are logged in, attempt to register slug.
        this.showLoginDialog = true
        // if (this.$store.user.me) {
        //   await this.$axios.post('api/company/', {
        //     body: {
        //       slug: this.slug
        //     }
        //   })
        // } else {
        //   this.showLoginModal = true
        // }
      } catch (e) {}
    }
  }
}
</script>
