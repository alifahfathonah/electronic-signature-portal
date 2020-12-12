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
      files: [],
      signer: null
    }
  },
  created () {
    this.$axios.$post(
      `api/signatures/container/${this.$route.params.containerId}/signer/${this.$route.params.signerId}/files`, {
        visual_signature: 'visual'
      }).then((response) => {
      console.log('Response on ', response)
      this.files = response.files
      this.signer = response.signer
    })
  },
  methods: {
    async submit () {
      await this.$axios.$post(
        `api/signatures/container/${this.$route.params.containerId}/signer/${this.$route.params.signerId}/visual-sign`, {
          visual_signature: 'iVBORw0KGgoAAAANSUhEUgAAAWwAAABjCAYAAAC/vjrYAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAENklEQVR4nO3d23KbMBRAUej0/3+ZPtGhFDtCXHSOtNZMH9pJEwJmW5ZlmJdlWSYAwvvVegMAKCPYAEkINkASgg2QhGADJCHYAEkINkASgg2QhGADJCHYAEkINkASgg2QhGADJCHYAEkINkASgg2QhGADJCHYAEkINkASv1tvAGOY57n1JkzTNE1uYUpmzYM9z7OTqDNHcY5yjNdti7I9cEbzYNOPDDFct237pBJ5e2FLsLksQ6j3ttuacfsZk2BzSQ9TWvtRd/bfh34JNtV6iPWW6RKiE2yq9BbrraPpkv2/QwuCDV/s4y3atOSDMwBJCDYUWpYlzAeAGJNgAyQh2FDI6JrWvOkIBbzhSATNR9jmBYlsnmexJozmwSanEZ5o11CLNVGYEoENH5QhMsGm2jrKzh42kSYLwWZIIk1Ggs0lmUbZIk12IYKd6aQnH5dNpRfzEuhRLNp5RYrifvVKhG2CO4QK9jSJdnYtjl/PgS5ZOtnT78t3IaZE6Md2ffaTIRlhPrr0yc8gZxzhgm0+O7/tnVvuOo49j6KPOAc4Ei7Y0yTavbgy2h4t0FAiZLCnKUe0o29fBGducDvCNMdder8sAMfCBjs6J8w5Rze4/fQ1HPOERuhgRxxlb0eKol3u076KdGyjirRkkrZCB3ua4kTbSXNO6Ry0UeO/tvvDY4698MGepveWih1x0vzsaPRcur/2dyU/83+z+7bfIgxSiCdFsKfp3JtXdxgtHmc8tYLj7WP8pitParBKE+zV0ZtXNQ/8n+afS75nlOmap729xK6HcFuWyBPSBXt19FL609cZ3fws4uqNkpUlUXl88YS0wd76aW3vkyfP/gkh8okaMcolIm8bY2n9irqLYH/zxs6N9MZZ1ihDBq2nQbsP9tvOvIy/ctCta4Y2WkY73OVVR3JlbtZh61/rl9981moaVLAhMNG+5o3VOm8eo1+v/BSgiksgXLcsy98/T+zLN4+RYENwol3vaBVX5mgLNiQg2vV6irY5bEjEnHa9/ZLbp/blt+97Nuj77yPYkIxoX7Pdf29Gu+Zn/ffKQLAhn9Yf0Mpuf137p6K9/oz171c/e+GDM5CQy7Be88Z1au4+RkbY0AGj7WveWqt9x/cWbOiEcNfbz2uv7tyXdxwfwYbOCPd5n6Yt7h59Xz02gg2dMr9drnRf3TH6vhJtbzpCp1reC7VXpTdOKVHz/42wYQDuuvS/ljceqX0iFWwY1IgRj3R3qJpomxKBQZW8yfbp6zKJFOmtmmuPGGEDX9XMtT6dlTPb1FPijLCBr2qCV3ORo1EjfIZgA7erucjRqBE+w/WwgebEuoxgAyQh2ABJCDZAEoINkIRgAyQh2ABJCDZAEoINkIRgAyQh2ABJCDZAEoINkIRgAyQh2ABJCDZAEoINkIRgAyQh2ABJCDZAEoINkIRgAyQh2ABJ/AFqe6lnHZbwBAAAAABJRU5ErkJggg==',
          identifier: this.signer.identifier,
          identifier_type: 'email'
        })
    }
  }
}
</script>

<style scoped>

</style>
