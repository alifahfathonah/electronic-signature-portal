export default function ({ $axios, store }) {
  $axios.onError((error) => {
    // If we're dealing with validation error, add errors to store.
    // if (error.response.status === 422) {
    //   alert('validation error!')
    // }
    alert(error)
  })
}
