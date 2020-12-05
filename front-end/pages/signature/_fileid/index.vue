<template>
  <div>
    <show-file></show-file>
    <signature-methods></signature-methods>
  </div>
</template>

<script>
import ShowFile from '@/components/ShowFile'
import SignatureMethods from '@/components/signature/SignatureMethods'

export default {
  components: { SignatureMethods, ShowFile }
}
</script>

<style>
</style>
