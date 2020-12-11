<template>
  <v-card align="center">
    <v-card-title class="headline">
      <v-spacer />
      <template v-if="!method">
        Please log in
      </template>
      <template v-else>
        Logging in...
      </template>
      <v-spacer />
    </v-card-title>
    <v-card-text>
      <p v-if="!method">
        Select your preferred log in method from the list below.
      </p>

      <!-- Show login methods -->
      <login-method-list v-if="!method" @selectMethod="selectMethod" />

      <!-- Show ID card tip -->
      <template v-if="method === 'id-card'">
        <p>For ID card login you need to have your local country card software installed. If you they're not yet installed then get from <a href="https://id.ee">www.id.ee</a>.</p>
      </template>

      <!-- Ask for more details (smart-id or mobile-id) -->
      <template v-if="method === 'password'">
        <v-text-field
          v-model="email"
          type="email"
          label="Email"
          :disabled="disable"
          :error-messages="$store.state.ui.validationErrors['email']"
        />
        <v-text-field
          v-model="password"
          type="password"
          label="Password"
          :disabled="disable"
          :error-messages="$store.state.ui.validationErrors['password']"
        />
      </template>

      <!-- Ask for more details (smart-id or mobile-id) -->
      <template v-if="twoStepPromptingMoreInfo">
        <v-text-field
          v-model="idCode"
          label="Identity Code"
          :disabled="disable"
          :error-messages="$store.state.ui.validationErrors['idcode']"
        />
        <v-text-field
          v-if="method === 'mobile-id'"
          v-model="phone"
          label="Phone number"
          :disabled="disable"
          :error-messages="$store.state.ui.validationErrors['phone']"
        />
        <v-select
          v-model="country"
          :items="countrySelect"
          label="Country"
          :disabled="disable"
          :error-messages="$store.state.ui.validationErrors['country']"
        />
      </template>

      <!-- Show challenge (smart-id or mobile-id) -->
      <template v-if="challenge">
        <p>After ensuring that the challenge below matches the one displayed in your mobile device, please enter PIN1 on your device.</p>
        <p>Challenge: <b>{{ challenge }}</b>.</p>
      </template>
    </v-card-text>
    <v-card-actions>
      <v-btn v-if="!!method" @click="reset">
        Back
      </v-btn>
      <v-spacer />
      <v-btn v-if="twoStepPromptingMoreInfo" color="primary" @click="beginTwoStepLogin">
        Next
      </v-btn>
      <v-btn v-if="method==='password'" color="primary" @click="beginSimpleLogin">
        Login
      </v-btn>
    </v-card-actions>
  </v-card>
</template>

<script>
import LoginMethodList from '@/components/login/LoginMethodList'

export default {
  components: { LoginMethodList },
  props: {
    companySlug: String,
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
    country: null,

    // Login details
    email: null,
    password: null
  }),
  computed: {
    twoStepPromptingMoreInfo () {
      return ['smart-id', 'mobile-id'].includes(this.method) && !this.challenge
    },
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
        this.reset()
      }
    }
  },
  methods: {
    reset () {
      this.method = null
      this.challenge = null
      this.token = null
    },
    async selectMethod (method, country) {
      this.method = method
      // With ID card, we will begin login process. With other
      // methods, we need to gather more data from user.
      if (method === 'id-card') {
        try {
          const readResponse = await this.$axios.get(`https://${country}${process.env.EID_IDCARD_URL}/api/identity/${process.env.EID_CLIENT_ID}/read-card`)
          const response = await this.$axios.post('api/authenticate/id-card', {
            token: readResponse.data.token,
            country
          })

          if (response.data.status === 'OK') {
            this.$store.commit('user/setMe', { me: response.data.user })
            this.$toast('Logged in!', { color: 'success' })
            this.$emit('loggedIn')
          } else {
            this.$toast(response.data.status, { color: 'error' })
            this.reset()
          }
        } catch (e) {
          console.error('ID card login error', e)
          this.$toast('Login failed', { color: 'error' })
          this.reset()
        }
      }
    },
    async beginSimpleLogin () {
      let response = null

      try {
        response = await this.$axios.post('api/password/login', {
          email: this.email,
          password: this.password
        })
      } catch (e) {
        console.error('Login error', e)
        this.$toast('Login failed: ' + e.response.data.message, { color: 'error' })
        this.reset()
        return
      }

      if (response.data.status === 'OK') {
        this.$store.commit('user/setMe', { me: response.data.user })
        this.$toast('Logged in!', { color: 'success' })
        this.$emit('loggedIn')
      } else {
        this.$toast(response.data.status, { color: 'error' })
        this.reset()
      }
    },
    async beginTwoStepLogin () {
      let response = null

      try {
        response = await this.$axios.post(`api/authenticate/${this.method}/start`, {
          idcode: this.idCode,
          phone: this.method === 'mobile-id' ? this.phone : undefined,
          country: this.country
        })
      } catch (e) {
        console.error('Login error', e)
        this.$toast('Login failed', { color: 'error' })
        this.reset()
        return
      }

      this.challenge = response.data.challenge
      this.token = response.data.token

      try {
        const response = await this.$axios.post(`api/authenticate/${this.method}/finish`, {
          token: this.token
        })

        if (response.data.status === 'OK') {
          this.$store.commit('user/setMe', { me: response.data.user })
          this.$toast('Logged in!', { color: 'success' })
          this.$emit('loggedIn')
        } else {
          this.$toast(response.data.status, { color: 'error' })
          this.reset()
        }
      } catch (e) {
        console.error('Login error', e)
        this.$toast('Login failed', { color: 'error' })
        this.reset()
      }
    }
  }
}
</script>
