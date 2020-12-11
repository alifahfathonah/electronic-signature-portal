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
            v-model="files"
            multiple
            :error-messages="$store.state.ui.validationErrors['files']"
          />
          <h3>Access control</h3>
          <p>Who can sign this document?</p>
          <v-radio-group
            v-model="accessControl"
            column
          >
            <v-radio
              label="Anyone who has the link."
              value="public"
            />
            <v-radio
              label="Only these people:"
              value="whitelist"
            />
          </v-radio-group>
          <v-list>
            <v-list-item :disabled="accessControl !== 'whitelist'">
              <v-list-item-icon>
                <v-icon>mdi-account</v-icon>
              </v-list-item-icon>
              <v-list-item-content>
                <v-list-item-title>{{ me.idcode }}</v-list-item-title>
                <v-list-item-subtitle>{{ me.first_name + ' ' + me.last_name }}</v-list-item-subtitle>
              </v-list-item-content>
              <v-list-item-action style="width: 250px; margin-left: 0px;">
                <v-select
                  :items="['owner']"
                  rounded
                  hide-details
                  value="owner"
                  disabled
                  :append-icon="null"
                />
              </v-list-item-action>
            </v-list-item>
            <template v-if="accessControl === 'whitelist'">
              <v-list-item v-for="(person, idx) in people" :key="person.idcode" inactive :ripple="false">
                <v-list-item-icon>
                  <v-icon>mdi-account</v-icon>
                </v-list-item-icon>
                <v-list-item-content>
                  <v-list-item-title>
                    {{ person.identifier }}
                    <template v-if="person.identifier_type === 'idcode'">
                      ({{ person.country }})
                    </template>
                  </v-list-item-title>
                  <v-list-item-subtitle v-if="person.first_name">
                    {{ person.first_name + ' ' + person.last_name }}
                  </v-list-item-subtitle>
                </v-list-item-content>
                <v-list-item-action style="width: 198px;">
                  <v-select
                    v-model="person.access_level"
                    :items="accessLevelOptions"
                    background-color="#f1f1f1"
                    rounded
                    hide-details
                  />
                </v-list-item-action>
                <v-list-item-action style="width: 36px; margin-left: 16px;">
                  <v-btn icon @click="removePerson(idx)">
                    <v-icon>mdi-close</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </template>
          </v-list>
          <v-card elevation="0" style="background-color: #f1f1f1;" tile>
            <v-card-text>
              <v-select
                v-model="newPersonIdentifierType"
                label="Identifier"
                :items="['email', 'idcode']"
                :disabled="accessControl !== 'whitelist'"
                @change="newPersonCountry = null"
              />
              <v-text-field
                v-model="newPersonIdentifier"
                :label="newPersonIdentifierType === 'idcode' ? 'Personal code' : 'E-mail'"
                :disabled="accessControl !== 'whitelist'"
              />
              <v-select
                v-if="newPersonIdentifierType === 'idcode'"
                v-model="newPersonCountry"
                label="Country"
                :items="countryItems"
                :disabled="accessControl !== 'whitelist'"
              />
              <v-row>
                <v-col align="right" class="pt-0 pb-0">
                  <v-btn
                    outlined
                    style="margin-bottom: 6px"
                    :disabled="accessControl !== 'whitelist'"
                    @click="addPerson"
                  >
                    <v-icon left>
                      mdi-plus
                    </v-icon>
                    Add person
                  </v-btn>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
          <br>
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            nuxt
            @click="submit"
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
  data: () => ({
    files: [],
    people: [],
    accessControl: 'public',
    accessLevelOptions: ['signer', 'viewer'],
    newPersonIdentifierType: 'idcode',
    newPersonIdentifier: '',
    newPersonCountry: null,
    countryItems: [
      { text: 'Estonia', value: 'ee' },
      { text: 'Latvia', value: 'lv' },
      { text: 'Lithuania', value: 'lt' }
    ]
  }),
  computed: {
    me () {
      return this.$store.state.user.me
    }
  },
  methods: {
    addPerson () {
      const newPerson = {
        identifier_type: this.newPersonIdentifierType,
        identifier: this.newPersonIdentifier,
        country: this.newPersonCountry,
        first_name: null,
        last_name: null,
        access_level: 'signer'
      }
      // TODO REMOVE.
      if (newPerson.identifier_type === 'email') {
        this.$toast('Email-based access is not available yet.', { color: 'error' })
        return
      }
      if (!newPerson.identifier) {
        this.$toast(`Please specify ${newPerson.identifier_type}.`, { color: 'error' })
        return
      }
      if (newPerson.identifier_type === 'idcode' && !newPerson.country) {
        this.$toast('Please specify country.', { color: 'error' })
        return
      }
      const isDuplicate = this.people.some((p) => {
        if (newPerson.identifier_type !== p.identifier_type) {
          return false
        }
        if (newPerson.identifier === p.identifier && newPerson.country === p.country) {
          return true
        }
        return false
      })
      if (isDuplicate) {
        this.$toast('This person was already added.', { color: 'error' })
        return
      }
      this.people.push(newPerson)
      this.$toast('Person added: ' + newPerson.identifier)
      this.newPersonIdentifier = ''
      this.newPersonCountry = null
    },
    removePerson (idx) {
      this.people.splice(idx, 1)
    },
    async submit () {
      const files = []
      const promises = this.files.map(async (f) => {
        const read = new FileReader()

        read.readAsDataURL(f)

        const base64 = await this.getFileContent(f)

        files.push({
          name: f.name,
          mime: f.type,
          content: base64
        })
      })

      await Promise.all(promises)

      try {
        const container = await this.$store.dispatch('container/uploadFiles', { files, people: this.people })

        this.$router.push(`/signature/${container.public_id}`)
        this.$toast('Container created!', { color: 'success' })
      } catch (e) {}
    },
    getFileContent (file) {
      return new Promise((resolve) => {
        const read = new FileReader()
        read.readAsDataURL(file)
        read.onloadend = () => {
          const base64 = read.result.replace(/data:.*\/.*;base64,/, '')
          resolve(base64)
        }
      })
    }
  }
}
</script>
