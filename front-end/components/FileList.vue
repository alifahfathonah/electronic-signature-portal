<template>
  <div>
    <h3>Signed files</h3>
    <v-list-item-group>
      <v-list-item>
        <v-list-item-icon>
          <v-icon>mdi-file</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>very_important_contract.pdf</v-list-item-title>
          <v-list-item-subtitle>125kb</v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action>
          <v-btn icon>
            <v-icon>mdi-download</v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
      <v-list-item>
        <v-list-item-icon>
          <v-icon>mdi-file</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>annex_1.pdf</v-list-item-title>
          <v-list-item-subtitle>311kb</v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action>
          <v-btn icon>
            <v-icon>mdi-download</v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list-item-group>
  </div>
</template>

<script>
export default {
  name: 'ShowFile',
  props: ['content']
}
</script>

<style scoped>

</style>
