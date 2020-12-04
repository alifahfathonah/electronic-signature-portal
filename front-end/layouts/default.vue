<template>
  <v-app dark>
    <v-app-bar
      fixed
      app
      elevation="1"
    >
      <v-toolbar-title v-text="title" />
    </v-app-bar>
    <v-main>
      <v-container>
        <nuxt />
      </v-container>
    </v-main>
    <v-footer
      :absolute="true"
      app
    >
      <span>&copy; {{ new Date().getFullYear() }}</span>
    </v-footer>
  </v-app>
</template>

<script>
export default {
  data () {
    return {
      miniVariant: false,
      right: true,
      title: 'Open Source Electronic Signing Portal'
    }
  }
}
</script>
