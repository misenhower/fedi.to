const website = import.meta.env.VITE_APP_URL;

export const oauthReturnUrl = `${website}/oauth/mastodon/return`;

export const scopes = 'read write:follows write:lists';
