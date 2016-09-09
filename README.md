github-api-test
===============

1. Add a new personal access token for your GitHub user
 You can do this at https://github.com/settings/tokens
 The "repo" scope is needed for listing all repositories accessible by your user. This includes public and private repositories that you can access through any organization membership as well. 

2. Place your secret token to `app/config/parameters.yml`

3. List all repositories accessible by your user

`
bin/console repo:list
`
