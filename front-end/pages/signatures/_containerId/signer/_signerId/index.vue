<template>
  <div>
    <h1>Add your signature</h1>
    <div>
      Here is PDF file content
    </div>
    <v-btn
      color="primary"
      nuxt
      @click="submit"
    >
      Save!
    </v-btn>
  </div>
</template>

<script>
export default {
  data () {
    return {
      files: []
    }
  },
  created () {
    this.$axios.$post(
      `api/signatures/container/${this.$route.params.containerId}/signer/${this.$route.params.signerId}/files`, {
        visual_signature: 'visual'
      }).then((response) => {
      this.files = response.data
    })
  },
  methods: {
    async submit () {
      await this.$axios.$post(
        `api/signatures/container/${this.$route.params.containerId}/signer/${this.$route.params.signerId}/visual-sign`, {
          visual_signature: 'visual'
        })
    }
  }
}
</script>

<style scoped>

</style>
