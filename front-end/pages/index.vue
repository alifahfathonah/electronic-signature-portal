<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          Electronic signature portal
        </v-card-title>
        <v-card-text>
          <p>Here's some of that sweet talk that we're so well known for.</p>
          <p>It's a pleasure to give in to temptations when these temptations actually make your life better. That's what we do here.</p>
          <p>Give in to the temptation, join the forces of freedom. You can have it all - legally binding signatures AND control over your data.</p>
          <v-text-field
            class="input-align-right"
            placeholder="your-company-name"
            suffix=".portal.eideasy.com"
          />
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            nuxt
            to="/admin/config"
          >
            Let's go!
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<style>
.input-align-right input {
  text-align: right;
}
</style>
