<template>
  <v-row>
    <v-col>
      <v-btn class="login-method-btn" elevation="1" @click="$emit('selectMethod', 'smart-id')">
        <img src="/methods/smart-id.svg" class="login-method-btn-img">
      </v-btn>
      <v-btn class="login-method-btn" elevation="1" @click="$emit('selectMethod', 'mobile-id')">
        <img src="/methods/eid_mobiilid_mark.png" class="login-method-btn-img">
      </v-btn>
      <v-btn class="login-method-btn" elevation="1" @click="$emit('selectMethod', 'id-card', 'EE')">
        <img src="/methods/eid_idkaart_mark.png" class="login-method-btn-img">
      </v-btn>
      <v-btn class="login-method-btn" elevation="1" @click="$emit('selectMethod', 'id-card', 'LV')">
        <img src="/methods/latvia-id-card.png" class="login-method-btn-img">
      </v-btn>
      <v-btn class="login-method-btn" elevation="1" @click="$emit('selectMethod', 'id-card', 'LT')">
        Lithuanian ID card
      </v-btn>
      <v-btn class="login-method-btn" elevation="1" @click="$emit('selectMethod', 'id-card', 'BE')">
        Belgian ID card
      </v-btn>
      <v-btn @click="$emit('selectMethod', 'password')" class="login-method-btn" elevation="1">
        Login with Password
      </v-btn>
    </v-col>
  </v-row>
</template>

<style scoped>
.login-method-btn {
  width:300px;
  height:50px !important;
  margin-top:4px;
  margin-bottom:6px;
}

.login-method-btn-img {
  max-width: 200px;
  margin:auto;
}
</style>
