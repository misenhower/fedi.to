<template>
  <div class="my-6 p-4 rounded-lg bg-cyan-800 space-y-2">
    <h2 class="text-xl font-semibold">Your account</h2>
    <div class="flex gap-2" v-for="account in accounts" :key="account.data.id">
      <!-- Avatar -->
      <div class="flex-shrink-0 w-12">
        <div class="aspect-square bg-cyan-500 rounded overflow-hidden">
          <img :src="account.data.avatar" />
        </div>
      </div>

      <!-- Main content -->
      <div class="flex-1 space-y-1">
        <div class="flex">
          <a href="accountLogin.account.profile_url" target="_blank" class="group" rel="noopener nofollow">
            <div>
              <div class="text-lg font-bold text-cyan-100 group-hover:text-white">
                {{ account.data.display_name }}
              </div>
              <div class="text-sm text-cyan-300 group-hover:text-cyan-100 group-hover:underline">
                @{{ account.data.acct }}@{{ account.domain }}
              </div>
            </div>
          </a>

          <div class="ml-auto">
            <form @submit.prevent="logout(account)">
              <button
                class="bg-cyan-600 hover:bg-red-600 px-2 py-1 rounded border border-cyan-500 hover:border-red-500 font-bold transition-colors text-sm">
                Log Out
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { accounts, logout } from '@/Fediverse/accounts';
</script>
