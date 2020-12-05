<template>
  <v-col>
    <v-btn block class="signing-method-btn"  elevation="1" @click="$emit('beginSigning')">
      <img src="/methods/smart-id.svg" class="method-height signing-method-btn-img">
    </v-btn>
  </v-col>
</template>

<script>
export default {
  name: 'SmartIdSignature'
}
</script>

<style scoped>

</style>
