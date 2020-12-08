<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          Config
        </v-card-title>
        <v-card-text>
          <h3>eID Easy credentials</h3>
          <v-text-field
            label="API key"
            required
          ></v-text-field>
          <v-text-field
            label="API secret"
            required
          ></v-text-field>
          <br>
          <h3>Access control</h3>
          <p>Logged in users can create documents. Document creator can choose to only allow signed-in users to view and sign a document.</p>
          <v-radio-group
            column
            v-model="accessControl"
          >
            <v-radio
              label="Anyone can log in."
              value="anyone"
            ></v-radio>
            <v-radio
              label="Only admins can log in."
              value="only-admins"
            ></v-radio>
            <v-radio
              label="Only these people can log in:"
              value="only-some"
            ></v-radio>
          </v-radio-group>
          <v-list-item-group>
            <v-list-item>
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>39304040012</v-list-item-title>
                  <v-list-item-subtitle>Bilbo Baggins</v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action>
                <v-btn icon>
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
            <v-list-item>
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>38801100121</v-list-item-title>
              </v-list-item-content>
              <v-list-item-action>
                <v-btn icon>
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-list-item-group>
          <v-text-field
            label="Social security number"
          >
            <template slot="append">
              <v-btn outlined style="margin-bottom: 6px">
                <v-icon left>mdi-plus</v-icon>
                Add person
              </v-btn>
            </template>
          </v-text-field>
          <br>
          <h3>Admins</h3>
          <p>It's a pleasure to give in to temptations when these temptations actually make your life better. That's what we do here.</p>
          <v-list-item-group>
            <v-list-item>
              <v-list-item-icon>
                <v-icon>mdi-account-key</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>39304040012</v-list-item-title>
                <v-list-item-subtitle>Bilbo Baggins</v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action>
                <v-btn icon>
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
            <v-list-item>
              <v-list-item-icon>
                <v-icon>mdi-account-key</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>38801100121</v-list-item-title>
              </v-list-item-content>
              <v-list-item-action>
                <v-btn icon>
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-list-item-group>
          <v-text-field
            label="Social security number"
          >
            <template slot="append">
              <v-btn outlined style="margin-bottom: 6px">
                <v-icon left>mdi-plus</v-icon>
                Add admin
              </v-btn>
            </template>
          </v-text-field>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            nuxt
            to="/signature/create-document"
          >
            Save!
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
export default {
  data: () => {
    return {
      accessControl: 'only-some'
    }
  }
}
</script>
