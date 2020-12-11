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
            v-model="clientId"
            label="Client ID"
            :placeholder="selectedCompany && selectedCompany.eid_client_id"
            required
            :error-messages="$store.state.ui.validationErrors['eid_client_id']"
          />
          <v-text-field
            v-model="secret"
            label="Secret"
            :placeholder="selectedCompany && selectedCompany.eid_secret"
            required
            :error-messages="$store.state.ui.validationErrors['eid_secret']"
          />
          <br>
          <h3>Access control <i>(in development)</i></h3>
          <p>Specify who can create documents. Document creator can choose to only allow signed-in users to view and sign a document.</p>
          <v-radio-group
            v-model="accessControl"
            column
            disabled
          >
            <v-radio
              label="Anyone can log in."
              value="public"
            />
            <v-radio
              label="Only admins can log in."
              value="only-admins"
            />
            <v-radio
              label="Only these people can log in:"
              value="only-some"
            />
          </v-radio-group>
          <!--          <v-list>-->
          <!--            <v-list-item>-->
          <!--              <v-list-item-icon>-->
          <!--                <v-icon>mdi-account</v-icon>-->
          <!--              </v-list-item-icon>-->
          <!--              <v-list-item-content>-->
          <!--                <v-list-item-title>39304040012</v-list-item-title>-->
          <!--                  <v-list-item-subtitle>Bilbo Baggins</v-list-item-subtitle>-->
          <!--              </v-list-item-content>-->
          <!--              <v-list-item-action>-->
          <!--                <v-btn icon>-->
          <!--                  <v-icon>mdi-close</v-icon>-->
          <!--                </v-btn>-->
          <!--              </v-list-item-action>-->
          <!--            </v-list-item>-->
          <!--            <v-list-item>-->
          <!--              <v-list-item-icon>-->
          <!--                <v-icon>mdi-account</v-icon>-->
          <!--              </v-list-item-icon>-->
          <!--              <v-list-item-content>-->
          <!--                <v-list-item-title>38801100121</v-list-item-title>-->
          <!--              </v-list-item-content>-->
          <!--              <v-list-item-action>-->
          <!--                <v-btn icon>-->
          <!--                  <v-icon>mdi-close</v-icon>-->
          <!--                </v-btn>-->
          <!--              </v-list-item-action>-->
          <!--            </v-list-item>-->
          <!--          </v-list>-->
          <v-text-field
            label="Social security number"
            disabled
          >
            <template slot="append">
              <v-btn outlined style="margin-bottom: 6px" disabled>
                <v-icon left>
                  mdi-plus
                </v-icon>
                Add person
              </v-btn>
            </template>
          </v-text-field>
          <br>
          <h3>Admins <i>(in development)</i></h3>
          <p>It's a pleasure to give in to temptations when these temptations actually make your life better. That's what we do here.</p>
          <!--          <v-list>-->
          <!--            <v-list-item>-->
          <!--              <v-list-item-icon>-->
          <!--                <v-icon>mdi-account-key</v-icon>-->
          <!--              </v-list-item-icon>-->
          <!--              <v-list-item-content>-->
          <!--                <v-list-item-title>39304040012</v-list-item-title>-->
          <!--                <v-list-item-subtitle>Bilbo Baggins</v-list-item-subtitle>-->
          <!--              </v-list-item-content>-->
          <!--              <v-list-item-action>-->
          <!--                <v-btn icon>-->
          <!--                  <v-icon>mdi-close</v-icon>-->
          <!--                </v-btn>-->
          <!--              </v-list-item-action>-->
          <!--            </v-list-item>-->
          <!--            <v-list-item>-->
          <!--              <v-list-item-icon>-->
          <!--                <v-icon>mdi-account-key</v-icon>-->
          <!--              </v-list-item-icon>-->
          <!--              <v-list-item-content>-->
          <!--                <v-list-item-title>38801100121</v-list-item-title>-->
          <!--              </v-list-item-content>-->
          <!--              <v-list-item-action>-->
          <!--                <v-btn icon>-->
          <!--                  <v-icon>mdi-close</v-icon>-->
          <!--                </v-btn>-->
          <!--              </v-list-item-action>-->
          <!--            </v-list-item>-->
          <!--          </v-list>-->
          <v-text-field
            label="Social security number"
            disabled
          >
            <template slot="append">
              <v-btn outlined style="margin-bottom: 6px" disabled>
                <v-icon left>
                  mdi-plus
                </v-icon>
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
            @click="skipConfig"
          >
            Skip!
          </v-btn>
          <v-btn
            color="primary"
            nuxt
            @click="save"
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
      clientId: null,
      secret: null,
      accessControl: 'only-admins'
    }
  },
  computed: {
    selectedCompany () {
      return this.$store.state.company.selectedCompany
    }
  },
  beforeCreate () {
    // Check that I have admin rights to this company.
    if (this.$store.state.company.selectedCompany.role !== 'ADMIN') {
      this.$toast('You can\'t access this company!', { color: 'error' })
      this.$router.replace('/')
    }
  },
  methods: {
    skipConfig () {
      const companySlug = this.$route.params.companySlug
      this.$router.replace(`/company/${companySlug}`)
    },
    async save () {
      const companySlug = this.$route.params.companySlug

      try {
        await this.$axios.put(`api/company/${companySlug}/`, {
          eid_client_id: this.clientId || undefined,
          eid_secret: this.secret || undefined
        })
      } catch (e) {
        return
      }

      this.$toast('Success!', { color: 'success' })
    }
  }
}
</script>
