<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <login-dialog-card v-if="requireLogin" :container-id="$route.params.containerId" />
      <v-card v-if="accessErrorMessage">
        <v-card-title>Oops!</v-card-title>
        <v-card-text>
          <p>{{ accessErrorMessage }}</p>
        </v-card-text>
      </v-card>
      <v-card v-if="container">
        <v-card-title class="headline">
          Document signing
        </v-card-title>
        <v-card-subtitle>Created on {{ $moment(container.created_at).format('DD.MM.Y') }}.</v-card-subtitle>
        <v-card-text>
          <p>
            Document link: <a href="javascript:;" @click="copyUrl">{{ signatureUrl }}</a>
            <v-btn
              fab
              small
              elevation="1"
              @click="copyUrl"
            >
              <v-icon>
                mdi-content-copy
              </v-icon>
            </v-btn>
            <br>
            Share this link with anyone who you wish to sign this document.
          </p>
          <file-list :files="container.files" :container-id="container.public_id" />
          <signature-methods @signWithSmartId="signWithSmartId" />
          <signee-list v-if="container.users && container.users.length" :people="container.users" />
          <template v-if="$route.query.signed">
            <br>
            <h3>Signed container</h3>
            Download signed container:
            <v-btn
              fab
              small
              elevation="1"
            >
              <v-icon>
                mdi-download
              </v-icon>
            </v-btn>
          </template>
        </v-card-text>
      </v-card>
    </v-col>
    <v-dialog
      v-model="showSmartIdSigningModal"
      persistent
      max-width="400"
    >
      <v-card>
        <v-card-title class="headline">
          Sign via Smart ID
        </v-card-title>
        <v-card-text>
          <p>Challenge: 0420</p>
          <p>If the challenge matches that shown in your Smart ID mobile app, please enter your PIN2 in the app.</p>
        </v-card-text>
      </v-card>
    </v-dialog>
    <v-dialog
      v-model="showSuccessDialog"
      persistent
      max-width="400"
    >
      <v-card>
        <v-card-title class="headline">
          Success
        </v-card-title>
        <v-card-text>
          <p>You signature was added.</p>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>

import LoginDialogCard from '@/components/login/LoginDialogCard'
import FileList from '@/components/FileList'
import SigneeList from '@/components/SigneeList'
import SignatureMethods from '@/components/signature/SignatureMethods'
import copyToClipboard from 'clipboard-copy'

export default {
  components: { LoginDialogCard, SignatureMethods, FileList, SigneeList },
  data: () => ({
    requireLogin: false,
    accessErrorMessage: null,

    container: null,
    idCardCertificate: null,
    error: null,
    showSmartIdSigningModal: false,
    showSuccessDialog: false
  }),
  computed: {
    signatureUrl () {
      return `${window.location.origin}/signature/${this.$route.params.containerId}`
    }
  },
  async beforeCreate () {
    const id = this.$route.params.containerId
    this.container = this.$store.state.container.containers.find(c => c.public_id === id + 's')
    if (!this.container) {
      try {
        const response = await this.$axios.get(`/api/container/${id}`)
        this.container = response.data.container
      } catch (e) {
        if (e.response.status === 401) {
          if (this.$store.state.user.me) {
            this.accessErrorMessage = 'You are not authorized to view this file.'
          } else {
            this.requireLogin = true
          }
        } else if (e.response.status === 404) {
          this.accessErrorMessage = 'Not found.'
        } else {
          this.accessErrorMessage = 'Unknown error.'
        }
      }
    }
  },
  methods: {
    copyUrl () {
      copyToClipboard(this.signatureUrl)
      this.$toast('Copied to clipboard!')
    },
    async idCardSigning () {
      console.log('Start reading certificate')

      let certificate = null
      try {
        // If result is not_allowed then not running over HTTPS.
        certificate = await window.hwcrypto.getCertificate({ lang: 'en' })
      } catch (err) {
        console.log(err)
        console.error('Getting certificate failed. Are you running over HTTPS?')
        return
      }

      console.log('Certificate acquired, preparing container')
      console.log('certificate is', certificate)
      const startSignResponse = await fetch('/api/signatures/start-signing', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          sign_type: 'id-card',
          doc_id: this.doc_id,
          certificate: certificate.hex
        })
      }).then(response => response.json())
      console.log('Container prepared, starting hash signing on the card')
      console.log('Getting data to sign: ', startSignResponse)

      const signature = await window.hwcrypto.sign(certificate, {
        type: 'SHA-256',
        hex: startSignResponse.hexDigest
      }, { lang: 'en' })
      console.log('Signature on the card completed, finalizing signature and getting signed container')
      console.log('Signature is', signature)

      const signResponse = await fetch(`https://id${process.env.MIX_EID_CARD_DOMAIN}/api/signatures/id-card/complete`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          client_id: process.env.MIX_EID_CLIENTID,
          doc_id: this.doc_id,
          signature_value: signature.hex
        })
      }).then(response => response.json())

      if (signResponse.status !== 'OK') {
        this.error = JSON.stringify(signResponse)
        console.log(signResponse)
        console.error('ID card signing failed, see console and server log')
        return
      }

      console.log('ID signature completed: ', signResponse)
    },
    signWithSmartId () {
      this.showSmartIdSigningModal = true

      setTimeout(() => {
        this.showSmartIdSigningModal = false
        this.showSuccessDialog = true
        setTimeout(() => {
          this.showSuccessDialog = false
          this.$router.push('/signature/28hqjUASd2a9S?signed=1')
        }, 3000)
      }, 4500)
    }
  }
}
</script>

<style>
</style>
