<template>
  <AppLayout>
    <div class="min-h-screen flex items-center">
      <div class="mx-auto max-w-7xl py-16 px-4 sm:py-24 sm:px-6 lg:px-8">
        <div class="text-center">
          <p class="mt-1 text-4xl font-bold tracking-tight text-gray-100 sm:text-5xl lg:text-6xl">
            <fa icon="fa-solid fa-circle-notch" class="animate-spin" />
          </p>
          <p class="mx-auto mt-5 max-w-xl text-xl text-gray-400">
            Finishing login...
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted } from 'vue';
import { finishLogin } from '@/Fediverse/accounts';
import delay from 'delay';

onMounted(async () => {
  let params = new URLSearchParams(window.location.search);
  let code = params.get('code');
  let state = params.get('state');

  // Wait for replication delay
  await delay(1000);

  let { returnUrl } = await finishLogin(code, state);

  window.location.href = returnUrl || '/';
});
</script>
