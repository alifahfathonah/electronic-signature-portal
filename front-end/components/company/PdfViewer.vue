<template>
  <v-row>
    <v-col id="pdf-viewer-outer">
      <div v-if="document" id="pdf-viewer">
        <Moveable
          v-for="item in dragItems"
          :ref="item.id"
          :key="item.id"
          class="moveable"
          :style="{transform: item.transformOrigin}"
          v-bind="item.moveable"
          @drag-start="handleDragStart($event, item.id)"
          @drag="handleDrag($event, item.id)"
          @drag-end="handleDragEnd($event, item.id)"
          @resize="handleResize"
        >
          <div v-if="item.title" class="signature-header">
            {{ item.title }}
          </div>
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
  </v-row>
</template>

<style>
#pdf-wrapper{
  margin-top: 0;
  padding-bottom: 8px;
  background: #cdcdcd;
}
#pdf-viewer-outer{
  position: relative;
  max-height: 82vh;
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

<script>
/* eslint-disable */

import Moveable from 'vue-moveable'
import pdf from 'vue-pdf'

const toBase64 = file => new Promise((resolve, reject) => {
  const reader = new FileReader();
  reader.readAsDataURL(file);
  reader.onload = () => resolve(reader.result);
  reader.onerror = error => reject(error);
});

export default {
  components: {
    pdf, Moveable
  },
  data: () => ({
    widthRef: 210,
    heightRef: 297,
    counter: 0,
    numPages: null,
    document: null,
    dragItems: [],
    currentItem: null
  }),
  methods: {
    async loadPdf(file) {
      this.counter = 0
      this.currentItem = null
      this.dragItems = []
      this.document = null
      if(!file){
        return
      }

      let s = await toBase64(file);
      this.document = pdf.createLoadingTask(s)
      this.document.promise.then((pdf) => {
        this.numPages = pdf.numPages
      })
    },
    pdfPages () {
      return Array.from(document.body.querySelectorAll('.pdf-page')).map(v => v.getBoundingClientRect())
    },
    deleteItem (id) {
      this.dragItems = this.dragItems.filter((x) => x.id !== id)
    },
    handleDrag ({ target, transform }, id) {
      target.style.transform = transform
      const itemBounds = target.getBoundingClientRect()

      if(!this.checkItemInBounds(itemBounds)){
        target.style.backgroundColor = 'rgba(255,50,50,0.3)'
      }else{
        target.style.backgroundColor = ''
      }
    },
    handleDragStart (e, id) {
      this.selectItem(id)
      const item = this.dragItems.find(item => item.id === id)
      item.latestDrag = e.target.style.transform
      e.target.style.cursor = 'grabbing'
    },
    handleDragEnd (e, id) {
      const item = this.dragItems.find(item => item.id === id)
      const itemBounds = e.target.getBoundingClientRect()

      if(!this.checkItemInBounds(itemBounds)){
        e.target.style.transform = item.latestDrag
        e.target.style.backgroundColor = ''
      }
      e.target.style.cursor = 'default'
    },
    handleResize ({ target, width, height, delta }) {
      delta[0] && (target.style.width = `${width}px`)
      delta[1] && (target.style.height = `${height}px`)
    },
    checkItemInBounds(itemBounds){
      let pdfPages = this.pdfPages();
      const page1 = pdfPages[0]
      if (page1.left-1 >= itemBounds.left || page1.right+1 <= itemBounds.right) {
        return false
      }

      for (let i = 0; i < pdfPages.length; i++) {
        const pageBounds = pdfPages[i]
        if (itemBounds.top >= pageBounds.top && itemBounds.bottom <= pageBounds.bottom) {
          return true
        }
      }
      return false
    },
    addItemOnPdf (type, title) {
      const viewer = document.querySelector('#pdf-viewer-outer')
      const id = 'moveable' + this.counter++;
      this.dragItems.push(
        {
          id: id,
          type: type,
          title: title,
          transformOrigin: `matrix(1, 0, 0, 1, 0, ${viewer.scrollTop}) translate(0, 0)`,
          moveable: {
            draggable: true,
            resizable: false,
            snappable: true,
            snapVertical: true,
            snapHorizontal: true,
            snapElement: true,
            container: viewer,
            elementGuidelines: Array.from(document.querySelectorAll('.pdf-page'))
          }
        }
      )
      return id
    },
    selectItem (id) {
      if (this.currentItem === id) {
        return
      }

      const last = this.dragItems.find(x => x.id === this.currentItem)
      if (this.currentItem != null && last) {
        last.moveable.resizable = false
      }

      this.currentItem = id
      this.dragItems.find(x => x.id === id).moveable.resizable = true
    },
    getCoordinates () {
      const output = {}

      for (const item of this.dragItems) {
        const itemBounds = this.$refs[item.id][0].$el.getBoundingClientRect()

        const pdfPages = this.pdfPages();
        for (let i = 0; i < pdfPages.length; i++) {
          const pageBounds = pdfPages[i]

          if (itemBounds.top <= pageBounds.bottom) {
            const widthRatio = (pageBounds.right - pageBounds.left) / this.widthRef
            const heightRatio = (pageBounds.bottom - pageBounds.top) / this.heightRef
            const x = (itemBounds.left - pageBounds.left) / widthRatio
            const y = (itemBounds.top - pageBounds.top) / heightRatio
            const width = (itemBounds.right - itemBounds.left) / widthRatio
            const height = (itemBounds.bottom - itemBounds.top) / heightRatio
            output[item.id] = {
              page: i + 1,
              x,
              y,
              width,
              height
            }
            break
          }
        }
      }

      return output
    }
  }
}
</script>
