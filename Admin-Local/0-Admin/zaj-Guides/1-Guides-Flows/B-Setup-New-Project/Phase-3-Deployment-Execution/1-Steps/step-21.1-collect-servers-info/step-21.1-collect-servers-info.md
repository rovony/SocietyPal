# Step 21.1: Collect Server & Deployment Information

## Overview
Before executing any deployment scenario (Local Build + SSH, GitHub Actions, DeployHQ, or Git Pull + Manual), gather all necessary server details and configuration information. This preparation step ensures smooth deployment execution and prevents mid-process interruptions due to missing credentials or configuration details.

## Information Collection Checklist

### 🖥️ Server Access Information

#### SSH Connection Details
- [ ] **Server Host/IP**: `_________________`
- [ ] **SSH Username**: `_________________`
- [ ] **SSH Port**: `________` (default: 22)
- [ ] **SSH Private Key**: Secured and accessible
- [ ] **SSH Public Key**: Added to server's authorized_keys

#### Server Hosting Details
- [ ] **Hosting Provider**: `_________________` (e.g., DigitalOcean, AWS, Hostinger, cPanel)
- [ ] **Server OS**: `_________________` (e.g., Ubuntu 20.04, CentOS 7)
- [ ] **Web Server**: `_________________` (Apache/Nginx)
- [ ] **PHP Version**: `________` (e.g., 8.1, 8.2)
- [ ] **Node.js Version**: `________` (if frontend builds required)

### 🌐 Domain & URL Configuration

#### Domain Settings
- [ ] **Production Domain**: `https://_________________`
- [ ] **Staging Domain**: `https://_________________` (e.g., staging.yourdomain.com)
- [ ] **Development Domain** (optional): `https://_________________`

#### SSL Certificate
- [ ] **SSL Provider**: `_________________` (Let's Encrypt, CloudFlare, etc.)
- [ ] **SSL Auto-renewal**: ☐ Configured ☐ Manual
- [ ] **SSL Status**: ☐ Active ☐ Needs Setup

### 🗄️ Database Information

#### Database Connection
- [ ] **Database Type**: `_________________` (MySQL, PostgreSQL, MariaDB)
- [ ] **Database Host**: `_________________`
- [ ] **Database Port**: `________` (default: 3306 for MySQL)
- [ ] **Database Name**: `_________________`
- [ ] **Database Username**: `_________________`
- [ ] **Database Password**: `_________________` (store securely)

#### Database Management
- [ ] **Admin Access**: ☐ phpMyAdmin ☐ Adminer ☐ Command Line ☐ Other: `_______`
- [ ] **Backup Strategy**: ☐ Automated ☐ Manual ☐ Provider Managed
- [ ] **Migration Access**: ☐ Direct ☐ Through Application ☐ Manual SQL

### 📁 Server Directory Structure

#### Web Root Configuration
- [ ] **Document Root**: `_________________` (e.g., `/var/www/html`, `~/public_html`)
- [ ] **Project Directory**: `_________________` (e.g., `~/domains/yoursite.com`)
- [ ] **Shared Storage**: `_________________` (for uploads, logs, cache)
- [ ] **Release Management**: ☐ Atomic Deploys ☐ Direct Deploy ☐ Manual

#### File Permissions
- [ ] **Web User**: `_________________` (www-data, apache, nginx)
- [ ] **File Owner**: `_________________`
- [ ] **Group**: `_________________`
- [ ] **Permission Requirements**: Documented ☐ Yes ☐ No

### 🔑 Authentication & Security

#### Application Secrets
- [ ] **App Key**: Generated and secured
- [ ] **JWT Secret** (if applicable): `_________________`
- [ ] **Encryption Keys**: Documented
- [ ] **API Keys**: Documented and secured

#### Third-Party Services
- [ ] **Email Service**: `_________________` (SMTP, SendGrid, Mailgun)
- [ ] **Payment Gateway**: `_________________` (Stripe, PayPal)
- [ ] **Storage Service**: `_________________` (AWS S3, CloudFlare R2)
- [ ] **CDN Service**: `_________________` (CloudFlare, AWS CloudFront)

### ⚙️ Scenario-Specific Requirements

#### Local Build + SSH (Scenario A)
- [ ] **SSH Client Configured**: ☐ OpenSSH ☐ PuTTY ☐ Other: `_______`
- [ ] **SCP/Rsync Access**: Tested and working
- [ ] **Build Tools Locally**: ☐ Composer ☐ NPM ☐ PHP ☐ Node.js
- [ ] **Deployment Scripts**: ☐ Ready ☐ Need Creation

#### GitHub Actions (Scenario B)
- [ ] **GitHub Repository**: Created and accessible
- [ ] **Actions Enabled**: ☐ Yes ☐ No
- [ ] **Secrets Configured**: ☐ All Set ☐ Pending
- [ ] **Workflow Files**: ☐ Ready ☐ Need Creation
- [ ] **Branch Strategy**: `_________________` (main/staging/production)

#### DeployHQ (Scenario C)
- [ ] **DeployHQ Account**: ☐ Active ☐ Need Setup
- [ ] **Repository Connected**: ☐ GitHub ☐ GitLab ☐ Bitbucket
- [ ] **Build Configuration**: ☐ Configured ☐ Pending
- [ ] **Deployment Targets**: ☐ Configured ☐ Pending

#### Git Pull + Manual (Scenario D)
- [ ] **Git on Server**: ☐ Installed ☐ Needs Installation
- [ ] **Repository Access**: ☐ SSH ☐ HTTPS ☐ Deploy Key
- [ ] **Build Tools on Server**: ☐ Composer ☐ NPM ☐ Available
- [ ] **Manual Process**: ☐ Documented ☐ Needs Documentation

### 🎯 Environment Configuration

#### Environment Files
- [ ] **Production .env**: ☐ Ready ☐ Needs Creation
- [ ] **Staging .env**: ☐ Ready ☐ Needs Creation
- [ ] **Development .env**: ☐ Ready ☐ Needs Creation

#### Environment Variables Template
```bash
# Application
APP_NAME=Your_App_Name
APP_ENV=production
APP_KEY=base64:YOUR_32_CHARACTER_SECRET_KEY
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your_mail_host
MAIL_PORT=587
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls

# Add your specific variables here
```

### 📋 Deployment Readiness Verification

#### Pre-Deployment Checks
- [ ] **All Information Collected**: Complete and verified
- [ ] **Access Tested**: SSH, Database, Repository
- [ ] **Permissions Verified**: File, directory, database access
- [ ] **Backup Strategy**: Confirmed for rollback capability
- [ ] **Monitoring Setup**: ☐ Ready ☐ Pending ☐ Not Required

#### Emergency Contacts & Procedures
- [ ] **Server Provider Support**: Contact info documented
- [ ] **Domain Provider Support**: Contact info documented  
- [ ] **Database Admin Contact**: `_________________`
- [ ] **Rollback Procedure**: ☐ Documented ☐ Tested ☐ Ready

## Next Steps

Once all information is collected and verified:

1. **Choose Your Deployment Scenario**:
   - Scenario A: Local Build + SSH (Manual, High Control)
   - Scenario B: GitHub Actions (Automated, CI/CD)
   - Scenario C: DeployHQ (Professional Pipeline)
   - Scenario D: Git Pull + Manual (Simple, Traditional)

2. **Proceed to Scenario-Specific Steps**:
   - Each scenario has detailed implementation guides
   - Use collected information to fill templates
   - Follow step-by-step instructions for your chosen method

3. **Keep Information Secure**:
   - Store credentials in password manager
   - Never commit secrets to version control
   - Use environment variables for sensitive data
   - Regularly rotate access keys and passwords

## Security Best Practices

- **Use Strong Passwords**: Minimum 12 characters, mixed case, numbers, symbols
- **Enable 2FA**: Wherever possible (GitHub, hosting provider, domain registrar)
- **Limit Access**: Only necessary team members should have production access
- **Regular Audits**: Review and update credentials quarterly
- **Backup Access**: Ensure multiple team members can access critical systems
- **Document Everything**: But keep sensitive information secure

---

**Note**: This information collection step is crucial for successful deployment. Take time to gather accurate details and verify access before proceeding with your chosen deployment scenario.