<template>
  <div>
    <v-list>
      <v-list-item>
        <v-list-item-content>
          <router-link to="/register-company">
            Register company
          </router-link>
        </v-list-item-content>
      </v-list-item>
      <v-list-item>
        <v-list-item-content>
          <router-link to="/login">
            Login
          </router-link>
        </v-list-item-content>
      </v-list-item>
    </v-list>
  </div>
</template>

<script>
export default {
  name: 'Index'
}
</script>

<style scoped>

</style>
