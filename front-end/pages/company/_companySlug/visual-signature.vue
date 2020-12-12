<template>
  <v-row justify="center">
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
            <v-list-item v-for="(person, idx) in signers" :key="person.idcode" inactive :ripple="false">
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
                :rules="[rules.required, rules.email]"
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
    <v-col>
      <v-card>
        <v-card-title>
          PDF preview
        </v-card-title>
        <v-card-text>
          <PdfViewer ref="pdf-preview" />
        </v-card-text>
      </v-card>
    </v-col>
  </v-row>
</template>

<script>
import PdfViewer from '@/components/company/PdfViewer.vue'

export default {
  name: 'VisualSignatureVue',
  components: { PdfViewer },
  data: () => ({
    file: null,
    signers: [],
    rules: {
      required: value => !!value || 'Required.',
      email: (value) => {
        const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
        return pattern.test(value) || 'Invalid e-mail.'
      }
    },
    accessControl: 'public',
    newPersonIdentifierType: 'idcode',
    newPersonIdentifier: ''
  }),
  computed: {
    me () {
      return this.$store.state.user.me
    }
  },
  watch: {
    file (val) {
      this.$refs['pdf-preview'].loadPdf(val)
    }
  },
  methods: {
    addPerson () {
      const visualCoordinates = {
        page: 1,
        x: 10.5,
        y: 30.7,
        width: 50,
        height: 10
      }
      const newSigner = {
        identifier_type: 'email',
        identifier: this.newPersonIdentifier,
        access_level: 'signer',
        visual_coordinates: visualCoordinates
      }
      if (!newSigner.identifier) {
        this.$toast(`Please specify ${newSigner.identifier_type}.`, { color: 'error' })
        return
      }
      const isDuplicate = this.signers.some((p) => {
        return newSigner.identifier === p.identifier
      })
      if (isDuplicate) {
        this.$toast('This person was already added.', { color: 'error' })
        return
      }
      this.signers.push(newSigner)
      newSigner['view-id'] = this.$refs['pdf-preview'].addItemOnPdf('signature', this.newPersonIdentifier)
      this.$toast('Person added: ' + newSigner.identifier)
      this.newPersonIdentifier = ''
    },
    removePerson (idx) {
      this.$refs['pdf-preview'].deleteItem(this.signers[idx].view_id)
      this.signers.splice(idx, 1)
    },
    async submit () {
      const files = []

      // TODO: Do something with coordinates, here is extraction from pdf-viewer
      const coordinates = this.$refs['pdf-preview'].getCoordinates()
      console.log(coordinates)
      this.signers.forEach((signer) => {
        signer.visual_coordinates = coordinates[signer.view_id]
      })

      const read = new FileReader()
      read.readAsDataURL(this.file)
      const base64 = await this.getFileContent(this.file)

      files.push({
        name: this.file.name,
        mime: this.file.type,
        content: base64
      })

      try {
        const container = await this.$store.dispatch('container/uploadVisualSignature', { files, signers: this.signers })

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
