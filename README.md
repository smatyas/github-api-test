github-api-test
===============

1. Add a new personal access token for your GitHub user
 You can do this at https://github.com/settings/tokens
 The "repo" scope is needed for:
    - listing all repositories accessible by your user. 
        _This includes public and private repositories that you can access through any organization membership as well._
    - listing all pull requests accessible by your user. 
        _This includes public and private repositories that you can access through any organization membership as well._
    - listing (non-review) comments on issues / pull requests accessible by your user. 
        _This includes public and private repositories that you can access through any organization membership as well._
    - adding (non-review) comments on issues / pull requests accessible by your user. 
        _This includes public and private repositories that you can access through any organization membership as well._
    - listing all deploy keys accessible by your user. 
        _The user has to be the owner/admin of the repo._
    - adding deploy keys for the repositories the user can access. 
        _The user has to be the owner/admin of the repo._
 

2. Place your secret token to `app/config/parameters.yml`

3. Create a test repository that you'll manipulate using the commands

4. List all repositories accessible by your user
    ```
    bin/console github:repo:list
    ```

5. List deploy keys for a repository
    ```
    bin/console github:repo:list-deploy-keys octocat/Hello-World
    ```

6. Add a new deploy key for a repository
    To create a deploy key, run something like `ssh-keygen -f ./test-key`
    Then add it to the repository as a deploy key:
    ```
    bin/console github:repo:add-deploy-key octocat/Hello-World test-key "`cat ./test-key.pub`"
    ```
    **Note:** you cannot use the same deploy key for multiple repositories.

5. List pull requests for a repository
    ```
    bin/console github:pr:list octocat/Hello-World
    ```

6. List (non-review) comments of an issue / pull request
    ```
    bin/console github:pr:comments octocat/Hello-World 1
    ```

7. Add a new (non-review) comment to an issue / pull request
    ```
    bin/console github:pr:comment-add octocat/Hello-World 1 "New comment."
    ```
