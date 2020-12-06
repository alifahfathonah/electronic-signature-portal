<template>
  <v-card align="center">
    <v-card-title class="headline">
      <v-spacer/>
        Logging in...
      <v-spacer/>
    </v-card-title>
    <v-card-text>

      <!-- Show login methods -->
      <login-method-list v-if="!method"></login-method-list>

      <!-- Show ID card tip -->
      <template v-if="method === 'id-card'">
        <p>For ID card login you need to have your local country card software installed. If you they're not yet installed then get from <a href="https://id.ee">www.id.ee</a>.</p>
      </template>

      <!-- Ask for more details (smart-id or mobile-id) -->
      <template v-if="['smart-id', 'mobile-id'].includes(method) && !challenge">
        <v-text-field
          v-model="idCode"
          label="Identity Code"
          :disabled="disable"
        ></v-text-field>
        <v-text-field
          v-if="method === 'mobile-id'"
          v-model="phone"
          label="Phone number"
          :disabled="disable"
        ></v-text-field>
        <v-select
          v-model="country"
          :items="countrySelect"
          label="Country"
          :disabled="disable"
        ></v-select>
      </template>

      <!-- Show challenge (smart-id or mobile-id) -->
      <template v-if="challenge">
        <p>Challenge: <b>{{ challenge }}</b>.</p>
        <p>After ensuring that the challenge above matches the one displayed in your mobile device, please enter PIN1 on your device.</p>
      </template>

    </v-card-text>
    <v-card-actions>
      <v-spacer/>
      <v-btn @click="beginTwoStepLogin">Next</v-btn>
      <v-spacer/>
    </v-card-actions>
  </v-card>
</template>

<script>
import LoginMethodList from '@/components/login/LoginMethodList'

export default {
  components: { LoginMethodList },
  props: {
    isOpen: Boolean
  },
  data: () => ({
    method: null,
    challenge: null,
    token: null,
    disable: false,

    // User details
    idCode: null,
    phoneNr: null,
    country: null
  }),
  computed: {
    countrySelect () {
      if (this.method === 'mobile-id') {
        return [
          {
            text: 'Estonia',
            value: 'EE'
          },
          {
            text: 'Lithuania',
            value: 'LT'
          }
        ]
      }

      return [
        {
          text: 'Estonia',
          value: 'EE'
        },
        {
          text: 'Latvia',
          value: 'LV'
        },
        {
          text: 'Lithuania',
          value: 'LT'
        }
      ]
    }
  },
  watch: {
    isOpen (isOpen) {
      if (!isOpen) {
        this.method = null
        this.challenge = null
      }
    }
  },
  methods: {
    async selectMethod (method) {
      this.method = method
      // With ID card, we will begin login process. With other
      // methods, we need to gather more data from user.
      if (method === 'id-card') {
        await this.$axios.post('api/authenticate/id-card/start')
      }
    },
    async beginTwoStepLogin () {
      const response = await this.$axios.post(`api/authenticate/${this.method}/start`, {
        body: {
          idcode: this.idCode,
          phone: this.method === 'mobile-id' ? this.phone : undefined,
          country: this.country
        }
      })

      this.challenge = response.data.challenge
      this.token = response.data.token

      const ticker = setInterval(async () => {
        try {
          const response = await this.$axios.post(`api/authenticate/${this.method}/finish`, {
            body: {
              token: this.token
            }
          })

          if (response.data.status === 'OK') {
            clearInterval(ticker)
            alert('LOGGED IN!')
          }
        } catch (e) {
          // TODO handle errors
          console.error(e)
        }
      })
    }
  }
}
</script>
