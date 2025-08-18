# Cloudways Cloud Hosting Setup Guide

## SSH Connection Details

### Step 1: Get Server Credentials
1. **Log into Cloudways Platform**: Access your Cloudways account
2. **Navigate to Server Management**:
   - Click on **Servers** in the main navigation
   - Select your target server
   - Find **Master Credentials** section

### Step 2: SSH Connection Information
**Server Details Available**:
- **Server Host/IP**: Displayed in server dashboard (e.g., `147.182.211.76`)
- **SSH Username**: Format `master_xxxxxxxxx` (e.g., `master_qhatdsfbhv`)
- **SSH Port**: Standard `22`
- **SSH Password**: Provided in Master Credentials section

### Step 3: Enable SSH Access for Applications
⚠️ **Important**: SSH access is **disabled by default** for applications.

**Enable SSH Access**:
1. Go to **Applications** → Select your app
2. Navigate to **Application Settings**
3. Scroll to **SSH Access** section
4. Toggle **Enable SSH Access**
5. Confirm the activation

### Step 4: SSH Key Authentication (Recommended)
1. **Generate SSH Key Pair**:
   ```bash
   ssh-keygen -t rsa -b 2048 -f ~/.ssh/cloudways_key
   cat ~/.ssh/cloudways_key.pub
   ```

2. **Add SSH Public Key**:
   - In Server Management Dashboard
   - Click **SSH Public Keys**
   - Add label for your key
   - Paste public key content
   - Click **Add Key**

### Step 5: Connect via SSH
**Using Terminal (Mac/Linux)**:
```bash
ssh master_username@server_ip
# Example: ssh master_qhatdsfbhv@147.182.211.76

# With SSH key:
ssh -i ~/.ssh/cloudways_key master_username@server_ip
```

**Using PuTTY (Windows)**:
- Host Name: Server IP from dashboard
- Port: `22`
- Connection Type: SSH
- Username: `master_username`
- Password: From Master Credentials

**Using Cloudways Built-in Terminal**:
- Server Dashboard → **SSH Terminal** → Opens browser-based terminal
- Automatically authenticated, no credentials needed

---

## Server Hosting Details

### Cloud Provider Options
| Cloud Provider | Features | Typical Specs |
|----------------|----------|---------------|
| **DigitalOcean** | SSD, Multiple regions | 1GB+ RAM, SSD storage |
| **Linode** | High performance | 1GB+ RAM, SSD storage |
| **Vultr** | Global presence | 1GB+ RAM, SSD storage |
| **AWS** | Enterprise grade | Variable specs |
| **Google Cloud** | Advanced features | Variable specs |

### Server Specifications
- **Hosting Provider**: Cloudways (Managed Cloud)
- **Server OS**: Ubuntu 20.04/22.04 LTS
- **Web Server**: Apache or Nginx (selectable)
- **PHP Version**: Multiple versions (7.4, 8.0, 8.1, 8.2, 8.3)
- **Node.js Version**: Available for all plans

### Cloudways Features
- **Managed Updates**: Automatic security patches
- **1-Click SSL**: Free Let's Encrypt certificates
- **Git Integration**: Built-in deployment
- **Staging Environment**: One-click staging areas
- **CDN**: Cloudflare enterprise integration

---

## Domain & URL Configuration

### Domain Management
**Primary Domain Setup**:
1. **Applications** → Select app → **Domain Management**
2. Add your domain name
3. Configure DNS pointing to server IP
4. Enable SSL certificate

### Domain Structure
```
Application Level:
├── Primary Domain: yourdomain.com
├── Staging Domain: staging-yourdomain.com (auto-generated)
└── Temporary Domain: server-ip-based access
```

### SSL Certificate Management
**Let's Encrypt SSL (Free)**:
1. **Applications** → **SSL Certificate**
2. Select **Let's Encrypt**
3. Add domain(s)
4. Click **Install Certificate**
5. **Auto-renewal**: ✅ Enabled by default

**Custom SSL Certificate**:
1. Upload certificate files
2. Private key, certificate, and CA bundle
3. Install on domain

---

## Database Information

### Database Configuration
**Standard Database Setup**:
- **Database Type**: MySQL 8.0 or MariaDB 10.6
- **Database Host**: `localhost` (same server)
- **Database Port**: `3306`
- **Database Name**: Auto-generated or custom
- **Database Username**: Application-specific
- **Database Password**: Auto-generated (secure)

### Database Access Methods
1. **phpMyAdmin**: Built-in database management
   - **Applications** → **Database Access** → **phpMyAdmin**
   - Access via browser with auto-login

2. **Remote Database Access**: 
   - Enable in **Server Settings** → **Packages & Services**
   - Use server IP, port 3306
   - Whitelist your IP address

3. **SSH Tunnel**: Secure remote access
   ```bash
   ssh -L 3306:localhost:3306 master_user@server_ip
   ```

### Database Backup Strategy
- **Automated Backups**: Available in server settings
- **On-demand Backups**: Manual backup creation
- **Backup Frequency**: Daily, weekly, or custom
- **Backup Retention**: Configurable retention period

---

## Server Directory Structure

### Application Directory Structure
```
/home/master_user/
├── applications/
│   └── app_name/
│       ├── public_html/          (Document root)
│       ├── logs/                 (Application logs)
│       ├── backup/              (Local backups)
│       └── tmp/                 (Temporary files)
├── server/
│   ├── etc/                     (Configuration files)
│   └── logs/                    (Server logs)
└── .ssh/                        (SSH keys)
```

### Document Root Paths
**Standard Path**:
```
/home/master_user/applications/app_name/public_html
```

**Custom Application Deployment**:
```bash
# Navigate to application directory
cd /home/master_user/applications/app_name

# Check current directory
pwd
# Output: /home/master_user/applications/app_name

# Web-accessible files go in:
# public_html/ directory
```

### File Permissions
- **Web User**: `www-data`
- **File Owner**: `master_user`
- **Group**: `www-data`
- **Standard Permissions**:
  - Files: `644`
  - Directories: `755`
  - Executable files: `755`

---

## Authentication & Security

### Server-Level Security
**Firewall Configuration**:
- Managed by Cloudways
- HTTP/HTTPS ports open by default
- Custom port opening available

**Security Features**:
- **Two-Factor Authentication**: Account level
- **IP Whitelisting**: SSH and admin access
- **Regular Security Updates**: Automated
- **DDoS Protection**: Included with all plans

### Application Security
**Environment Variables**:
```bash
# Access via SSH
vim /home/master_user/applications/app_name/.env

# Common configuration:
DB_HOST=localhost
DB_DATABASE=app_database_name
DB_USERNAME=app_db_user
DB_PASSWORD=secure_generated_password

APP_URL=https://yourdomain.com
APP_KEY=base64:generated_app_key
```

### Third-Party Integrations
**Email Configuration**:
- **SMTP Settings**: Via application configuration
- **Elastic Email**: Integrated addon
- **SendGrid**: Third-party SMTP service

**CDN & Performance**:
- **Cloudflare Enterprise**: Built-in integration
- **Redis**: Available as addon
- **Memcached**: Built-in caching
- **Varnish**: Advanced caching layer

---

## Deployment & Git Integration

### Git Deployment Setup
1. **Applications** → **Deployment Via Git**
2. **Add Repository**:
   - Repository URL (GitHub, GitLab, Bitbucket)
   - Branch selection (main, develop, etc.)
   - Deployment path (usually `public_html`)

3. **Deploy Process**:
   - **Manual Deploy**: Click deploy button
   - **Auto Deploy**: Webhook-triggered
   - **Build Commands**: Custom build scripts

### Deployment Workflow
```bash
# Git deployment process:
1. Code pushed to repository
2. Webhook triggers Cloudways
3. Code pulled to staging area
4. Build commands executed
5. Files moved to public_html
6. Application restarted if needed
```

### Staging Environment
**One-Click Staging**:
1. **Applications** → **Staging Management**
2. **Create Staging Area**
3. **Clone Production**: Data and files copied
4. **Independent Environment**: Separate domain and database

---

## Advanced Features & Monitoring

### Performance Monitoring
**Server Monitoring**:
- CPU usage graphs
- RAM utilization
- Disk space monitoring
- Database performance metrics

**Application Monitoring**:
- PHP error logs
- Access logs
- Application-specific metrics
- Real-time monitoring dashboard

### Backup & Recovery
**Backup Options**:
- **Server Backups**: Full server snapshots
- **Application Backups**: App-specific backups
- **Database Backups**: Separate database dumps
- **Scheduled Backups**: Automated scheduling

**Recovery Process**:
1. **Server Level**: Restore entire server
2. **Application Level**: Restore specific app
3. **Database Level**: Restore database only
4. **File Level**: Restore individual files

---

## Important Notes & Best Practices

### Cloudways-Specific Features
**Advantages**:
- Fully managed infrastructure
- 1-click installations (WordPress, Laravel, etc.)
- Built-in staging environments
- Advanced caching (Redis, Memcached, Varnish)
- 24/7 expert support

**Limitations**:
- Limited root access (managed environment)
- Specific software stack (can't install arbitrary software)
- Cost increases with resource usage
- Platform lock-in considerations

### Common Use Cases
**Development Workflow**:
1. Develop locally
2. Push to Git repository
3. Auto-deploy to staging
4. Test on staging environment
5. Deploy to production

**Scaling Options**:
- **Vertical Scaling**: Increase server resources
- **Horizontal Scaling**: Multiple servers with load balancer
- **Auto-scaling**: Available on AWS/Google Cloud

### Troubleshooting
**SSH Connection Issues**:
1. Verify SSH is enabled for application
2. Check server firewall settings
3. Confirm correct credentials
4. Try browser-based SSH terminal

**Application Issues**:
1. Check application logs
2. Verify file permissions
3. Review PHP error logs
4. Contact Cloudways support

### Support Resources
- **24/7 Live Chat**: Available for all plans
- **Knowledge Base**: Comprehensive documentation
- **Video Tutorials**: Step-by-step guides
- **Community Forum**: User discussions
- **Expert Support**: Dedicated support team