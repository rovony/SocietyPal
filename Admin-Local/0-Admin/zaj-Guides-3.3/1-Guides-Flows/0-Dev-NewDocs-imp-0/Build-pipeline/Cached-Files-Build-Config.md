## ARTICLE: 1


// TAKE HOME MESSAGE:  For example with github repos, or marketplace purchased scripts where you are not sure what did the developer change in packages, avoid caching files.


# Understanding Cached Files in Build Pipelines for Laravel Applications

## - What are Cached Files?

**Cached files** in build pipelines are dependencies and artifacts that are stored between deployments to avoid re-downloading or regenerating the same resources repeatedly. 

These files persist across builds, significantly reducing deployment time and server load by reusing previously downloaded or compiled assets.[1]

## - When to Use Cached Files

### -- Primary Use Cases

1. **Dependency Management**: Cache directories like `vendor/` (Composer) and `node_modules/` (npm) that contain packages downloaded from external repositories.[2][1]

2. **Lock File Dependencies**: When you have stable lock files (`composer.lock`, `package-lock.json`, `yarn.lock`) that haven't changed between deployments.[3][2]

3. **Large Dependency Trees**: Projects with hundreds or thousands of dependencies benefit most from caching, as downloading all packages can take several minutes.[3]

4. **Frequent Deployments**: Applications that deploy multiple times per day see the greatest time savings from caching.[4]

## Benefits of Using Cached Files

### Performance Improvements

- **Faster Build Times**: Caching can reduce build times from 5-7 minutes to under 90 seconds in complex applications.[1][4]

- **Reduced Network Traffic**: Avoids re-downloading the same packages repeatedly, saving bandwidth and reducing external API calls.[1]

- **Lower Infrastructure Costs**: Fewer compute resources needed during builds, reducing CI/CD costs.[5]

- **Improved Developer Experience**: Faster feedback loops and reduced waiting time for deployments.[3]

## Potential Drawbacks and Cons

### Cache-Related Issues

1. **Stale Dependencies**: Cached files may become outdated if cache invalidation isn't properly configured, leading to deployment issues.[6][5]

2. **Hidden Build Problems**: Cache can mask dependency resolution issues that would otherwise surface during fresh installations.[5]

3. **Complexity**: Adds another layer of complexity to the deployment process that can introduce hard-to-debug issues.[6]

4. **Storage Overhead**: Requires additional infrastructure for cache storage and management.[6]

## When NOT to Use Cached Files

### -- Avoid Caching When:

1. **Inconsistent Environments**: When different build agents or environments might have different cached states.[5]

2. **Frequent Lock File Changes**: If `composer.lock` or `package-lock.json` change frequently, caching provides minimal benefit.

3. **Security-Critical Applications**: Where you need to ensure fresh dependencies are always fetched to get latest security updates.

4. **Small Projects**: Applications with minimal dependencies where download time is already negligible.
5. **Uncertain Lock File Chages**: For example with github repos, or marketplace purchased scripts where you are not sure what did the developer change in packages, avoid caching files.

## Universal Cache Paths for Laravel Applications

Based on industry best practices and the glob pattern system you're using, here are the recommended universal cache paths for ANY Laravel application:

### For Laravel with PHP Only
```
vendor/**
composer.lock
storage/framework/cache/**
storage/framework/sessions/**
storage/framework/views/**
bootstrap/cache/**
.env.example
```

### For Laravel with JavaScript Frontend
```
vendor/**
node_modules/**
composer.lock
package-lock.json
yarn.lock
storage/framework/cache/**
storage/framework/sessions/**
storage/framework/views/**
bootstrap/cache/**
public/js/**
public/css/**
public/mix-manifest.json
.env.example
```

### Advanced Universal Set (All-in-One)
```
# PHP Dependencies
vendor/**
composer.lock

# JavaScript Dependencies  
node_modules/**
package-lock.json
yarn.lock

# Laravel Framework Cache
storage/framework/cache/**
storage/framework/sessions/**
storage/framework/views/**
bootstrap/cache/**

# Compiled Assets
public/js/**
public/css/**
public/mix-manifest.json
public/build/**
public/hot

# Configuration
.env.example

# Additional Build Artifacts
.phpunit.result.cache
_ide_helper.php
.phpstorm.meta.php
```

## Example Implementation

Here's how to configure these cache patterns in DeployHQ's build pipeline:

### Build Commands Example:
```bash
# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install and build frontend assets (if applicable)
npm ci
npm run production

# Clear and cache Laravel configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Cache Configuration:
Set up cache rules for the paths listed above using DeployHQ's glob pattern system. The cache will be invalidated when lock files change, ensuring fresh dependencies when needed while maintaining speed benefits for unchanged dependencies.[2][1]

This approach provides optimal performance for both simple Laravel APIs and complex full-stack applications with frontend assets, while maintaining reliability and avoiding common caching pitfalls.

// TAKE HOME MESSAGE:  For example with github repos, or marketplace purchased scripts where you are not sure what did the developer change in packages, avoid caching files.


---
## ARTICLE 2:

## Filename Pattern Cheatsheet (DeployHQ / Glob Patterns)


## TL;DR

Use glob patterns (`*`, `**`, `?`, `[set]`) to control which files are **excluded**, **included**, or **cached** during DeployHQ deployments.

* `*` = any file
* `**` = recursive directories
* `?` = single character
* `[set]` = character ranges
* `!` = force include
  Add **cache rules** for `vendor/`, `node_modules/`, and lock files to speed up builds.

---


### Wildcards

| Pattern | Meaning                                                  | Example                                                             |
| ------- | -------------------------------------------------------- | ------------------------------------------------------------------- |
| `*`     | Matches **any file** (single level)                      | `*.log` → matches `error.log`, `debug.log` but not `logs/error.log` |
| `**`    | Matches **directories recursively** or files expansively | `logs/**` → matches `logs/error.log`, `logs/app/debug.log`          |
| `?`     | Matches **any one character**                            | `file?.txt` → matches `file1.txt`, `fileA.txt` but not `file12.txt` |
| `[set]` | Matches any **one character in set**                     | `file[0-9].txt` → matches `file1.txt`, `file2.txt`                  |

---

### Excluding / Including Rules

| Pattern    | Meaning                                              | Example                                                       |
| ---------- | ---------------------------------------------------- | ------------------------------------------------------------- |
| `!pattern` | Always **include**, even if excluded by another rule | `!important.env` → ensures `important.env` is always uploaded |

---

### Common Examples

| Pattern            | Behavior                                                             | Example Use                                             |
| ------------------ | -------------------------------------------------------------------- | ------------------------------------------------------- |
| `vendor`           | Excludes the **vendor directory itself**, but uploads subdirectories | Keeps `vendor/package1/` files but not `vendor/` itself |
| `vendor/**`        | Excludes **vendor and all its subdirectories**                       | Good when dependencies should not be deployed           |
| `**/*.yml`         | Excludes all `.yml` files **outside root**                           | Blocks `config/db.yml` but not `/db.yml`                |
| `*.md`             | Excludes all Markdown files in **root directory only**               | Blocks `README.md`, keeps `docs/README.md`              |
| `config/*.php`     | Excludes all `.php` files **inside config/**                         | Blocks `config/app.php`, keeps `src/app.php`            |
| `!**/database.yml` | Always include `database.yml`                                        | Useful to ensure DB config is kept                      |

---

## Cached Files (Build Optimization)

> Use caching for dependencies or build artifacts so they don’t have to be re-downloaded every time.

Examples:

* **Node.js (npm / yarn):**

  ```yml
  node_modules/**
  package-lock.json
  yarn.lock
  ```
* **PHP (Composer):**

  ```yml
  vendor/**
  composer.lock
  ```
* **Ruby (Bundler):**

  ```yml
  vendor/bundle/**
  Gemfile.lock
  ```
* **Python (pip):**

  ```yml
  venv/**
  requirements.txt
  ```

---

## Practical Workflow Example (Laravel App)

* **Exclusions** (don’t deploy build junk or sensitive files):

  ```yml
  node_modules/**
  vendor/**
  .env
  tests/**
  *.log
  ```

* **Inclusions** (force-keep important configs):

  ```yml
  !config/database.php
  !public/.htaccess
  ```

* **Cache** (speed up builds):

  ```yml
  vendor/**
  node_modules/**
  composer.lock
  package-lock.json
  ```

---

Do you want me to **package this into a final SOP template** (with sections for *Exclusions*, *Inclusions*, *Cache*) that you can reuse for all your DeployHQ + Laravel/Node projects?
