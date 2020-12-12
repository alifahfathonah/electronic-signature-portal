<template>
  <v-container id="pdf-wrapper" class="mt-0">
    <v-row class="text-center text-h4 ml-2">
      Add signature on your document
    </v-row>
    <hr class="my-2">
    <v-row>
      <v-col id="pdf-options" class="flex-column" cols="3">
        <div class="text-h5 mb-4">
          Tools:
        </div>
        <v-btn class="mb-4" @click="addSignature">
          Add signature
        </v-btn>
        <br>
        <v-btn @click="addSignature">
          Add placeholder
        </v-btn>
        <v-btn class="mt-12" color="primary" @click="sendRequest">
          Send
        </v-btn>
      </v-col>
      <v-col id="pdf-viewer-outer">
        <div id="pdf-viewer">
          <Moveable
            v-for="item in dndItems"
            :ref="item.id"
            :key="item.id"
            class="moveable"
            v-bind="item.moveable"
            style="position: absolute; width: 200px; height: 120px; z-index: 1; text-align: center"
            @drag="handleDrag($event, item.id)"
            @resize="handleResize"
          >
            <img src="/signature.png" style="max-width: 100%; max-height: 100%" alt="">
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
  </v-container>
</template>

<style>
#pdf-wrapper{
  margin-top: 0;
  padding-bottom: 8px;
  background: #cdcdcd;
}
#pdf-viewer-outer{
  position: relative;
  max-height: 80vh;
  overflow-y: scroll;
  background: #f1f1f1;
  padding: 20px 0;
}
#pdf-viewer{
  padding: 0 32px;
  min-height: 400px;
  min-width: 100%;
}
#pdf-options{
  padding: 12px 30px;
}
</style>

<script>
import Moveable from 'vue-moveable'
import pdf from 'vue-pdf'

export default {
  components: {
    pdf, Moveable
  },
  data: () => ({
    widthRef: 210,
    heightRef: 297,
    counter: 0,
    numPages: null,
    document: pdf.createLoadingTask('/3-pages.pdf'), // TODO: feed pdf dynamically
    dndItems: [],
    currentItem: null
  }),
  mounted () {
    this.document.promise.then((pdf) => {
      this.numPages = pdf.numPages
    })
    window.addEventListener('keydown', (e) => {
      this.handleButtonPress(e)
    })
  },
  methods: {
    handleButtonPress (e) {
      switch (e.key) {
        case 'Backspace':
        case 'Delete':
          e.preventDefault()
          if (!this.currentItem) {
            break
          }
          this.dndItems = this.dndItems.filter(item => item.id !== this.currentItem)
          break
        case 'z':
          e.preventDefault()
          if (e.ctrlKey) {
            // TODO: add some buffer to rollback latest changes
            alert('Unlock premium feature for only 25€ a month!')
          }
          break
      }
    },
    handleDrag ({ target, transform }, id) {
      // TODO: add check for item being completely inside of any page
      this.selectItem(id)
      target.style.transform = transform
    },
    handleResize ({ target, width, height, delta }) {
      delta[0] && (target.style.width = `${width}px`)
      delta[1] && (target.style.height = `${height}px`)
    },
    addSignature () {
      this.dndItems.push(
        {
          id: 'moveable' + this.counter++,
          type: 'signature',
          moveable: {
            draggable: true,
            resizable: false,
            snappable: true,
            snapVertical: true,
            snapHorizontal: true,
            snapElement: true,
            container: document.querySelector('#pdf-viewer'),
            elementGuidelines: Array.from(document.querySelectorAll('.pdf-page'))
          }
        }
      )
    },
    selectItem (id) {
      if (this.currentItem === id) {
        return
      }

      const last = this.dndItems.find(x => x.id === this.currentItem)
      if (this.currentItem != null && last) {
        last.moveable.resizable = false
      }

      this.currentItem = id
      this.dndItems.find(x => x.id === id).moveable.resizable = true
    },
    sendRequest () {
      const output = []

      const pages = Array.from(document.body.querySelectorAll('.pdf-page')).map(v => v.getBoundingClientRect())
      for (const item of this.dndItems) {
        const itemBounds = this.$refs[item.id][0].$el.getBoundingClientRect()

        for (let i = 0; i < pages.length; i++) {
          const pageBounds = pages[i]

          if (itemBounds.top <= pageBounds.bottom) {
            const widthRatio = (pageBounds.right - pageBounds.left) / this.widthRef
            const heightRatio = (pageBounds.bottom - pageBounds.top) / this.heightRef
            const x = (itemBounds.left - pageBounds.left) / widthRatio
            const y = (itemBounds.top - pageBounds.top) / heightRatio
            const width = (itemBounds.right - itemBounds.left) / widthRatio
            const height = (itemBounds.bottom - itemBounds.top) / heightRatio
            output.push({
              page: i + 1,
              x,
              y,
              width,
              height
            })
            break
          }
        }
      }

      // TODO: change output to match endpoint needs and use axios
      console.log(output)
    }
  }
}
</script>
