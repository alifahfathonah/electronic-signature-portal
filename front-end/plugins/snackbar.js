import Vue from 'vue'
import VuetifyToast from 'vuetify-toast-snackbar'

export default ({ $vuetify }, inject) => {
  Vue.use(VuetifyToast, {
    $vuetify,
    x: 'right',
    y: 'top',
    queueable: true,
    timeout: 5000
  })

  inject('toast', Vue.prototype.$toast)
}
