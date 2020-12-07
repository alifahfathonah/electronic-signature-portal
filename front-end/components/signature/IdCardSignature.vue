<template>
  <v-col>
    <v-btn block class="signing-method-btn" elevation="1" @click="beginSigning">
      <img src="/methods/eid_idkaart_mark.png" class="method-height signing-method-btn-img">
    </v-btn>
  </v-col>
</template>

<script>
export default {
  name: 'IdCardSignature',
  data () {
    return {
      iFrame: null,
      digestResponse: null
    }
  },
  methods: {
    async beginSigning () {
      // TODO use process.env.EID_EASY_URL
      const eidEasyUrl = 'https://id.eideasy.localhost' // Must be withhout slash as this is the event origin format

      if (this.iFrame) {
        this.iFrame.contentWindow.postMessage({ operation: 'getCertificate', lang: 'en' }, eidEasyUrl)
        return
      }

      console.log('Signing already started')
      this.iFrame = document.createElement('iframe')

      const response = await this.$axios.post('api/signatures/get-idcard-token')
      this.iFrame.src = eidEasyUrl + '/signatures/integration/id-card?token=' + response.data.token

      window.addEventListener('message', async (event) => {
        if (event.origin !== eidEasyUrl) {
          console.log('Wrong origin', event.origin, eidEasyUrl)
          return
        }

        console.log('Got response from eID Easy ID card iFrame', event.data)
        if (event.data.operation === 'getCertificate') {
          this.digestResponse = await this.$axios.post('api/signatures/get-signature-digest', {
            certificate: event.data.certificate,
            container_id: this.$route.params.fileid
          })

          console.log('Digest received', this.digestResponse)
          this.iFrame.contentWindow.postMessage({ operation: 'getSignature', hexDigest: this.digestResponse.data.hexDigest, lang: 'en' }, eidEasyUrl)
        } else if (event.data.operation === 'getSignature') {
          this.$axios.post('api/signatures/finish-signature', {
            doc_id: this.digestResponse.data.doc_id,
            signature_value: event.data.signature_value
          }).then(() => alert('signature completed'))
        }
      })

      this.iFrame.onload = () => {
        console.log('Posting message')
        this.iFrame.contentWindow.postMessage({ operation: 'getCertificate', lang: 'en' }, eidEasyUrl)
      }

      document.body.appendChild(this.iFrame)

      // this.$emit('beginSigning')
    }
  }
}
</script>

<style scoped>

</style>
