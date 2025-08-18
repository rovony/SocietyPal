# cPanel Hosting Setup Guide

## SSH Connection Details

### Step 1: Check SSH Access Availability
⚠️ **Important**: SSH access is **disabled by default** on most cPanel shared hosting accounts. Contact your hosting provider to enable SSH access before proceeding.

### Step 2: Enable SSH Access in cPanel
1. **Log into cPanel**: Access your hosting provider's cPanel interface
2. **Navigate to SSH Settings**: 
   - Look for **SSH Access** under the **Security** section
   - If not visible, SSH is likely disabled by your provider

### Step 3: Get SSH Connection Details
**Standard cPanel SSH Configuration**:
- **Server Host/IP**: Your server's IP address or hostname
- **SSH Username**: Your cPanel username
- **SSH Port**: Usually `22` (may be custom like `2222` or `2211`)
- **SSH Password**: Your cPanel account password

### Step 4: SSH Key Setup (Recommended)
1. **Generate SSH Key Pair**:
   ```bash
   ssh-keygen -t rsa -b 2048 -f ~/.ssh/cpanel_key
   cat ~/.ssh/cpanel_key.pub
   ```

2. **Add Public Key in cPanel**:
   - Go to **SSH Access** → **Manage SSH Keys**
   - Click **Import Key** or **Generate New Key**
   - Paste your public key
   - Click **Import** then **Authorize**

### Step 5: Connect via SSH
**Using Terminal (Mac/Linux)**:
```bash
ssh -p [PORT] [USERNAME]@[SERVER_IP]
# With key authentication:
ssh -p 2211 -i ~/.ssh/cpanel_key cpaneluser@server.example.com
```

**Using PuTTY (Windows)**:
- Host Name: `server.example.com`
- Port: `2211`
- Connection Type: SSH
- Username: Your cPanel username
- Password: Your cPanel password

**Finding SSH Port**:
If unknown, contact your hosting provider or check WHM → Service Status → SSH Daemon logs

---

## Server Hosting Details

### Server Information
- **Hosting Provider**: Various (GoDaddy, Bluehost, SiteGround, etc.)
- **Server OS**: Linux (CentOS, AlmaLinux, Rocky Linux)
- **Web Server**: Apache (standard), Nginx (some providers)
- **PHP Version**: Multiple versions available (5.6 to 8.3)
- **Node.js Version**: Available on some providers with SSH access

### cPanel Account Types
| Account Type | SSH Access | Root Access | Features |
|--------------|------------|-------------|----------|
| Shared Hosting | 🔒 Request Required | ❌ No | Basic cPanel |
| VPS/Dedicated | ✅ Yes | ✅ Yes (WHM) | Full Control |
| Reseller | ✅ Possible | 🔒 Limited | WHM Access |

---

## Domain & URL Configuration

### Domain Structure in cPanel
**Primary Domain**: Main domain associated with the account
**Addon Domains**: Additional domains hosted in the same account
**Subdomains**: Subdivisions of your primary or addon domains
**Parked Domains**: Domain aliases pointing to primary domain

### Domain Path Structure
**Primary Domain**:
```
/home/username/public_html/
├── index.html/.php        (Main site)
├── .htaccess             (URL rewriting)
└── subdirectory/         (Subdirectories)
```

**Addon Domain Structure**:
```
/home/username/
├── public_html/          (Primary domain)
├── addondomain.com/      (Addon domain folder)
│   └── public_html/      (Addon domain files)
└── logs/                 (Domain logs)
```

### SSL Certificate Configuration
1. **Access SSL/TLS Settings**:
   - cPanel → **SSL/TLS** section
   - Choose **Manage SSL Sites**

2. **SSL Options**:
   - **Let's Encrypt**: Free SSL (if available)
   - **AutoSSL**: Automatic SSL management
   - **Upload Certificate**: Custom SSL certificates

**SSL Setup Steps**:
1. Generate or obtain SSL certificate
2. Upload certificate files via cPanel
3. Install on domain
4. Enable HTTPS redirects

---

## Database Information

### Database Connection Details
**MySQL Database Configuration**:
- **Database Type**: MySQL 5.7/8.0 or MariaDB
- **Database Host**: `localhost` (same server)
- **Database Port**: `3306` (standard)
- **Database Name**: Format `cpanel_user_dbname`
- **Database Username**: Format `cpanel_user_dbuser`
- **Database Password**: Set during creation

### Database Naming Convention
```
cPanel Username: cpaneluser (8 characters max)
Database: cpaneluser_myapp
DB User: cpaneluser_dbuser
```

### Database Management Tools
- **phpMyAdmin**: Web-based MySQL administration
- **Remote Database Access**: Configure if needed
- **Database Backups**: Through cPanel backup feature

**Database Access Steps**:
1. cPanel → **MySQL Databases**
2. Create database and user
3. Assign user to database with full privileges
4. Access via **phpMyAdmin** for management

---

## Server Directory Structure

### Document Root Locations
**Primary Domain**:
- **Document Root**: `/home/username/public_html`
- **Full Path**: `/home/username/public_html/`

**Addon Domains**:
- **Document Root**: `/home/username/addondomain.com`
- **Alternative**: `/home/username/public_html/addondomain.com`

### cPanel Directory Structure
```
/home/username/
├── public_html/           (Primary domain web root)
├── addondomain.com/       (Addon domain directory)
├── mail/                  (Email data)
├── logs/                  (Access/error logs)
├── tmp/                   (Temporary files)
├── .cpanel/               (cPanel configuration)
└── perl/                  (Perl modules)
```

### File Permissions
- **Web User**: `apache` or `nobody`
- **File Owner**: Your cPanel username
- **Group**: Your cPanel username or `apache`
- **Standard Permissions**:
  - Files: `644`
  - Directories: `755`
  - CGI Scripts: `755`

---

## Addon Domain Management via SSH/API

### Creating Addon Domains via Command Line
**Using cpapi2** (cPanel API v2):
```bash
# Add addon domain
/usr/local/cpanel/bin/cpapi2 AddonDomain addaddondomain \
  newdomain=example.com \
  subdomain=example \
  dir=example.com

# Alternative with full path
cpapi2 AddonDomain addaddondomain \
  dir=%2Faddondomain%2Fpath \
  newdomain=addondomain.com \
  subdomain=addondomain
```

**Note**: `%2F` represents `/` in URL encoding for directory paths.

### Command Variations
**For subdomain creation**:
```bash
cpapi2 SubDomain addsubdomain \
  domain=subdomain \
  rootdomain=example.com \
  dir=%2Fpublic_html%2Fsubdirectory
```

---

## Authentication & Security

### Application Configuration
**Environment Variables Setup**:
```bash
# Database configuration
DB_HOST=localhost
DB_DATABASE=cpaneluser_dbname
DB_USERNAME=cpaneluser_dbuser
DB_PASSWORD=secure_password

# Application settings
APP_URL=https://yourdomain.com
APP_ENV=production
APP_KEY=base64:generated_key_here
```

### Security Best Practices
1. **Strong Passwords**: Use complex passwords for all accounts
2. **Two-Factor Authentication**: Enable if available
3. **IP Restrictions**: Limit SSH access to specific IPs
4. **Regular Updates**: Keep software updated
5. **File Permissions**: Proper permission settings

### Third-Party Integrations
- **Email Service**: cPanel email or SMTP providers
- **Payment Gateways**: Configure via application settings
- **CDN Services**: Cloudflare integration available
- **Backup Services**: Regular automated backups

---

## Important Notes & Variations

### Provider-Specific Differences
**GoDaddy cPanel**:
- SSH often requires special request
- Custom SSH ports common
- AutoSSL typically enabled

**Bluehost cPanel**:
- SSH available on higher plans
- Shell access jailed to user directory
- Advanced features in Business+ plans

**SiteGround cPanel**:
- SSH enabled by default on higher tiers
- Git integration available
- Advanced caching options

### Common Limitations
1. **Shared Hosting**: Limited SSH access, no root privileges
2. **Resource Limits**: CPU, memory, and disk quotas
3. **Software Restrictions**: Limited ability to install software
4. **Port Restrictions**: Limited outbound connections

### Troubleshooting
**SSH Connection Issues**:
1. Verify SSH is enabled by provider
2. Check correct port number
3. Confirm username and password
4. Contact hosting support if needed

**Permission Errors**:
```bash
# Fix common permission issues
chmod 755 public_html/
chmod 644 public_html/*.php
```

### Support Resources
- **Hosting Provider Support**: Primary support channel
- **cPanel Documentation**: Official cPanel guides
- **Community Forums**: Provider-specific communities
- **WHM Access**: For VPS/Dedicated server management

---

## Deployment Strategies

### File Upload Methods
1. **cPanel File Manager**: Web-based file management
2. **FTP/SFTP**: Traditional file transfer
3. **SSH/SCP**: Command-line file transfer
4. **Git**: Version control deployment (if available)

### Typical Deployment Flow
1. Upload files to document root
2. Configure database connections
3. Set proper file permissions
4. Configure SSL certificates
5. Test functionality
6. Setup regular backups