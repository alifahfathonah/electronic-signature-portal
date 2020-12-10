<template>
  <div>
    <h3>Signees</h3>
    <v-list>
      <v-list-item v-for="person in people" :key="person.id">
        <v-list-item-icon>
          <v-icon v-if="person.signed_at">mdi-account-check</v-icon>
          <v-icon v-else>mdi-account-outline</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>
            <template v-if="person.first_name">{{ person.first_name }} {{ person.last_name }}</template>
            <template v-if="person.idcode">
              <template v-if="person.first_name">({{ person.idcode }})</template>
              <template v-else>{{ person.idcode }}</template>
            </template>
          </v-list-item-title>
          <v-list-item-subtitle v-if="person.signed_at">Signed {{ $moment(person.signed_at).format('DD.MM.Y') }}</v-list-item-subtitle>
          <v-list-item-subtitle v-else>Not yet signed</v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </div>
</template>

<script>
export default {
  props: {
    people: {
      type: Array,
      default: () => ([])
    }
  }
}
</script>
