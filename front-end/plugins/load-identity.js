// Load user identity upon app boot. Keep all other things waiting.
export default async (context) => {
  if (process.browser) {
    await context.store.dispatch('user/getMe', context)
  }
}
