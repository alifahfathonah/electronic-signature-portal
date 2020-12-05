<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          Create new document
        </v-card-title>
        <v-card-text>
          Select files
          <v-file-input
            multiple
          ></v-file-input>
          <h3>Access control</h3>
          <p>Who can sign this document?</p>
          <v-radio-group
            column
            v-model="accessControl"
          >
            <v-radio
              label="Anyone who has the link."
              value="anyone"
            ></v-radio>
            <v-radio
              label="Only logged in users."
              value="only-logged-in"
            ></v-radio>
            <v-radio
              label="Only these people:"
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
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            nuxt
            to="/signature/28hqjUASd2a9S"
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
