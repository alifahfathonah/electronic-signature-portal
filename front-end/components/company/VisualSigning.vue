<template>
  <v-row id="wrapper">
    <v-col id="pdf-viewer-outer">
      <div v-if="document" id="pdf-viewer">
        <Moveable
          v-if="signatureBox"
          ref="signatureHolder"
          class="moveable"
          v-bind="signatureBox.moveable"
          @drag-start="openDialogSignature"
          @drag="handleDrag"
        >
          <div v-if="signatureBox.title" class="signature-header">
            {{ signatureBox.title }}
          </div>
          <img v-if="placeholder" :src="placeholder" style="max-width: 100%; max-height: 100%" alt="">
        </Moveable>
        <v-row v-for="i of numPages" :key="i" class="pdf-page mb-6">
          <pdf
            :page="i"
            :src="document"
            style="width: 100%"
          />
        </v-row>
      </div>
    </v-col>

    <v-dialog
      v-model="signPad"
      max-width="400"
      style="z-index: 9999"
    >
      <v-card style="opacity: 0.95">
        <v-card-text class="pt-5">
          <div class="d-block" style="background-color: #eee">
            <VueSignaturePad id="signature" ref="signaturePad" width="100%" height="240px" />
          </div>
        </v-card-text>
        <v-divider />
        <v-card-actions>
          <v-spacer />
          <v-btn color="secondary" text @click="clearSignature">
            Clear
          </v-btn>
          <v-btn color="primary" text @click="saveSignature">
            Save
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-row>
</template>

<script>
/* eslint-disable */
import Vue from 'vue'
import pdf from 'vue-pdf'
import VueSignature from 'vue-signature-pad'
import Moveable from 'vue-moveable'
Vue.use(VueSignature)

export default {
  name: 'VisualSigning',
  components: { pdf, Moveable },
  data: () => ({
    widthRef: 210,
    heightRef: 297,
    signPad: false,
    signPadDisabled: true, // Must be false for moving `moveable` box
    signatureBox: null,
    placeholder: null,
    numPages: null,
    document: null
  }),
  methods: {
    async loadPdf(file, coords) {
      this.counter = 0
      this.currentItem = null
      this.dragItems = []
      this.document = null
      console.log(file)
      if(!file){
        return
      }

      this.document = pdf.createLoadingTask('data:application/pdf;base64,' + file)
      this.document.promise.then((pdf) => {
        const viewer = document.querySelector('#pdf-viewer-outer')
        this.numPages = pdf.numPages
        this.signatureBox = {
          title: 'Sign here',
          moveable: {
            draggable: true,
            container: viewer,
            translateZ: 0
          }
        }
        this.$nextTick(() => {
          this.signPadDisabled = true
          // TODO: make calculations to find offset, don't forget -12px on x axis(hidden padding from vue-pdf)
          this.$refs['signatureHolder'].request('draggable', { deltaX: -12, deltaY: 0 }, true)
          this.signPadDisabled = false
        })
      })
    },
    handleDrag ({ target, transform }) {
      if(!this.signPadDisabled){
        return
      }
      target.style.transform = transform
    },
    async openDialogSignature() {
      if(this.signPadDisabled){
        return
      }
      this.signPad = true;
      await Vue.nextTick();
      this.$refs.signaturePad.resizeCanvas();
    },
    clearSignature (){
      this.$refs.signaturePad.clearSignature();
    },
    saveSignature (){
      this.signPad = false;
      const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
      this.placeholder = data;
    }
  }
}
</script>

<style scoped>

#wrapper{
  background: #cdcdcd;
}
#pdf-viewer-outer{
  position: relative;
  max-height: 75vh;
  overflow-y: scroll;
  background: #f1f1f1;
  padding: 20px 0;
}
#pdf-viewer{
  padding: 0 32px;
  min-height: 400px;
  min-width: 100%;
}
.moveable{
  position: absolute;
  width: 160px;
  height: 100px;
  z-index: 1;
  text-align: center
}
.signature-header{
  position: absolute;
  left: 0;
  top:0;
  right: 0;
  height: 80%;
  overflow: hidden;
  max-height: 1.4em;
  font-size: 1em;
  color: #ddd;
  background: #0d408e;
  opacity: 0.6;

}
</style>
