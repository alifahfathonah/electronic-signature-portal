<template>
  <div>
    <show-file></show-file>
    <signature-methods></signature-methods>
  </div>
</template>

<style>
.input-align-right input {
  text-align: right;
}
</style>
<script>
import ShowFile from '@/components/ShowFile'
import SignatureMethods from '@/components/signature/SignatureMethods'

export default {
  components: { SignatureMethods, ShowFile }
}
</script>
