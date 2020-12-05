<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          Document signing
        </v-card-title>
        <v-card-subtitle>Created on 01.12.2020 by Bilbo Baggins.</v-card-subtitle>
        <v-card-text>
          <p>Document link: <a href="#">https://signing.yousite.com/signature/28hqjUASd2a9S</a>
            <v-btn
            fab
            small
            elevation="1"
          >
            <v-icon>
              mdi-content-copy
            </v-icon>
          </v-btn>
            <br>
            Share this link with anyone who you wish to sign this document.</p>
          <file-list></file-list>
          <signature-methods></signature-methods>
          <signee-list></signee-list>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import FileList from '@/components/FileList'
import SigneeList from '@/components/SigneeList'
import SignatureMethods from '@/components/signature/SignatureMethods'

export default {
  components: { SignatureMethods, FileList, SigneeList }
}
</script>

<style>
</style>
