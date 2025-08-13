# Herd Free & Pro Comprehensive Reference Guide

This guide provides exhaustive, step-by-step instructions for installing, configuring, and using every aspect of Herd (both Free and Pro editions). It is organized by topic and includes exact commands, GUI workflows, configuration snippets, and usage examples to empower AI agents, human developers, and dev teams.

***

## 1. Installation & Licensing

### 1.1 Herd Free Installation (macOS)
1. Download Herd Free DMG from https://herd.pm.  
2. Open the DMG and drag **Herd.app** into **/Applications**.  
3. Launch Herd; grant network and filesystem permissions when prompted.

### 1.2 Upgrade to Herd Pro
1. In the menu bar, click the Herd tray icon → **Account → Upgrade to Pro**.  
2. Enter your license key, click **Activate**.  
3. Herd restarts; verify the **"Pro"** badge appears next to the tray icon.

***

## 2. Site Management & Initial Setup

### 2.1 Linking & Creating Sites
– Terminal:
  ```bash
  cd ~/Projects/my-laravel-app
  herd link                       # Link current folder as a new site
  herd unlink my-laravel-app.test # Unlink a site
  ```
– Herd GUI:
  1. Open **Herd → Settings → Sites**.  
  2. Click **Add Site**; set:
     – **Name/Domain:** `myapp.test`  
     – **Path:** `/Users/you/Projects/my-laravel-app`  
     – **PHP Version:** select from installed list  
     – **HTTPS:** toggle ON  
  3. Click **Save**, then **Open**.

**📌 Important:** Site management (listing, detailed configuration) is primarily GUI-based. Use `herd link/unlink` for basic operations.  

### 2.2 Managing PHP Versions
1. Herd UI → **Settings → PHP Versions**.  
2. Click **+** to download/install additional PHP versions (7.4, 8.0, 8.1, 8.2, 8.3).  
3. Assign a version per site via **Sites → Edit → PHP Version**.

***

## 3. Herd Core Services

All services run on localhost and are managed via the Herd UI's **Services** tab.

| Service     | Port   | Credentials                |
|-------------|--------|----------------------------|
| MySQL       | 3306   | user: root, no password    |
| MariaDB     | 3307   | user: root, no password    |
| PostgreSQL  | 5432   | user: postgres, no password|
| MongoDB     | 27017  | no auth by default         |
| Redis       | 6379   | no auth by default         |
| SMTP (Mail) | 1025   | no auth                    |
| MinIO       | 9000   | AccessKey: `minio`, Secret: `minio123` |

### 3.1 Enabling Services
1. Herd UI → **Services**.  
2. Toggle desired service to **ON**; confirm green status.  
3. Click **Settings** for any service to customize port, password, or persistence directory.

**📌 Important:** Service management is **GUI-only**. The Herd CLI does not include a `services` command.

**CLI Alternative (Unofficial):**
```bash
# List all Herd-managed services
launchctl list | grep herd

# Example: stop/start Herd's MySQL service
launchctl stop com.tiny.team.Herd.MySQL
launchctl start com.tiny.team.Herd.MySQL
```

**⚠️ Note:** `launchctl` commands are not officially supported by Herd. Use the GUI for reliable service management.

### 3.2 CLI Access Examples
- MySQL:
  ```bash
  mysql -u root -h 127.0.0.1 -P 3306
  ```
- MariaDB:
  ```bash
  mysql -u root -h 127.0.0.1 -P 3307
  ```
- PostgreSQL:
  ```bash
  psql -U postgres -h 127.0.0.1 -p 5432
  ```
- MongoDB:
  ```bash
  mongo --host 127.0.0.1 --port 27017
  ```
- Redis:
  ```bash
  redis-cli -h 127.0.0.1 -p 6379
  ```
- SMTP (for local email testing):
  ```bash
  telnet 127.0.0.1 1025
  ```

***

## 4. Debugging & Profiling (Pro)

### 4.1 Xdebug Setup & Detection
1. Herd UI → **Settings → PHP Versions → Configure Xdebug**.  
2. Set:
   ```
   xdebug.mode=debug
   xdebug.start_with_request=yes
   xdebug.client_host=127.0.0.1
   xdebug.client_port=9003
   ```
3. Restart PHP.  
4. Use any DBGp-compatible IDE (VS Code, PHPStorm) listening on port 9003.  
5. Herd's tray icon shows **"Xdebug Active"** when requests include `XDEBUG_SESSION`.

### 4.2 PRO Profiler
1. Herd UI → **Services → Profiler** (Pro-only).  
2. Toggle **ON** for a site.  
3. Visit the site; click the floating toolbar to view request timeline, database queries, memory usage, and call graphs.

### 4.3 Log Viewer
1. Herd UI → **Logs** → select site.  
2. Browse, filter, and tail Laravel logs (`storage/logs/laravel.log`) and Herd's own logs.

### 4.4 Tinker Dumps
– In Laravel code:
  ```php
  dump($var);
  dd($var);
  ```
– Output appears inline in the browser and in Herd's log console, showing file and line references.

***

## 5. Integrations & Extras (Pro)

### 5.1 Forge Integration
1. Install Forge CLI:
   ```bash
   composer global require laravel/forge-cli
   ```
2. Login & list sites:
   ```bash
   forge login
   forge site:list
   ```
3. Deploy from local:
   ```bash
   forge deploy --site=myapp.com
   ```
4. SSH into server:
   ```bash
   forge ssh --site=myapp.com
   ```
5. Fetch remote `.env`:
   ```bash
   forge env --site=myapp.com > .env.production
   ```

### 5.2 Expose (Public Tunnel)
1. Herd UI → **Services → Expose**.  
2. Toggle **ON** for a site; copy the generated public URL:  
   ```
   https://abcd1234.expose.herd.pm
   ```
3. Use for client demos or webhook endpoints.

### 5.3 Tinkerwell Integration
1. Herd UI → **Settings → Integrations → Tinkerwell**.  
2. Open Tinkerwell; click **Add Herd Project** → select your site.  
3. Instantly run code in the context of your Laravel app.

***

## 6. Search & Indexing Services (Pro)

### 6.1 Typesense
1. Herd UI → **Services → Typesense**.  
2. Default port **8108**, no auth.  
3. Connect via:
   ```bash
   curl -X GET 'http://127.0.0.1:8108/health'
   ```
4. Configure in Laravel Scout:
   ```env
   SCOUT_DRIVER=typesense
   TYPESENSE_HOST=127.0.0.1
   TYPESENSE_PORT=8108
   ```

### 6.2 Meilisearch
1. Herd UI → **Services → Meilisearch**.  
2. Default port **7700**, no auth.  
3. Connect via:
   ```bash
   curl 'http://127.0.0.1:7700/health'
   ```
4. Laravel Scout config:
   ```env
   SCOUT_DRIVER=meilisearch
   MEILISEARCH_HOST=http://127.0.0.1:7700
   ```

***

## 7. Storage Services (Pro)

### 7.1 MinIO (S3-Compatible)
1. Herd UI → **Services → MinIO**.  
2. Default endpoint: `http://127.0.0.1:9000`  
   – Access Key: `minio`  
   – Secret Key: `minio123`  
3. Laravel filesystem config in `config/filesystems.php`:
   ```php
   's3' => [
     'driver' => 's3',
     'key'    => env('MINIO_KEY', 'minio'),
     'secret' => env('MINIO_SECRET', 'minio123'),
     'endpoint'=> env('MINIO_ENDPOINT', 'http://127.0.0.1:9000'),
     'region' => 'us-east-1',
     'bucket' => 'my-bucket',
     'use_path_style_endpoint' => true,
   ],
   ```
4. In `.env`:
   ```
   MINIO_KEY=minio
   MINIO_SECRET=minio123
   MINIO_ENDPOINT=http://127.0.0.1:9000
   AWS_BUCKET=my-bucket
   ```

***

## 8. Version Matrix & Supported Frameworks

| Component     | Versions Supported                    |
|---------------|---------------------------------------|
| PHP           | 7.4, 8.0, 8.1, 8.2, 8.3               |
| Node.js       | 14.x, 16.x, 18.x                      |
| Laravel       | 6.x, 7.x, 8.x, 9.x, 10.x             |
| Symfony       | 4.x, 5.x, 6.x                         |
| WordPress     | 5.x, 6.x                              |
| Next.js       | 12.x, 13.x                            |
| Nuxt.js       | 2.x, 3.x                              |
| Drupal        | 8.x, 9.x, 10.x                        |
| CraftCMS      | 3.x, 4.x                              |
| Static Sites  | Hugo, Jekyll, Eleventy                |

***

## 9. Extending Herd & Custom Drivers

### 9.1 Creating a Custom Service Driver
1. Create a JS file: `~/.herd/drivers/my-service.js`
   ```js
   module.exports = {
     name: 'my-service',
     version: '1.0.0',
     install() {
       /* download and install logic */
     },
     start() {
       /* start service logic */
     },
     stop() {
       /* stop service logic */
     },
     status() {
       /* return running status */
     }
   };
   ```
2. Restart Herd; **my-service** appears under **Services**.

***

## 10. Best Practices & Troubleshooting

- **Environment Caching**:  
  ```bash
  php artisan config:clear && php artisan cache:clear && php artisan config:cache
  ```
- **Socket vs TCP**: Ensure `DB_HOST=127.0.0.1` (not `localhost`) to force TCP.  
- **Disk Space**: Monitor free space; clear unused logs in `~/Library/Application Support/Herd/Log`.  
- **Permissions**:  
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R $(whoami):staff storage bootstrap/cache
  ```
- **Service Restarts**: Toggle problematic services off/on in Herd UI.  
- **PHP Binary**: Verify correct PHP with `which php` and `php -v`.
- **SSL Certificates**: Use Herd GUI Sites → Open for automatic certificate trust.

***

**Important Notes:**
- Service management is **GUI-only** - no `herd services` command exists
- Site listing requires GUI - use Sites tab in Herd app
- For CLI automation, use `launchctl` commands (unofficial) or standard `php artisan` commands
- Most reliable approach: Use Herd GUI for service/site management, CLI for Laravel operations

***

This guide delivers every setting, command, integration, extension, and debugging tool available in Herd Free and Pro, ready for distribution to AI agents, human developers, and dev teams.
