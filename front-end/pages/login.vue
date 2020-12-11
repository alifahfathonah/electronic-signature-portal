<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <login-dialog-card />
    </v-col>
  </v-row>
</template>

<script>
import LoginDialogCard from '@/components/login/LoginDialogCard'
export default {
  name: 'Login',
  components: { LoginDialogCard }
}
</script>

<style scoped>

</style>
