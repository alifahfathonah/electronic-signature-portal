<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          Company dashboard
        </v-card-title>
        <v-card-text>
          <v-list>
            <v-list-item>
              <v-list-item-content>
                <router-link :to="`/company/${$route.params.companySlug}/visual-signature`">
                  New visual signature PDF
                </router-link>
              </v-list-item-content>
            </v-list-item>
            <v-list-item>
              <v-list-item-content>
                <router-link :to="`/company/${$route.params.companySlug}/crypto-signature`">
                  New crypto signature documents
                </router-link>
              </v-list-item-content>
            </v-list-item>
          </v-list>
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  methods: {}
}
</script>
