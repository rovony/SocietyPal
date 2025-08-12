---

 Here’s the clean, final SOP with your refinements baked in:

- dev branch may also be named development (both handled)
- DeployHQ has a separated Build Pipeline and a Commands Pipeline (with maintenance, backup, zero-downtime, etc.)
- Staging environment included for all scenarios
- Git branches and mapping included for B, C, D
- Clear tools per scenario + step-by-step + ready templates

If anything’s unclear, tell me where and I’ll tighten it more.

# Laravel Deployment SOP (Scenarios A–D)

## 1) Purpose

A practical, step-by-step playbook to deploy Laravel using four approaches. Covers tools, branching, staging, server layout, safety, verification, rollback, and ready-to-use templates.

## 2) Environments & Branching (B, C, D)

- Environments
    - production → prod.mysite.com
    - staging (optional) → staging.mysite.com (same or separate server)
- Branches
    - main = canonical default (protected)
    - production = deploys to production
    - staging = deploys to staging
    - dev or development = developer integration branch (merges → staging)
- Rules
    - PRs: dev/development → staging → production (or main → production if you prefer)
    - Tag production releases (vX.Y.Z) for audit/rollback
    - Protect main and production with required reviews

## 3) Server Layout & Safety (all scenarios)- may need to adapt for lock domain root like hostinger (domain/public_html) by deleting the public_html folder and symlink it as file.

- Release pattern (preferred)
    - /var/www/releases/ → symlink /var/www/current
    - Shared: /var/www/shared/.env, /var/www/shared/storage, /var/www/shared/uploads
- Zero-downtime idea
    - Prepare new release fully → atomically update symlink → reload queues
- Core reminders
    - Never overwrite shared dirs
    - Back up DB and uploads before production deploys
    - Run php artisan down for risky deploys; up after finalize

## 4) Scenario Quick Compare

| Scenario | Build Location | Deploy Method | Tools | Automation | Zero-Downtime | Staging | Best For |
| --- | --- | --- | --- | --- | --- | --- | --- |
| A | Local (your machine) | SSH/SCP + manual steps | Terminal, Composer, NPM, SSH | Low | Optional (manual) | Yes | Small/simple, full control |
| B | GitHub CI | CI pushes to server | GitHub Actions, SSH/rsync | Med–High | Yes (scripted) | Yes | Teams on GitHub CI/CD |
| C | DeployHQ | Managed pipeline | DeployHQ, SSH/SFTP | High | Yes (native) | Yes | Pro/enterprise |
| D | Local (your machine)

—-
Server pull + manual built dirs | git pull code + upload build artifact | Git on server/Hostinger Git, SFTP | Low–Med | Yes  | Yes | Minimal CI, familiar flow |

## 5) Scenarios (full details)

### A) Local Build + SSH Deploy (manual)

Tools

- Local: Composer, Node/NPM, Terminal
- Transport: SSH/SCP
- Server: tar/zip, PHP, (optional) Composer/Node if needed

Steps

1. Local build
    
    composer install --no-dev --optimize-autoloader
    
    npm ci && npm run build
    
    php artisan config:cache route:cache view:cache
    
2. Package all app code + build folders  (include vendor/ and public/build/) and (and any other build artifacts) into .tar.gz
3. scp package to server → extract into /var/www/releases/
4. Link shared: .env, storage, uploads
5. Switch symlink to new release
6. php artisan migrate --force ; php artisan queue:restart
7. Optional: php artisan up

Staging

- Repeat same flow to staging.mysite.com path or server

Pros: simple, full control

Cons: repetitive, easy to miss steps

Minimal scripts

- package_local.sh (run on your machine)

```
#!/usr/bin/env bash
set -e
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan config:cache route:cache view:cache
REL=$(date +%Y%m%d-%H%M%S)-$(git rev-parse --short HEAD 2>/dev/null || echo local)
tar -czf release-$REL.tar.gz \
  --exclude=.git --exclude=node_modules .
echo "Created release-$REL.tar.gz"

```

- deploy_on_server.sh (run via SSH on server)

```
#!/usr/bin/env bash
set -e
REL="$1"
BASE=/var/www
mkdir -p $BASE/releases/$REL
tar -xzf release-$REL.tar.gz -C $BASE/releases/$REL
ln -nfs $BASE/shared/.env $BASE/releases/$REL/.env
rm -rf $BASE/releases/$REL/storage
ln -nfs $BASE/shared/storage $BASE/releases/$REL/storage
ln -nfs $BASE/releases/$REL $BASE/current
cd $BASE/current
php artisan migrate --force
php artisan queue:restart
echo "Deployed $REL"

```

---

### B) GitHub Actions Build + Deploy (CI/CD)

Tools

- GitHub repo + Actions
- SSH/rsync (or SFTP) from CI to server

Branch mapping

- staging branch → staging.mysite.com
- production branch (or tags) → production

Steps

1. Push to staging or production
2. Workflow builds (composer, npm)
3. CI uploads artifact (rsync/scp) into /var/www/releases/
4. Server post-deploy: link shared, switch symlink, migrate, queue:restart

Staging

- Separate job or environment; different server/paths

Pros: automated, consistent, auditable

Cons: initial setup, CI secrets management

Sample GitHub Actions (single workflow; deploy per branch)

```
name: build-and-deploy
on:
  push:
    branches: [staging, production, main, development]
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with: { php-version: '8.3' }
      - name: Composer
        run: composer install --no-dev --optimize-autoloader
      - name: Node
        run: |
          npm ci
          npm run build
      - name: Package
        run: |
          REL=${GITHUB_SHA::7}
          tar -czf release-$REL.tar.gz --exclude=.git --exclude=node_modules .
          echo "REL=$REL" >> $GITHUB_ENV
      - uses: actions/upload-artifact@v4
        with: { name: release-${{ env.REL }}, path: release-${{ env.REL }}.tar.gz }

  deploy:
    needs: build
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/staging' || github.ref == 'refs/heads/production'
    steps:
      - uses: actions/download-artifact@v4
        with: { name: release-${{ needs.build.outputs.REL || env.REL }}, path: . }
      - name: Set REL
        run: echo "REL=${GITHUB_SHA::7}" >> $GITHUB_ENV
      - name: Ship to server
        uses: burnett01/rsync-deployments@6.0.0
        with:
          switches: -avz
          path: release-${{ env.REL }}.tar.gz
          remote_path: /var/www/releases/
          remote_host: ${{ (github.ref == 'refs/heads/staging' && secrets.STG_HOST) || secrets.PROD_HOST }}
          remote_user: ${{ (github.ref == 'refs/heads/staging' && secrets.STG_USER) || secrets.PROD_USER }}
          remote_key:  ${{ (github.ref == 'refs/heads/staging' && secrets.STG_SSH_KEY) || secrets.PROD_SSH_KEY }}
      - name: Post-deploy (link/symlink/migrate)
        uses: appleboy/ssh-action@v1.0.3
        with:
          host: ${{ (github.ref == 'refs/heads/staging' && secrets.STG_HOST) || secrets.PROD_HOST }}
          username: ${{ (github.ref == 'refs/heads/staging' && secrets.STG_USER) || secrets.PROD_USER }}
          key: ${{ (github.ref == 'refs/heads/staging' && secrets.STG_SSH_KEY) || secrets.PROD_SSH_KEY }}
          script: |
            set -e
            BASE=/var/www
            REL=${REL}
            mkdir -p $BASE/releases/$REL
            mv $BASE/releases/release-$REL.tar.gz $BASE/releases/$REL.tar.gz
            tar -xzf $BASE/releases/$REL.tar.gz -C $BASE/releases/$REL
            ln -nfs $BASE/shared/.env $BASE/releases/$REL/.env
            rm -rf $BASE/releases/$REL/storage
            ln -nfs $BASE/shared/storage $BASE/releases/$REL/storage
            ln -nfs $BASE/releases/$REL $BASE/current
            cd $BASE/current
            php artisan migrate --force
            php artisan queue:restart

```

Notes

- If you prefer tags for prod, change on: push to on: push.tags + condition on refs/tags
- Use Actions Environments for secrets + manual approvals

---

### C) DeployHQ Pipeline + Deploy (managed zero-downtime)

Concept

- DeployHQ pulls from your Git provider, runs a Build Pipeline, then runs a Commands Pipeline against your server.
- **DeployHQ**: Professional build on their infrastructure - with ability to define build pipeline
- Zero-downtime uses release folders + atomic symlink swap.
- **Auto Deploy**: Zero-downtime deployment with advanced features with ability to add SSH commands
    1. Before Changes: Executed immediately once connected to the server.
    2. **Before Release Link: Executed after files have been uploaded/removed, but before the new release has been made active (zero-downtime only).**
    3. **After Changes:** Executed at the end of a deployment after files have been uploaded/removed.

Tools

- DeployHQ account
- GitHub/GitLab/Bitbucket repo
- SSH/SFTP to servers
- Optional: Slack/Email notifications

Branch mapping

- staging → staging target
- production → production target
- dev/development → optional preview target

Build Pipeline (runs in DeployHQ’s build environment)

- Composer install (no dev) and NPM build
- Package artifact for transfer to target
    
    Example build commands
    

```
composer install --no-dev --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
tar -czf build.tar.gz --exclude=.git --exclude=node_modules .

```

Commands Pipeline (runs on your server; zero-downtime + safety)

Pre-deploy (optional maintenance + backups)

```
# put app into maintenance (optional; zero-downtime swap often makes this unnecessary)
php /var/www/current/artisan down || true

# backups (DB + uploads)
mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > /var/backups/db-$(date +%F-%H%M%S).sql
tar -czf /var/backups/uploads-$(date +%F-%H%M%S).tar.gz /var/www/shared/uploads

```

Deploy (zero-downtime)

```
BASE=/var/www
REL=$(date +%Y%m%d-%H%M%S)-${DEPLOYHQ_RELEASE:0:7}
mkdir -p $BASE/releases/$REL
tar -xzf build.tar.gz -C $BASE/releases/$REL
ln -nfs $BASE/shared/.env $BASE/releases/$REL/.env
rm -rf $BASE/releases/$REL/storage
ln -nfs $BASE/shared/storage $BASE/releases/$REL/storage
ln -nfs $BASE/releases/$REL $BASE/current

```

Post-deploy (migrate, queues, up)

```
php /var/www/current/artisan migrate --force
php /var/www/current/artisan queue:restart
php /var/www/current/artisan up || true

```

Staging

- Create a second DeployHQ “Server/Target” bound to staging branch
- Same pipelines; separate env vars/paths

Pros: fastest path to reliable zero-downtime, GUI, rollbacks

Cons: paid, needs initial config

---

### D) Git Pull on Server + Manual Upload of Built Folders

Your correction reflected here.

What it is

- Server (or Hostinger’s built-in Git) pulls code from GitHub.
- You manually upload production build outputs (vendor/ and public/build/), when you choose to build locally.
- Optionally, you can build on the server if Node/Composer are available.

Tools

- GitHub repo, server Git access (or Hostinger Git feature)
- SFTP/FTP for manual upload of vendor/ and public/build/
- Optional: simple releases+symlink layout if your host allows

Branch mapping

- staging branch → staging.mysite.com
- production branch → production
- dev/development → optional preview or merged to staging

Steps

1. Developer pushes to staging or production
2. On server, pull code
    - SSH servers: cd /var/www/current (or repo path) ; git fetch --all ; git checkout ; git pull
    - Hostinger Git: configure repo URL/branch; click Deploy
3. Option A (build locally): upload vendor/ and public/build/ via SFTP; overwrite the server copies
4. Option B (build on server): run composer install --no-dev and npm ci && npm run build (if supported)
5. Link shared, clear caches, run migrate, queue:restart
6. Optional zero-downtime: if host allows, use /releases + symlink swap

Staging

- Same process targeting staging path/server

Pros: minimal CI, familiar flow, works well with Hostinger/cPanel Git

Cons: manual steps, risk of drift (ensure you always update vendor/build together)

Helper: local sync of built dirs

```
# run locally after building, to stage environment
rsync -avz vendor/ user@staging-host:/path/to/app/vendor/
rsync -avz public/build/ user@staging-host:/path/to/app/public/build/

```

## 6) Staging Playbook (all scenarios)

- URL: staging.mysite.com
- DB: separate DB/schema from production
- Env: separate .env on staging server (never share prod secrets)
- Data: anonymize prod data before import to staging if needed
- Deploy triggers
    - A: run same manual flow to staging target
    - B/C/D: map staging branch to staging target
- Approvals
    - PR dev/development → staging, then staging → production (tags for prod)

## 7) Verification Checklist (every deploy)

- 200 OK on target URL; no 500s in logs
- storage writable; logs updating
- php artisan config:cache route:cache view:cache done
- DB migrations applied; idempotent if rerun
- queue workers restarted; scheduled jobs ok
- critical flows pass (login, CRUD, upload, email)
- error tracker clean (Sentry/Bugsnag)

## 8) Rollback

- A: keep previous release-*.tar.gz and /releases/ → re-link symlink → migrate:rollback if needed
- B: redeploy previous tag or previous build artifact from CI
- C: use DeployHQ rollback to prior release (one-click)
- D: git checkout previous tag/commit (or Hostinger revert) + restore matching vendor/ and public/build/ backup; clear caches

## 9) Backups (production)

- DB: nightly full + pre-deploy snapshot (mysqldump)
- Uploads: nightly tar.gz + pre-deploy snapshot
- Keep at least 7 daily, 4 weekly
- Store off-box (object storage) when possible

## 10) Security

- Never commit .env, uploads, backups
- Use least-privileged deploy keys; no full-access tokens on CI
- Restrict SSH by IP and key; disable password auth
- Deny access to non-public paths (public/.htaccess for shared hosting)
- Rotate keys/secrets on role changes

## 11) Quick Chooser

- Want no extra tools, OK with manual → A
- Already on GitHub; want automation → B
- Want GUI + hardened zero-downtime → C
- Use server/Hostinger Git and manually upload built dirs → D

---

Want me to generate:

- a DeployHQ project checklist (fields, variables, targets)
- a Hostinger Git step-by-step with screenshots placeholders
- and a 1-page “choose & run” cheatsheet?

Also, do you want a reusable prompt template so you can regenerate this SOP per project (domains/paths/branches as variables)?

---

## **Scenario D Deep Dive: Local Build + Artifact Storage Deploy**

This is indeed a **real and practiced deployment method**, particularly useful when developers want to maintain control over the build process while enabling flexible, repeatable deployments across multiple environments.youtube[reddit+1](https://www.reddit.com/r/laravel/comments/zsnk0h/what_are_your_deployment_steps_for_continuous/)

## **Key Characteristics**

**Local Control, Remote Distribution**:[jmh+1](https://jmh.me/blog/build-deploy-php-application-with-github-actions)

- Developer controls build environment and dependencies
- Ensures consistent local testing before artifact creation
- Separates build concerns from deployment infrastructure
- Enables offline development and building

**Artifact Versioning and Distribution**:[jfrog+2](https://jfrog.com/whitepaper/php-composer-9-benefits-of-using-a-binary-repository-manager/)

- Each build creates a versioned, immutable artifact
- Artifacts stored in centralized repository for team access
- Same artifact can be deployed to multiple environments
- Easy rollback to previous artifact versions

## **Implementation Example**

**Local Build Script**:[reddit+1](https://www.reddit.com/r/PHP/comments/51rsue/how_do_you_deploy_your_php_applications/)

`bash#!/bin/bash
*# Local build script*
echo "Building Laravel application locally..."

*# Install dependencies*
composer install --no-dev --optimize-autoloader

*# Build frontend assets*
npm ci && npm run build

*# Optimize Laravel*
php artisan config:cache
php artisan route:cache
php artisan view:cache

*# Create versioned artifact*
VERSION=$(git rev-parse --short HEAD)
tar -czf "app-${VERSION}.tar.gz" \
    --exclude=node_modules \
    --exclude=.git \
    --exclude=storage \
    --exclude=.env \
    .

echo "Artifact created: app-${VERSION}.tar.gz"`

**Artifact Upload Script**:[jfrog+1](https://jfrog.com/whitepaper/php-composer-9-benefits-of-using-a-binary-repository-manager/)

`bash#!/bin/bash
*# Upload to artifact repository*
VERSION=$(git rev-parse --short HEAD)

*# Upload to AWS S3*
aws s3 cp "app-${VERSION}.tar.gz" "s3://my-artifacts/laravel-app/"

*# Or upload to GitHub Packages*
gh release upload v${VERSION} "app-${VERSION}.tar.gz"

*# Or upload to local artifact repository*
scp "app-${VERSION}.tar.gz" artifacts.company.com:/var/artifacts/laravel-app/`

## **Artifact Storage Options**

**Cloud Storage (AWS S3, Google Cloud)**:[cloud.laravel+1](https://cloud.laravel.com/docs/environments)

- Scalable artifact storage with versioning
- Integration with cloud deployment tools
- Cost-effective for long-term artifact retention

**Version Control Releases (GitHub, GitLab)**:[reddit](https://www.reddit.com/r/laravel/comments/zsnk0h/what_are_your_deployment_steps_for_continuous/)youtube

- Tight integration with source code repositories
- Built-in versioning and release management
- Easy access control and team collaboration

**Enterprise Repositories (Nexus, JFrog, Artifactory)**:[jfrog+2](https://jfrog.com/help/r/jfrog-artifactory-documentation/deploying-artifacts)

- Enterprise-grade artifact management
- Advanced metadata and dependency tracking
- Role-based access control and audit trails

**Self-Hosted Storage**:[jmh](https://jmh.me/blog/build-deploy-php-application-with-github-actions)

- Full control over artifact storage
- Custom retention policies
- Integration with existing infrastructure

## **Deployment Process**youtube[reddit+1](https://www.reddit.com/r/laravel/comments/zsnk0h/what_are_your_deployment_steps_for_continuous/)

**Deployment Script Example**:

`bash#!/bin/bash
*# Download and deploy specific artifact version*
VERSION=${1:-latest}

*# Download artifact*
wget "https://artifacts.company.com/laravel-app/app-${VERSION}.tar.gz"

*# Create new release directory*
mkdir -p "/var/www/releases/${VERSION}"

*# Extract artifact*
tar -xzf "app-${VERSION}.tar.gz" -C "/var/www/releases/${VERSION}"

*# Link shared directories*
ln -nfs "/var/www/shared/storage" "/var/www/releases/${VERSION}/storage"
ln -nfs "/var/www/shared/.env" "/var/www/releases/${VERSION}/.env"

*# Switch symlink (zero-downtime)*
ln -nfs "/var/www/releases/${VERSION}" "/var/www/current"

*# Post-deployment tasks*
cd "/var/www/current"
php artisan migrate --force
php artisan queue:restart

echo "Deployment completed: ${VERSION}"`

## **Advantages of Scenario D**

**Developer Control**:[reddit+1](https://www.reddit.com/r/PHP/comments/51rsue/how_do_you_deploy_your_php_applications/)

- Full control over build environment and dependencies
- Local testing before artifact creation
- No dependency on external CI/CD infrastructure
- Faster iteration during development

**Deployment Flexibility**:[reddit](https://www.reddit.com/r/laravel/comments/zsnk0h/what_are_your_deployment_steps_for_continuous/)youtube

- Deploy same artifact to multiple environments
- Easy rollback to previous versions
- Deployment timing independent of development
- Support for emergency hotfix deployments

**Hybrid Benefits**:[jmh](https://jmh.me/blog/build-deploy-php-application-with-github-actions)

- Combines local control with remote distribution
- Suitable for teams transitioning to full CI/CD
- Works well with existing development workflows
- Reduced complexity compared to full CI/CD pipelines

## **Common Implementation Patterns**

**Makefile Approach**:[reddit+1](https://www.reddit.com/r/PHP/comments/51rsue/how_do_you_deploy_your_php_applications/)

`makefilebuild:
	composer install --no-dev --optimize-autoloader
	npm ci && npm run build
	php artisan optimize

package: build
	tar -czf app-$(shell git rev-parse --short HEAD).tar.gz \
		--exclude=node_modules --exclude=.git .

deploy: package
	./scripts/upload-artifact.sh
	./scripts/deploy.sh $(shell git rev-parse --short HEAD)`

**GitHub Actions Artifact Storage**:youtube[jmh](https://jmh.me/blog/build-deploy-php-application-with-github-actions)

`textname: Store Local Build
on:
  workflow_dispatch:
    inputs:
      artifact_path:
        description: 'Path to local build artifact'
        required: true

jobs:
  store-artifact:
    runs-on: ubuntu-latest
    steps:
      - name: Upload Artifact
        uses: actions/upload-artifact@v4
        with:
          name: laravel-app-${{ github.sha }}
          path: ${{ inputs.artifact_path }}`

## **When to Choose Scenario D**

**Recommended For**:[jmh+1](https://jmh.me/blog/build-deploy-php-application-with-github-actions)

- Teams with strong local development practices
- Projects requiring specific build environment control
- Hybrid deployment strategies
- Companies transitioning to DevOps practices
- Emergency deployment capabilities
- Multiple environment deployments from single build

**Not Ideal For**:

- Large teams requiring standardized build processes
- Projects with complex build dependencies
- Environments requiring audit trails for build processes
- Teams fully committed to CI/CD practices

Scenario D represents a **practical middle ground** that combines the control benefits of local building with the distribution and deployment advantages of artifact-based deployments. This approach is particularly valuable for teams that want deployment flexibility without fully committing to automated CI/CD infrastructure.[reddit+2](https://www.reddit.com/r/laravel/comments/zsnk0h/what_are_your_deployment_steps_for_continuous/)youtube

1. https://www.youtube.com/watch?v=2zduPKmszmI
2. https://www.reddit.com/r/laravel/comments/zsnk0h/what_are_your_deployment_steps_for_continuous/
3. https://jmh.me/blog/build-deploy-php-application-with-github-actions
4. https://www.reddit.com/r/PHP/comments/51rsue/how_do_you_deploy_your_php_applications/
5. https://jfrog.com/whitepaper/php-composer-9-benefits-of-using-a-binary-repository-manager/
6. https://jfrog.com/help/r/jfrog-artifactory-documentation/deploying-artifacts
7. https://stackoverflow.com/questions/21334987/deploy-artifacts-in-my-local-maven-repository-to-a-remote-repository
8. https://cloud.laravel.com/docs/environments
9. https://stackoverflow.com/questions/60344936/how-to-deploy-a-laravel-project-locally
10. https://laravel.io/forum/how-to-deploy-laravel-app-sail
11. https://docs.ionos.space/blog/deploy-laravel-via-github/
12. https://dev.to/jesusantguerrero/deploying-and-hosting-full-stack-laravel-apps-1673
13. https://laravel.com/docs/12.x/filesystem
14. https://laracasts.com/discuss/channels/tips/laravel-project-deploy-using-own-server
15. https://stackoverflow.com/questions/49706224/still-get-the-path-of-my-local-machine-to-storage-logs-after-ive-deployed-my-la
16. https://www.hostinger.com/tutorials/how-to-deploy-laravel
17. https://stackoverflow.com/questions/58881911/how-to-deploy-a-laravel-project-from-local-to-server
18. https://laravel.io/forum/03-17-2016-need-a-how-to-for-setting-up-production-and-staging-deployments-on-same-server
19. https://laracasts.com/discuss/channels/laravel/deploying-laravel-project-on-server
20. https://laraveldaily.com/post/how-to-deploy-laravel-projects-to-live-server-the-ultimate-guide
21. https://laravel.com/docs/12.x/deployment
22. https://forums.docker.com/t/how-to-setup-laravel-in-docker-at-production-server/140111
23. https://masteringbackend.com/posts/deploy-laravel-to-heroku/
24. https://www.codemag.com/Article/2111071/Beginner%E2%80%99s-Guide-to-Deploying-PHP-Laravel-on-the-Google-Cloud-Platform
25. https://laracasts.com/discuss/channels/general-discussion/laravel-deployments-1
26. https://stackoverflow.com/questions/15281665/how-to-deploy-locally-already-installed-artifact-with-maven
27. https://cloud.google.com/run/docs/quickstarts/build-and-deploy/deploy-php-service
28. https://saasykit.com/blog/deploying-laravel-applications-with-coolify-a-complete-guide
29. https://www.youtube.com/watch?v=oMtPkNMKpV8
30. https://lorisleiva.com/laravel-deployment-using-gitlab-pipelines
31. https://www.koyeb.com/docs/deploy/php
32. https://blog.back4app.com/how-to-deploy-a-laravel-application/
33. https://faun.pub/configure-laravel-8-for-ci-cd-with-jenkins-and-github-part-1-58b9be304292
34. https://docs.docker.com/guides/frameworks/laravel/development-setup/
35. https://buddy.works/guides/5-ways-to-deploy-php-applications

---

`archived`

## 🔄 Deployment Strategy Types

### **Scenario A: Local Build + SSH Deploy**

**Process Flow:**

1. **Developer Action:** Build on local machine (`composer install --no-dev`, `npm run build`)
2. **Package Creation:** Create `.tar.gz` with vendor/, public/build/ included
3. **Upload:** SCP package to server
4. **Server Action:** Extract, link shared dirs, switch symlink
5. **Best For:** Simple projects, full control, learning deployments

### **Scenario B: GitHub Actions Build + Deploy**

**Process Flow:**

1. **Developer Action:** Push code to GitHub (`git push origin production`)
2. **GitHub Action:** Automatically builds on GitHub's servers
3. **Auto Deploy:** GitHub uploads built package and deploys
4. **Server Action:** Automated extraction, linking, switching
5. **Best For:** Team collaboration, automated workflows, CI/CD

### **Scenario C: DeployHQ Pipeline + Deploy**

**Process Flow:**

1. **Developer Action:** Push code to repository
2. **DeployHQ:** Professional build on their infrastructure
3. **Pro Deploy:** Zero-downtime deployment with advanced features
4. **Server Action:** Professional-grade deployment management
5. **Best For:** Professional deployments, enterprise features, zero-downtime