# Dry-Run GitHub Action for Build & Pre-Deployment Checks

By defining a single workflow that can “turn on” or “turn off” build, SSH, and real–deploy steps via inputs, you can simulate every phase without ever touching production. You’ll also pin PHP, Composer, and Node versions to catch mismatches ahead of time.

```yaml
# .github/workflows/pre-deploy-dry-run.yml
name: “Pre-Deployment Dry Run”

on:
  workflow_dispatch:
    inputs:
      php-version:
        description: “PHP version to test”
        required: true
        default: “8.1”
      node-version:
        description: “Node.js version to test”
        required: true
        default: “16”
      run-build:
        description: “Run build steps?”
        required: true
        default: “true”
      run-ssh:
        description: “Run SSH-only steps?”
        required: true
        default: “false”

jobs:
  dry-run:
    runs-on: ubuntu-latest
    env:
      COMPOSER_NO_INTERACTION: “1”
      DEPLOY_HOST: “example.com”
      DEPLOY_USER: “deployer”

    steps:
    - name: Check out code
      uses: actions/checkout@v4

    - name: Set up PHP ${{ inputs.php-version }}
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ inputs.php-version }}
        tools: composer

    - name: Validate composer.json & lock
      run: |
        composer validate --strict
        composer install --dry-run --no-interaction --no-dev

    - name: Set up Node.js ${{ inputs.node-version }}
      uses: actions/setup-node@v3
      with:
        node-version: ${{ inputs.node-version }}

    - name: Validate package.json & lock
      run: |
        npm ci --dry-run
      if: ${{ inputs.run-build == 'true' }}

    - name: Run Build Commands
      run: |
        composer install --no-dev --optimize-autoloader
        npm ci
        npm run production
      if: ${{ inputs.run-build == 'true' }}

    - name: Simulate SSH Connectivity
      run: |
        ssh -o BatchMode=yes -p 22 $DEPLOY_USER@$DEPLOY_HOST "echo 'SSH OK on $DEPLOY_HOST'"
      if: ${{ inputs.run-ssh == 'true' }}

    - name: Dry-Run Remote Build Checks
      run: |
        ssh $DEPLOY_USER@$DEPLOY_HOST << 'EOF'
          php -r "printf('PHP %s\n', PHP_VERSION);"
          composer install --dry-run --no-interaction --no-dev
          npm ci --dry-run
        EOF
      if: ${{ inputs.run-ssh == 'true' }}

    - name: Report summary
      run: |
        echo "✓ Dry-run complete. Build: ${{ inputs.run-build }}, SSH checks: ${{ inputs.run-ssh }}"
```

**How this meets your needs:**
- **Version pinning** via `inputs.php-version` and `inputs.node-version`  
- **Build-only dry run** when `run-build: true` (skips SSH)  
- **SSH-only dry run** when `run-ssh: true` (skips build)  
- **Combined full-pipeline dry run** by setting both flags to **true**  
- Uses `--dry-run` flags in Composer/NPM to catch missing extensions or dev-only dependencies  
- Does **not** actually deploy anything—only tests the commands and environment  

## Local Simulation with `act`
To test this without pushing commits every time, install [act](https://github.com/nektos/act) and run:
```bash
act workflow_dispatch \
  -W .github/workflows/pre-deploy-dry-run.yml \
  -j dry-run \
  --input run-build=true \
  --input run-ssh=false
```
You’ll get immediate feedback on YAML syntax and step failures locally, just like GitHub Actions would run.

[1] https://www.youtube.com/watch?v=YORvmxQBPeM
[2] https://www.twilio.com/en-us/blog/developers/community/build-test-deploy-laravel-application-github-actions
[3] https://codefresh.io/learn/github-actions/deployment-with-github-actions/
[4] https://sentry.io/answers/how-do-i-connect-to-a-database-using-laravel-in-a-github-action/
[5] https://earthly.dev/blog/using-github-actions-locally/
[6] https://codecourse.com/articles/running-laravel-dusk-on-github-actions
[7] https://www.reddit.com/r/Terraform/comments/r8a9tt/how_to_make_a_github_actions_workflow_dry/
[8] https://laravel-news.com/laravel-ci-with-github-action
[9] https://stackoverflow.com/questions/68019866/how-to-locally-test-and-simulate-scenarios-for-github-actions-development
[10] https://laraveldaily.com/lesson/testing-advanced/auto-launch-tests-with-github-actions-ci-cd
[11] https://redberry.international/creating-ci-cd-pipeline-for-laravel-project/
