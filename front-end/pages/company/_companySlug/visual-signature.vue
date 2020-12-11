<template>
  <v-row justify="center" align="center">
    <v-col cols="12" sm="8" md="6">
      <v-card>
        <v-card-title class="headline">
          Upload new PDF for visal signatures
        </v-card-title>
        <v-card-text>
          Select PDF
          <v-file-input
            v-model="file"
            :error-messages="$store.state.ui.validationErrors['file']"
          />
          <h3>Access control</h3>
          <p>Who must sign this document?</p>
          <v-list>
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
              <v-list-item-action style="width: 36px; margin-left: 16px;">
                <v-btn icon @click="removePerson(idx)">
                  <v-icon>mdi-close</v-icon>
                </v-btn>
              </v-list-item-action>
            </v-list-item>
          </v-list>
          <v-card elevation="0" style="background-color: #f1f1f1;" tile>
            <v-card-text>
              <v-text-field
                v-model="newPersonIdentifier"
                label="E-mail"
              />
              <v-row>
                <v-col align="right" class="pt-0 pb-0">
                  <v-btn
                    outlined
                    style="margin-bottom: 6px"
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
          <v-spacer/>
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
  name: 'VisualSignatureVue',
  data: () => ({
    file: null,
    people: [],
    accessControl: 'public',
    newPersonIdentifierType: 'idcode',
    newPersonIdentifier: ''
  }),
  computed: {
    me () {
      return this.$store.state.user.me
    }
  },
  methods: {
    addPerson () {
      const visualCoordinates = {
        x: 10.5,
        y: 30.7,
        width: 50,
        height: 10
      }
      const newPerson = {
        identifier_type: 'email',
        identifier: this.newPersonIdentifier,
        access_level: 'signer',
        visual_coordinates: visualCoordinates
      }
      if (!newPerson.identifier) {
        this.$toast(`Please specify ${newPerson.identifier_type}.`, { color: 'error' })
        return
      }
      const isDuplicate = this.people.some((p) => {
        return newPerson.identifier === p.identifier
      })
      if (isDuplicate) {
        this.$toast('This person was already added.', { color: 'error' })
        return
      }
      this.people.push(newPerson)
      this.$toast('Person added: ' + newPerson.identifier)
      this.newPersonIdentifier = ''
    },
    removePerson (idx) {
      this.people.splice(idx, 1)
    },
    async submit () {
      const files = []

      const read = new FileReader()
      read.readAsDataURL(this.file)
      const base64 = await this.getFileContent(this.file)

      files.push({
        name: this.file.name,
        mime: this.file.type,
        content: base64
      })

      try {
        const container = await this.$store.dispatch('container/uploadVisualSignature', { files, people: this.people })

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

<style scoped>

</style>
