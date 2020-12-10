<template>
  <div>
    <h3>Signed files</h3>
    <v-list-item-group>
      <v-list-item v-for="file in filesFormatted" :key="file.id" inactive>
        <v-list-item-icon>
          <v-icon>mdi-file</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title class="">{{ file.name }}</v-list-item-title>
          <v-list-item-subtitle>{{ file.size }}</v-list-item-subtitle>
        </v-list-item-content>
        <v-list-item-action>
          <v-btn
            icon
            :href="file.link"
            target="_blank"
          >
            <v-icon>mdi-download</v-icon>
          </v-btn>
        </v-list-item-action>
      </v-list-item>
    </v-list-item-group>
  </div>
</template>

<script>
import filesize from 'filesize'

export default {
  props: {
    files: {
      type: Array,
      default: () => ([])
    },
    containerId: {
      type: String,
      default: () => ('undefined')
    }
  },
  computed: {
    filesFormatted () {
      return this.files.map((f) => {
        return {
          id: f.id,
          name: f.name,
          link: `${this.$axios.defaults.baseURL}/api/container/${this.containerId}/download?file_name=${f.name}`,
          size: filesize(f.size)
        }
      })
    }
  }
}
</script>
