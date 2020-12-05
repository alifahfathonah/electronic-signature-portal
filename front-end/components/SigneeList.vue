<template>
  <div>
    <h3>Signees</h3>
    <v-list-item-group>
      <v-list-item>
        <v-list-item-icon>
          <v-icon v-if="$route.query.signed">mdi-account-check</v-icon>
          <v-icon v-else>mdi-account-outline</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>John Smith (38820013128)</v-list-item-title>
          <v-list-item-subtitle v-if="$route.query.signed">Signed 05.12.2020</v-list-item-subtitle>
          <v-list-item-subtitle v-else>Not yet signed</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-list-item>
        <v-list-item-icon>
          <v-icon>mdi-account-outline</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>Tim Berg (38820013128)</v-list-item-title>
          <v-list-item-subtitle>Not yet signed</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
      <v-list-item>
        <v-list-item-icon>
          <v-icon>mdi-account-outline</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>38820013128</v-list-item-title>
          <v-list-item-subtitle>Not yet signed</v-list-item-subtitle>
        </v-list-item-content>
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
