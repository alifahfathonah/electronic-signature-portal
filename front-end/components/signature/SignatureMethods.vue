<template>
  <v-row>
    <id-card-signature></id-card-signature>
    <mobile-id-signature></mobile-id-signature>
    <smart-id-signature></smart-id-signature>
    <zeal-id-signature></zeal-id-signature>
  </v-row>
</template>

<script>
import MobileIdSignature from '@/components/signature/MobileIdSignature'
import IdCardSignature from '@/components/signature/IdCardSignature'
import SmartIdSignature from '@/components/signature/SmartIdSignature'
import ZealIdSignature from '@/components/signature/ZealIdSignature'
export default {
  name: 'SignatureMethods',
  components: { ZealIdSignature, SmartIdSignature, IdCardSignature, MobileIdSignature }
}
</script>

<style scoped>

</style>
