<template>
  <div class="my-6 p-4 rounded-lg bg-cyan-800 space-y-2">
    <h2 class="text-xl font-semibold">Log In to Follow</h2>
    <div>
      Logging into Fedi.to lets you easily follow accounts across the Fediverse.
      <strong>Enter your Mastodon instance URL below to continue.</strong>
    </div>

    <form @submit.prevent="submit">
      <label for="input_url" class="block text-sm font-medium text-cyan-200">Mastodon URL:</label>

      <div class="mt-1 flex rounded-md shadow-sm text-gray-800">
        <div class="relative flex flex-grow items-stretch focus-within:z-10">
          <input id="input_url" v-model="url" :disabled="submitting"
            class="block w-full rounded-none rounded-l-md border-gray-300 pl-4 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm placeholder:text-gray-400"
            placeholder="mastodon.social, mas.to, etc." />
        </div>
        <button type="submit" :disabled="submitting"
          class="relative -ml-px inline-flex items-center space-x-2 rounded-r-md border border-gray-300 bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500">
          <span>Log In</span>
        </button>
      </div>

      <div v-if="error" class="mt-2 bg-red-800 px-2 py-1 border border-red-600 rounded">
        {{ error }}
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { getAuthorizationUrl } from '@/Fediverse/accounts';

const url = ref('');
const error = ref(null);
const submitting = ref(false);

async function submit() {
  error.value = null;
  submitting.value = true;

  try {
    let authorizationUrl = await getAuthorizationUrl(url.value, window.location.href);
    window.location.href = authorizationUrl;
  } catch (e) {
    error.value = e.message;
  }

  submitting.value = false;
}
</script>
