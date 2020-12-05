<template>
  <v-col>
    <v-btn block class="signing-method-btn"  elevation="1" @click="$emit('beginSigning')">
      <img src="/methods/eid_mobiilid_mark.png" class="method-height signing-method-btn-img">
    </v-btn>
  </v-col>
</template>

<script>
export default {
  name: 'MobileIdSignature'
}
</script>

<style scoped>

</style>
