# Laravel Deployment Solutions Guide

Laravel deployment failures often stem from dependency conflicts, version mismatches, and autoloader corruption. **The most critical issue is Faker being treated as a dev dependency when production seeders need it, causing runtime failures despite successful build verification.** This comprehensive guide provides production-tested solutions for dedicated servers and shared hosting environments.

## Handling dev dependencies in production

The "Class Faker\Factory not found" error occurs because Laravel's DatabaseServiceProvider automatically registers Faker when available, but Faker is typically a dev-only dependency. When running `composer install --no-dev` in production, Faker disappears but seeders still reference it.

**Solution 1: Move Faker to production dependencies**
```json
{
    "require": {
        "fakerphp/faker": "^1.23"
    }
}
```

**Solution 2: Environment-conditional seeders**
```php
// DatabaseSeeder.php
public function run()
{
    if (app()->environment('production')) {
        $this->call([
            ProductionSeeder::class,
            RolesSeeder::class,
        ]);
    } else {
        $this->call([
            UserSeeder::class, // Uses factories/Faker
            DevelopmentSeeder::class,
        ]);
    }
}

// ProductionSeeder.php - No Faker dependency
public function run()
{
    DB::table('users')->insert([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => bcrypt('secure_password'),
        'created_at' => now(),
    ]);
}
```

**Solution 3: Use migrations for essential data**
```php
// In migration file
public function up()
{
    Schema::create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
    
    // Insert essential data directly
    DB::table('roles')->insert([
        ['name' => 'admin', 'created_at' => now(), 'updated_at' => now()],
        ['name' => 'user', 'created_at' => now(), 'updated_at' => now()],
    ]);
}
```

## Managing Composer version conflicts

Servers defaulting to Composer v1 cause significant performance and compatibility issues with modern Laravel applications. Composer v2 is 2-10x faster and handles dependency resolution much more effectively.

**Upgrading server from Composer v1 to v2:**
```bash
# Upgrade to Composer v2
composer self-update --2

# Verify version
composer --version

# Rollback if issues occur
composer self-update --1
```

**For Laravel Forge users:**
Delete existing scheduled jobs created with the `--1` flag, then create new jobs without version flags or use Forge's bulk upgrade recipe.

**Handling plugin compatibility issues:**
```bash
# Check for incompatible plugins
composer diagnose

# Update composer.json for v2 plugin API
{
    "config": {
        "allow-plugins": {
            "plugin-name": true
        }
    }
}

# Clear cache if issues persist
composer clear-cache
```

**Installing multiple Composer versions:**
```bash
# Install both versions side by side
curl -sS https://getcomposer.org/installer | php -- --1 --filename=composer1
curl -sS https://getcomposer.org/installer | php -- --2 --filename=composer

sudo mv composer /usr/local/bin/composer
sudo mv composer1 /usr/local/bin/composer1
sudo chmod +x /usr/local/bin/composer*
```

## Preventing build artifact corruption

Autoloader corruption and "illuminate/support missing" errors typically result from incomplete deployments, corrupted vendor folders, or version conflicts between illuminate packages.

**Nuclear option (most effective recovery):**
```bash
# Delete vendor and lock file
rm -rf vendor/
rm composer.lock

# Fresh install with production optimization
composer install --no-dev --optimize-autoloader --classmap-authoritative
```

**Standard recovery procedure:**
```bash
# Clear compiled files
php artisan clear-compiled

# Regenerate autoloader
composer dump-autoload --optimize

# Rebuild Laravel optimizations
php artisan optimize
```

**Production deployment commands sequence:**
```bash
# 1. Install dependencies with optimization
composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --classmap-authoritative

# 2. Clear all caches
php artisan optimize:clear

# 3. Cache everything for production
php artisan optimize

# 4. Run migrations
php artisan migrate --force

# 5. Create storage links
php artisan storage:link

# 6. Restart queue workers
php artisan queue:restart
```

**Composer.json configuration for production:**
```json
{
    "config": {
        "optimize-autoloader": true,
        "preferred-install": "dist",
        "sort-packages": true
    },
    "scripts": {
        "post-install-cmd": [
            "php artisan clear-compiled",
            "php artisan optimize"
        ]
    }
}
```

## Best practices for dependency management

**Include dev dependencies as production dependencies when:**
- ✅ Seeders use factories that require Faker
- ✅ Application generates demo/sample data for users
- ✅ Production environment needs development tools for debugging

**Keep as dev-only dependencies when:**
- ❌ Factories are only used for testing/development
- ❌ Production data can be seeded without Faker
- ❌ Tools are purely for development workflow

**Optimal production deployment approach:**
```bash
# Build phase (CI/CD server with dev dependencies)
composer install --dev
npm ci
npm run production
php artisan config:cache

# Deploy phase (production server without dev dependencies)
composer install --no-dev --optimize-autoloader --classmap-authoritative
```

## Updated deployment checklists

### Phase 1: Project Setup checklist
- [ ] **Environment Configuration**: Set up proper .env files for each environment with APP_ENV=production and APP_DEBUG=false in production
- [ ] **Security Setup**: Configure HTTPS, SSL certificates, proper file permissions (644 for files, 755 for directories)
- [ ] **Version Control**: Include composer.lock in version control, set up proper .gitignore, configure repository access
- [ ] **Infrastructure Planning**: Choose dedicated hosting (avoid shared hosting), plan server requirements, set up staging environment
- [ ] **Composer Strategy**: Decide on dev dependency handling strategy early, configure composer.json with production optimizations
- [ ] **Database Strategy**: Plan migration rollback procedures, separate production and development seeders

### Phase 2: Pre-deployment preparation checklist
- [ ] **Code Optimization**: Run `php artisan optimize`, clear all caches, compile assets with `npm run production`
- [ ] **Database Preparation**: Test migrations on staging, prepare production-safe seeders, backup production database
- [ ] **Security Hardening**: Run `composer audit`, update dependencies, configure web server security headers
- [ ] **Performance Testing**: Load test application, profile database queries, test caching mechanisms
- [ ] **Quality Assurance**: All tests pass, code review completed, staging environment tested
- [ ] **Deployment Readiness**: Production .env configured, deployment scripts tested, team notified

## Zero-downtime deployment strategies

**Symlink-based atomic deployment:**
```bash
#!/bin/bash
DEPLOY_PATH="/var/www/app"
RELEASE=$(date +%Y%m%d%H%M%S)
RELEASE_PATH="$DEPLOY_PATH/releases/$RELEASE"

# Create new release directory
mkdir -p $RELEASE_PATH

# Deploy code
git clone --depth 1 $REPO_URL $RELEASE_PATH
cd $RELEASE_PATH

# Install dependencies and optimize
composer install --no-dev --optimize-autoloader --no-interaction
npm ci && npm run production

# Create symlinks to shared resources
ln -nfs $DEPLOY_PATH/shared/.env $RELEASE_PATH/.env
ln -nfs $DEPLOY_PATH/shared/storage $RELEASE_PATH/storage

# Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force

# Atomic switch - update current symlink
ln -nfs $RELEASE_PATH $DEPLOY_PATH/current

# Cleanup old releases (keep last 5)
cd $DEPLOY_PATH/releases && ls -t | tail -n +6 | xargs rm -rf

# Restart services
sudo supervisorctl restart laravel-worker
sudo service php8.2-fpm reload
```

**Blue-green deployment with health checks:**
```bash
#!/bin/bash
CURRENT_ENV=$(cat /etc/active_environment)
NEW_ENV=$([[ $CURRENT_ENV == "blue" ]] && echo "green" || echo "blue")

# Deploy to inactive environment
deploy_to_environment() {
    ssh deploy@${1}-server.com "cd /var/www/app && git pull"
    ssh deploy@${1}-server.com "cd /var/www/app && composer install --no-dev --optimize-autoloader"
    ssh deploy@${1}-server.com "cd /var/www/app && php artisan migrate --force"
    ssh deploy@${1}-server.com "cd /var/www/app && php artisan optimize"
}

# Health check before switching
health_check() {
    response=$(curl -s -o /dev/null -w "%{http_code}" http://${1}-server.com/health)
    [[ $response == "200" ]] || { echo "Health check failed"; exit 1; }
}

deploy_to_environment $NEW_ENV
health_check $NEW_ENV

# Switch traffic by updating load balancer
sed -i "s/backend_$CURRENT_ENV/backend_$NEW_ENV/" /etc/nginx/sites-available/app
nginx -t && nginx -s reload

echo $NEW_ENV > /etc/active_environment
```

## Version matching strategies

**Exact versions (v1.2.3) recommended for:**
- Production environments requiring stability
- Critical dependencies affecting security
- Infrastructure tools (PHP, Composer, Node.js)

**Major versions (^1.0) acceptable for:**
- Development environments
- Non-critical packages
- Internal tools and utilities

**Version constraint examples:**
```json
{
    "require": {
        "php": "8.2.*",                    // Allow patch updates
        "laravel/framework": "^11.0",      // Allow compatible updates
        "guzzlehttp/guzzle": "7.8.1"      // Lock to exact version
    },
    "require-dev": {
        "fakerphp/faker": "^1.23",        // More flexibility in dev
        "phpunit/phpunit": "^10.0"
    }
}
```

**Package.json version strategy:**
```json
{
    "dependencies": {
        "vue": "3.3.4",                    // Exact version for stability
        "axios": "^1.6.0"                 // Allow compatible updates
    },
    "devDependencies": {
        "vite": "^4.0.0",                 // More flexibility in dev tools
        "tailwindcss": "^3.3.0"
    }
}
```

## Solving the Faker runtime failure

The specific case where build verification shows "Seeders use Faker and it's available" but runtime fails with "Class Faker\Factory not found" occurs due to a mismatch between build and runtime environments.

**Root cause analysis:**
1. Build server installs dev dependencies (includes Faker)
2. Build verification passes because Faker is available
3. Production server runs `composer install --no-dev`
4. Runtime fails because Faker is no longer available

**Comprehensive solution:**
```bash
# Build phase (CI/CD server)
composer install --dev                    # Install all dependencies for building
php artisan config:cache                  # Cache config with dev dependencies
npm run production                        # Build assets

# Create production-ready package
composer install --no-dev --optimize-autoloader --no-interaction
php artisan config:cache                  # Re-cache without dev dependencies
tar --exclude='tests' --exclude='.git' -czf app.tar.gz .

# Deployment phase (production server)
tar -xzf app.tar.gz                      # Extract pre-built package
php artisan migrate --force              # Run migrations
php artisan optimize                     # Final optimization
```

**Environment-specific seeder detection:**
```php
// Add to your base Seeder class
abstract class BaseSeeder extends Seeder
{
    protected function canUseFaker(): bool
    {
        return class_exists(\Faker\Factory::class);
    }
    
    protected function createUser($attributes = [])
    {
        if ($this->canUseFaker() && app()->environment('local', 'testing')) {
            return User::factory()->create($attributes);
        }
        
        return User::create(array_merge([
            'name' => 'Default User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ], $attributes));
    }
}
```

This comprehensive approach addresses all deployment challenges while providing specific, tested solutions for both dedicated servers and shared hosting environments. The key is establishing consistent processes that prevent common pitfalls through proper dependency management, version control, and systematic deployment procedures.