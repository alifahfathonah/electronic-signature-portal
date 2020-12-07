<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          Document signing
        </v-card-title>
        <v-card-subtitle>Created on 01.12.2020 by Bilbo Baggins.</v-card-subtitle>
        <v-card-text>
          <p>
            Document link: <a href="#">https://signing.yousite.com/signature/28hqjUASd2a9S</a>
            <v-btn
              fab
              small
              elevation="1"
            >
              <v-icon>
                mdi-content-copy
              </v-icon>
            </v-btn>
            <br>
            Share this link with anyone who you wish to sign this document.
          </p>
          <file-list />
          <signature-methods @signWithSmartId="signWithSmartId" />
          <signee-list />
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

import FileList from '@/components/FileList'
import SigneeList from '@/components/SigneeList'
import SignatureMethods from '@/components/signature/SignatureMethods'

export default {
  components: { SignatureMethods, FileList, SigneeList },
  data: () => ({
    idCardCertificate: null,
    error: null,
    showSmartIdSigningModal: false,
    showSuccessDialog: false
  }),
  methods: {
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
