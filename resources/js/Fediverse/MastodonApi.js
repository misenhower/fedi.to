export default class MastodonApi
{
  constructor(domain, token = null) {
    this._domain = domain;
    this._token = token;
  }

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

  get(path) {
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
