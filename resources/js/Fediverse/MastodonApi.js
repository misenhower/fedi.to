export default class MastodonApi
{
  constructor(domain, token = null) {
    this._domain = domain;
    this._token = token;
  }

  lookup(handle) {
    return this.get('api/v1/accounts/lookup', { acct: handle });;
  }

  relationships(ids) {
    let params = new URLSearchParams;
    ids.forEach(id => params.append('id[]', id));

    return this.get(`api/v1/accounts/relationships?${params}`);
  }

  async isFollowing(handle) {
    let remoteAccount = await this.lookup(handle);
    let [relationship] = await this.relationships([remoteAccount.id]);

    return relationship?.following;
  }

  async follow(handle) {
    let remoteAccount = await this.lookup(handle);

    await this.post(`api/v1/accounts/${remoteAccount.id}/follow`);
  }

  // Low-level API client methods

  get headers() {
    let headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
    };

    if (this._token) {
      headers['Authorization'] = `Bearer ${this._token}`;
    }

    return headers;
  }

  url(path) {
    return `https://${this._domain}/${path}`;
  }

  get(path, query = null) {
    if (query) {
      query = new URLSearchParams(query);
      path += `?${query}`
    }

    return this.request('get', path);
  }

  async post(path, data = null) {
    return this.request('post', path, {
      body: JSON.stringify(data),
    })
  }

  async request(method, path, options = null) {
    let response = await fetch(this.url(path), {
      method,
      headers: this.headers,
      ...options
    });

    let data = await response.json();

    if (!response.ok) {
      let error = new Error('Invalid response');
      error.data = data;

      throw error;
    }

    return data;
  }
}
