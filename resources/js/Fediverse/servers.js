import { useLocalStorage } from '@vueuse/core';
import { oauthReturnUrl, scopes } from './common';
import MastodonApi from './MastodonApi';
import delay from 'delay';

const servers = useLocalStorage('auth/servers', []);

// Get or create a server record
export async function getServer(domain) {
  domain = domain.trim().toLowerCase();

  let server = servers.value.find(s => s.domain === domain);

  if (!server) {
    server = await _createServer(domain);

    // Wait a moment in case there's a replication delay
    await delay(500);
  }

  return server;
}

// Create a server record
async function _createServer(domain) {
  let server = {
    domain,
    app: await _createApp(domain),
  };

  servers.value.push(server);

  return server;
}

// Create an app using the Mastodon API
async function _createApp(domain) {
  let clientName = import.meta.env.VITE_APP_NAME;
  let website = import.meta.env.VITE_APP_URL;

  let client = new MastodonApi(domain);

  try {
    return await client.post('api/v1/apps', {
      client_name: clientName,
      website: website,
      redirect_uris: oauthReturnUrl,
      scopes,
    });
  } catch (e) {
    console.error(e.data);

    throw new Error('Error creating app');
  }
}
