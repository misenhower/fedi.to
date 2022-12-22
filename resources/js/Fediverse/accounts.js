import { useLocalStorage } from '@vueuse/core';
import { oauthReturnUrl, scopes } from './common';
import MastodonApi from './MastodonApi';
import { getServer } from './servers';

export const accounts = useLocalStorage('auth/accounts', []);

export async function getAuthorizationUrl(domain, returnUrl) {
  let { app } = await getServer(domain);

  let state = window.btoa(JSON.stringify({
    domain,
    returnUrl,
  }));

  let params = new URLSearchParams({
    response_type: 'code',
    client_id: app.client_id,
    redirect_uri: oauthReturnUrl,
    scope: scopes,
    state,
  });

  return `https://${domain}/oauth/authorize?${params.toString()}`;
}

export async function finishLogin(code, state) {
  state = JSON.parse(window.atob(state));
  let domain = state.domain;
  let tokens = await getTokens(domain, code);
  let api = new MastodonApi(domain, tokens.access_token);
  let data = await api.get('api/v1/accounts/verify_credentials');

  let account = { domain, data, tokens };
  accounts.value.push(account);

  return { ...state, account };
}

async function getTokens(domain, code) {
  let { app } = await getServer(domain);

  let client = new MastodonApi(domain);

  try {
    return await client.post('oauth/token', {
      grant_type: 'authorization_code',
      code,
      client_id: app.client_id,
      client_secret: app.client_secret,
      redirect_uri: oauthReturnUrl,
    });
  } catch (e) {
    console.error(e.data);

    throw new Error('Error retrieving tokens');
  }
}

export async function logout(account) {
  let client = new MastodonApi(account.domain, account.tokens.access_token);

  try {
    await client.post('oauth/revoke');
  } catch (e) {
    //
  }

  let index = accounts.value.indexOf(account);
  accounts.value.splice(index, 1);
}
