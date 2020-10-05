# Detoxify
Detoxify is a Laravel based web app to provide Spotify users a workaround for broken _Your Release Radar_ playlists. This application uses Spotify's Web API to do the following things:

- Identify and track the artists a user follows.
- Creates a public playlist in the user's library with a custom cover image and title.
- Checks the last 3 singles and last 3 albums from each artist and adds those released within the last month to the new playlist.

### Dev Notes
To eliminate the need for a DB to track users, information is tracked through cookies and local storage. When a user fetches a list of the artists they followed, the returned JSON response is stored in browser storage. When a user generates a playlist, album and track information JSON are also stored in browser storage. This allows for a user to refresh the page and not have to do a new call for information, having information load from local storage instead.

##### .ENV variables

**SPOTIFY_APP_CLIENT_ID** = The Client ID provided by Spotify in your developer dashboard.
**SPOTIFY_APP_CLIENT_SECRET**= The Client Secret provided by Spotify in your developer dashboard.
**SPOTIFY_ACCESS_STATE**= A variable serving as an additional level of security passed as a GET param in your app authorization request.
**AUTHORIZE_ACCESS_ENDPOINT**= The callback path set in your developer dashboard. Set as a .ENV variable to help setting port if developing locally.
**CAPTCHA_SITE_KEY**= The SITE KEY provided in your Google reCAPTCHA dashboard.
**CAPTCHA_SECRET_KEY**= The SECRET KEY provided in your Google reCAPTCHA dashboard.
