<template>
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="mx-auto max-w-lg py-10">
      <Link href="/">
        <h1 class="text-4xl font-semibold">Fedi.to</h1>
      </Link>

      <div class="my-6 p-2 rounded-lg bg-indigo-800">
        <div class="flex gap-2">
          <!-- Avatar -->
          <div class="flex-shrink-0 w-20">
            <div class="aspect-square bg-indigo-500 rounded overflow-hidden">
              <img :src="account.avatar_url" />
            </div>
          </div>

          <!-- Main content -->
          <div class="flex-1 space-y-1">
            <div class="flex">
              <a :href="account.profile_url" target="_blank" class="group" rel="noopener nofollow">
                <div>
                  <div class="text-lg font-bold text-indigo-100 group-hover:text-white">
                    {{ account.display_name }}
                  </div>
                  <div class="text-sm text-indigo-300 group-hover:text-indigo-100 group-hover:underline">
                    @{{ account.display_handle }}
                  </div>
                </div>
              </a>

              <div class="ml-auto text-right" v-if="loggedIn">
                <form @submit.prevent="follow" v-if="!following">
                  <button
                    class="bg-green-700 hover:bg-green-600 px-2 py-1 rounded border border-green-600 hover:border-green-500 font-bold transition-colors"
                    :disabled="followLoading">
                    <div class="space-x-1" v-if="followLoading">
                      <fa icon="fa-solid fa-circle-notch" class="animate-spin" />
                      <span>Following...</span>
                    </div>
                    <div class="space-x-1" v-else>
                      <fa icon="fa-solid fa-user-plus" />
                      <span>Follow</span>
                    </div>
                  </button>
                </form>

                <div v-else>
                  <div
                    class="bg-indigo-700 hover:bg-indigo-600 px-2 py-1 rounded border border-indigo-600 hover:border-indigo-500 font-bold transition-colors space-x-1">
                    <fa icon="fa-solid fa-check" />
                    <span>Following</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="text-indigo-100 summary" v-html="sanitize(account.summary)" />
            <div class="flex gap-4 text-indigo-300">
              <div v-if="account.following_count">
                <strong class="text-white">{{ formatNumber(account.following_count) }}</strong>
                Following
              </div>
              <div v-if="account.followers_count">
                <strong class="text-white">{{ formatNumber(account.followers_count) }}</strong>
                Followers
              </div>
              <div v-if="account.statuses_count">
                <strong class="text-white">{{ formatNumber(account.statuses_count) }}</strong>
                Posts
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
:deep(.summary a) {
  @apply underline;
}
</style>

<script setup>
import { Inertia } from '@inertiajs/inertia';
import { Link } from '@inertiajs/inertia-vue3';
import sanitizeHtml from 'sanitize-html';
import { ref, computed, onUnmounted } from 'vue';

const props = defineProps({
  account: Object,
});

const updatedAccount = ref(null);
const account = computed(() => updatedAccount.value || props.account);

// Re-fetch the account to look for updated data
onUnmounted(Inertia.on('navigate', async () => {
  let response = await fetch(`/api/accounts/${account.value.id}`);
  let { data } = await response.json();
  updatedAccount.value = data;
}));

const loggedIn = ref(true);
const following = ref(false);
const followLoading = ref(false);

function follow() {
  //
}

function formatNumber(number) {
  return new Intl.NumberFormat(undefined, {
    notation: 'compact',
    compactDisplay: 'short',
    maximumFractionDigits: 1,
  }).format(number);
}

function sanitize(value) {
  return sanitizeHtml(value, {
    allowedTags: [
      'a',
      'br',
      'em',
      'i',
      'p',
      'span',
      'strong',
    ],
    transformTags: {
      'a': (tagName, attribs) => {
        return {
          tagName,
          attribs: {
            ...attribs,
            target: '_blank',
            rel: 'nofollow noopener noreferrer',
          },
        };
      },
    },
  });
}
</script>
