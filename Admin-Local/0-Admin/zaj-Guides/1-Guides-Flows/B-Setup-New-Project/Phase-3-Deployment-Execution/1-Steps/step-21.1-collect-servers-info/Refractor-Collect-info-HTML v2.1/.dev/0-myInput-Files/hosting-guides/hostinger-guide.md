# Hostinger Hosting Setup Guide

## SSH Connection Details

### Step 1: Enable SSH Access
1. **Log into hPanel**: Go to your Hostinger account dashboard
2. **Navigate to SSH Settings**: 
   - Go to **Websites** → Select your hosting plan → **Manage**
   - On the sidebar, find **SSH Access** under **Security & Performance**
   - Click **Enable SSH** if not already enabled

### Step 2: Get SSH Connection Details
- **Server Host/IP**: Available in hPanel SSH section (e.g., `185.185.185.185`)
- **SSH Username**: Displayed in hPanel (format: `u123456789`)
- **SSH Port**: Default is `22` (may be custom port like `65002`)
- **SSH Password**: Same as your FTP password for the main domain

### Step 3: SSH Key Setup (Optional but Recommended)
1. **Generate SSH Key Pair** (if not exists):
   ```bash
   ssh-keygen -t rsa -b 2048
   cat ~/.ssh/id_rsa.pub
   ```
2. **Add Public Key in hPanel**:
   - Go to SSH Access section
   - Click **Add SSH Key**
   - Provide a name for your key
   - Paste your public key content
   - Click **Add SSH Key**

### Step 4: Connect via SSH
**Using Terminal (Mac/Linux)**:
```bash
ssh -p [PORT] [USERNAME]@[IP_ADDRESS]
# Example: ssh -p 65002 u123456789@185.185.185.185
```

**Using PuTTY (Windows)**:
- Host Name: `185.185.185.185`
- Port: `65002`
- Connection Type: SSH
- Username: `u123456789`
- Password: Your FTP password

---

## Server Hosting Details

### Server Information
- **Hosting Provider**: Hostinger
- **Server OS**: Linux (typically CentOS/CloudLinux)
- **Web Server**: Apache or LiteSpeed (varies by plan)
- **PHP Version**: Multiple versions available (7.4, 8.0, 8.1, 8.2, 8.3)
- **Node.js Version**: Available on Business and higher plans

### Plan Types & Features
| Plan Type | SSH Access | PHP Versions | Node.js | Database |
|-----------|------------|--------------|---------|----------|
| Single Web | ❌ No | Limited | ❌ No | MySQL |
| Premium Web | ✅ Yes | Multiple | ❌ No | MySQL |
| Business Web | ✅ Yes | Multiple | ✅ Yes | MySQL |
| Cloud | ✅ Yes | Multiple | ✅ Yes | MySQL |

---

## Domain & URL Configuration

### Domain Settings
- **Production Domain**: Set in hPanel → Domains
- **Staging Domain**: Create subdomain (e.g., `staging.yourdomain.com`)
- **Development Domain**: Create additional subdomain if needed

### Domain Path Structure
**Main Domain Structure**:
```
/home/u123456789/domains/yourdomain.com/
├── public_html/          (Document root)
├── logs/                 (Access/error logs)
├── deploy/              (Deployment directory - custom)
└── private_html/        (Private files)
```

**Custom Deploy Path Example**:
```
/home/u123456789/domains/yourdomain.com/deploy/
```

### SSL Certificate
- **SSL Provider**: Let's Encrypt (free) or custom SSL
- **SSL Auto-renewal**: ✅ Automatic for Let's Encrypt
- **SSL Setup**: Available in hPanel → SSL section

**SSL Configuration Steps**:
1. Go to hPanel → **Websites** → **Manage**
2. Navigate to **Security** → **SSL**
3. Select SSL type (Let's Encrypt recommended)
4. Click **Setup** and wait for activation

---

## Database Information

### Database Connection Details
**Standard MySQL Configuration**:
- **Database Type**: MySQL 5.7/8.0
- **Database Host**: `localhost` (for same server)
- **Database Port**: `3306` (default)
- **Database Name**: Format `u123456789_dbname`
- **Database Username**: Format `u123456789_user`
- **Database Password**: Set during creation

### Database Management
- **Admin Access**: ✅ phpMyAdmin (available in hPanel)
- **Backup Strategy**: ✅ Automated daily backups
- **Migration Access**: ✅ Through phpMyAdmin or SSH

**Database Access Path**:
```
hPanel → Databases → MySQL Databases → phpMyAdmin
```

---

## Server Directory Structure

### Document Root Configuration
**Main Domain**:
- **Document Root**: `/home/u123456789/domains/yourdomain.com/public_html`
- **Project Directory**: `/home/u123456789/domains/yourdomain.com/`
- **Deploy Directory**: `/home/u123456789/domains/yourdomain.com/deploy` (custom)

**Subdomain Structure**:
```
/home/u123456789/domains/yourdomain.com/public_html/subdomain/
```

### File Permissions
- **Web User**: `apache` or `litespeed`
- **File Owner**: `u123456789` (your username)
- **Group**: `u123456789`
- **Standard Permissions**:
  - Files: `644`
  - Directories: `755`
  - Executables: `755`

### Directory Structure Variations

#### Standard Hostinger Structure
```
/home/u123456789/
├── domains/
│   └── yourdomain.com/
│       ├── public_html/     (Web accessible)
│       ├── logs/           (Server logs)
│       └── deploy/         (Custom deploy path)
├── public_html/           (Legacy structure)
└── tmp/                   (Temporary files)
```

#### Important Notes
- **Public HTML Locked**: Cannot change document root location
- **Deploy Path**: Custom paths like `/deploy` must be manually created
- **File Upload**: Use File Manager or FTP to `/public_html`

---

## Authentication & Security

### Application Secrets
- **App Key**: Generate securely and store in environment files
- **JWT Secret**: If using JWT authentication
- **Encryption Keys**: Store outside web-accessible directory
- **API Keys**: Keep in secure configuration files

### Environment Configuration
**Recommended .env structure**:
```
DB_HOST=localhost
DB_DATABASE=u123456789_dbname
DB_USERNAME=u123456789_user
DB_PASSWORD=your_secure_password

APP_URL=https://yourdomain.com
APP_KEY=your_app_key

MAIL_DRIVER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=your_email@yourdomain.com
```

### Third-Party Services
- **Email Service**: Hostinger SMTP or external (Gmail, SendGrid)
- **Payment Gateway**: Stripe, PayPal (configure via environment)
- **Storage Service**: Local storage or external CDN
- **CDN Service**: Cloudflare (recommended)

---

## Deployment Process

### Git Deployment (Recommended)
1. **Setup Git Repository** in hPanel:
   - Go to **Git** section
   - Add repository URL
   - Set branch (usually `main` or `master`)
   - Set install path (leave empty for `/public_html`)

2. **Auto-Deploy Setup**:
   - Enable webhook URL
   - Configure in your Git repository
   - Auto-deploys on push

### Manual Deployment
1. **File Upload Methods**:
   - hPanel File Manager
   - FTP/SFTP client
   - SSH (scp/rsync)

2. **Deployment Structure**:
```bash
# Upload to custom deploy directory
/home/u123456789/domains/yourdomain.com/deploy/

# Then symlink or copy to public_html
ln -s /home/u123456789/domains/yourdomain.com/deploy/public/* /home/u123456789/domains/yourdomain.com/public_html/
```

---

## Important Notes & Variations

### Plan-Specific Limitations
- **Single Plans**: No SSH, limited features
- **Premium+**: Full SSH access, multiple PHP versions
- **Business+**: Node.js support, advanced features

### Common Issues & Solutions
1. **Permission Errors**: Check file permissions (644/755)
2. **Database Connection**: Verify host is `localhost`
3. **SSL Issues**: Allow 24-48 hours for propagation
4. **Deploy Path**: Must be created manually via SSH or File Manager

### File Manager Access
**Two Access Types**:
1. **Domain-specific**: Opens directly in `public_html`
2. **Full hosting**: Access to entire hosting structure including `domains/` folder

### Support Resources
- **24/7 Chat Support**: Available in hPanel
- **Knowledge Base**: Comprehensive documentation
- **Video Tutorials**: Step-by-step guides
- **Community Forum**: User discussions and solutions