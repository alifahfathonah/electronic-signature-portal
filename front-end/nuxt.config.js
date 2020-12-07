import fs from 'fs'
// eslint-disable-next-line no-unused-vars
import colors from 'vuetify/es5/util/colors'

export default {
  // Disable server-side rendering (https://go.nuxtjs.dev/ssr-mode)
  ssr: false,

  // Target (https://go.nuxtjs.dev/config-target)
  target: 'static',

  generate: {
    dir: '../back-end/public'
  },

  // Global page headers (https://go.nuxtjs.dev/config-head)
  head: {
    titleTemplate: '%s - signature-portal-front-end',
    title: 'signature-portal-front-end',
    meta: [
      { charset: 'utf-8' },
      { name: 'viewport', content: 'width=device-width, initial-scale=1' },
      { hid: 'description', name: 'description', content: '' }
    ],
    link: [
      { rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }
    ]
  },

  // Global CSS (https://go.nuxtjs.dev/config-css)
  css: [
    '~/assets/style.css'
  ],

  // Plugins to run before rendering page (https://go.nuxtjs.dev/config-plugins)
  plugins: [],

  // Auto import components (https://go.nuxtjs.dev/config-components)
  components: true,

  // Modules for dev and build (recommended)
  // (https://go.nuxtjs.dev/config-modules)
  buildModules: [
    '@nuxtjs/eslint-module',
    '@nuxtjs/vuetify',
    '@nuxtjs/dotenv'
  ],

  // Modules (https://go.nuxtjs.dev/config-modules)
  modules: [
    // https://go.nuxtjs.dev/axios
    '@nuxtjs/axios'
  ],

  // Axios module configuration (https://go.nuxtjs.dev/config-axios)
  axios: {
    // proxy: true
    baseURL: process.env.BASE_URL
  },
  // proxy: {
  //   '/api/': { target: 'http://localhost:8080/api' }
  // },

  // Vuetify module configuration (https://go.nuxtjs.dev/config-vuetify)
  vuetify: {
    customVariables: ['~/assets/variables.scss']
  },

  // Build Configuration (https://go.nuxtjs.dev/config-build)
  build: {},

  hooks: {
    generate: {
      done (generator, errors) {
        // After generating, copy all files from public-base to public.
        // eslint-disable-next-line no-path-concat
        const files = fs.readdirSync(__dirname + '/../back-end/public-base/')
        files.forEach((file) => {
          fs.copyFileSync('../back-end/public-base/' + file,
            '../back-end/public/' + file)
        })
      }
    }
  }
}
