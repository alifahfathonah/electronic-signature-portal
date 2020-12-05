<template>
  <v-row>
    <v-col>
      <h3>Add you signature now!</h3>
      <p>Select signing method:</p>
      <v-row>
        <id-card-signature></id-card-signature>
        <mobile-id-signature></mobile-id-signature>
      </v-row>
      <v-row>
        <smart-id-signature></smart-id-signature>
        <zeal-id-signature></zeal-id-signature>
      </v-row>
    </v-col>
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

<style>
  .signing-method-btn {
    /*width:300px;*/
    height:50px !important;
    margin-top:4px;
    margin-bottom:6px;
  }

  .signing-method-btn-img {
    max-width: 200px;
    margin:auto;
  }
</style>
