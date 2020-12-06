<template>
  <v-row>
    <v-col>
      <v-btn @click="$emit('selectMethod', 'smart-id')" class="login-method-btn" elevation="1">
        <img src="/methods/smart-id.svg" class="login-method-btn-img"/>
      </v-btn>
      <v-btn @click="$emit('selectMethod', 'mobile-id')" class="login-method-btn" elevation="1">
        <img src="/methods/eid_mobiilid_mark.png" class="login-method-btn-img"/>
      </v-btn>
      <v-btn @click="$emit('selectMethod', 'id-card')" class="login-method-btn" elevation="1">
        <img src="/methods/eid_idkaart_mark.png" class="login-method-btn-img"/>
      </v-btn>
      <v-btn @click="$emit('selectMethod', 'id-card')" class="login-method-btn" elevation="1">
        <img src="/methods/latvia-id-card.png" class="login-method-btn-img"/>
      </v-btn>
      <v-btn @click="$emit('selectMethod', 'id-card')" class="login-method-btn" elevation="1">
        Lithuanian ID card
      </v-btn>
      <v-btn @click="$emit('selectMethod', 'id-card')" class="login-method-btn" elevation="1">
        Finnish ID card
      </v-btn>
      <v-btn @click="$emit('selectMethod', 'id-card')" class="login-method-btn" elevation="1">
        Belgian ID card
      </v-btn>
      <v-btn @click="$emit('selectMethod', 'id-card')" class="login-method-btn" elevation="1">
        LuxTrust ID card
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
