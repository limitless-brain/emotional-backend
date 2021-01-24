# Emotional APIs Specification

## API endpoints
| **Route** | **Method** | **Params** | **Middleware**
| --------- | ---------- | ---------- | --------------
| /login | post | email(string), password(string), remember_me(bool) | 
| /signup | post | name(string), email(string), password(string), password_confirmation(string) |
| /user | get | | auth
| /user | put | name(string), password(string, optional) | auth
| /user | delete | id(int) | auth
| /logout | get | | auth
|/search | get | query(string), pageToken(string, optional) | auth
|/featured | get | pageToken(string, optional) | auth
|/videos/{id}/audio | get | id(string) youtubeId | auth
|/videos/{id}/info | get | id(string) youtubeId | auth
|/videos/find | get | artist(string), title(string) | auth
|/lyrics | get | youtubeId(string) | auth
| /spotify/albums | get | | auth
| /spotify/artists | get | | auth
| /songs | get | | auth
| /songs/{song} | get | | auth
| /songs/{song}/lyrics | get | | auth
| /songs/{song}/played | post | | auth
| /songs/{song}/like | post | | auth
| /songs/{song}/unlike | post | | auth
| /songs/{song}/match | post | | auth
| /songs/{song}/un-match | post | | auth
| /songs/{song}/interactions | get | | auth
| /albums | get | | auth
| /albums/{album} | get | | auth
| /artists | get | | auth
| /artists/{artist} | get | | auth
| /playlists | get | | auth
| /playlists/{playlist} | post | name(string), emotion(string) | auth
| /playlists/{playlist} | put | name(string), emotion(string) | auth
| /playlists/{playlist} | delete | | auth
| /playlists/{playlist}/{song} | get | | auth
| /playlists/{playlist}/own | get | | auth
| /playlist/{playlist}/{song} | delete | | auth
| /playlists/{emotion} | get | emotion(string) | auth
| /ai/emotion | get | paragraphs(string) | auth
