# GitHub Actions Deployment Files

This directory contains all necessary templates and scripts for setting up automated GitHub Actions deployment for your Laravel application.

## 📁 File Overview

### Core Templates
- **`deploy.yml.template`** - Main GitHub Actions workflow file
- **`secrets.env.template`** - Required secrets configuration guide
- **`deploy-server-script.sh.template`** - Server-side deployment script

### Documentation
- **`README.md`** - This guide
- **`setup-instructions.md`** - Detailed setup instructions

## 🚀 Quick Setup Guide

### 1. Prepare GitHub Repository
1. Ensure your project is in a GitHub repository
2. Enable GitHub Actions in repository settings
3. Create necessary branches: `main`, `staging` (optional), `production` (optional)

### 2. Configure Repository Secrets
1. Go to **Repository Settings > Secrets and Variables > Actions**
2. Use `secrets.env.template` as your guide
3. Add each secret with exact names (case-sensitive)

**Required Secrets:**
```
SERVER_HOST=your-server.com
SERVER_USER=your-username  
SERVER_PORT=22
SERVER_SSH_KEY=your-private-key-content
DB_HOST=localhost
DB_DATABASE=your_database
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
APP_KEY=your-laravel-app-key
```

### 3. Setup Server
1. Copy `deploy-server-script.sh.template` to your server
2. Customize variables in the script header
3. Make it executable: `chmod +x deploy-server-script.sh`
4. Test SSH access from GitHub Actions

### 4. Configure Workflow
1. Copy `deploy.yml.template` to `.github/workflows/deploy.yml`
2. Replace all `{{VARIABLES}}` with your actual values:
   - `{{PROJECT_NAME}}` → Your project name
   - `{{DOMAIN}}` → Your domain (e.g., mysite.com)
   - `{{PHP_VERSION}}` → Your PHP version (e.g., 8.2)
   - `{{NODE_VERSION}}` → Your Node.js version (e.g., 18)

### 5. Test Deployment
1. Push to your configured branch (main/staging/production)
2. Monitor the workflow in **Actions** tab
3. Verify deployment at your domain

## 🔧 Configuration Variables

### Server Configuration
```yaml
PROJECT_NAME: "MyLaravelApp"
DOMAIN: "mysite.com"
PHP_VERSION: "8.2"
NODE_VERSION: "18"
```

### Directory Structure on Server
```
~/domains/mysite.com/
├── current/           # Symlink to active release
├── releases/          # All deployment releases
│   ├── 20240101-120000/
│   ├── 20240102-130000/
│   └── 20240103-140000/
├── shared/            # Persistent data
│   ├── .env
│   ├── storage/
│   └── public/uploads/
└── public_html        # Web server document root
```

## 🎯 Deployment Scenarios

### Automatic Deployment
- **Trigger**: Push to `main`, `staging`, or `production` branches
- **Target**: Based on branch name
- **Environment**: Automatically determined

### Manual Deployment
- **Trigger**: Manual workflow dispatch
- **Target**: Choose environment
- **Options**: Force fresh install, custom parameters

## 🔍 Monitoring & Health Checks

The workflow includes automatic health checks:
- ✅ Site availability (HTTP 200 response)
- ✅ Response time monitoring  
- ✅ Laravel-specific checks (CSRF token detection)
- ✅ Automatic rollback on failure

## 🆘 Troubleshooting

### Common Issues

#### SSH Connection Failed
```bash
# Test SSH connection manually
ssh -p 22 -i ~/.ssh/your_key user@your-server.com

# Check SSH key format in secrets (include full key with headers)
-----BEGIN OPENSSH PRIVATE KEY-----
your-key-content-here
-----END OPENSSH PRIVATE KEY-----
```

#### Database Migration Errors
- Check database credentials in secrets
- Verify database permissions
- Review migration files for destructive operations

#### Build Failures
- Verify PHP/Node versions match server
- Check composer.json and package.json syntax
- Ensure all dependencies are available

#### Deployment Timeouts
- Increase workflow timeout in deploy.yml
- Check server resources (disk space, memory)
- Optimize build process (cache dependencies)

### Debug Mode
Enable debug logging by adding to workflow:
```yaml
env:
  ACTIONS_STEP_DEBUG: true
  ACTIONS_RUNNER_DEBUG: true
```

## 🛡️ Security Best Practices

### Secret Management
- ✅ Use GitHub Secrets for all sensitive data
- ✅ Rotate SSH keys regularly
- ✅ Use environment-specific secrets
- ✅ Never commit secrets to repository

### Server Security  
- ✅ Use SSH keys instead of passwords
- ✅ Limit SSH access to necessary IPs
- ✅ Regular security updates
- ✅ Monitor access logs

### Application Security
- ✅ Use strong APP_KEY
- ✅ Keep dependencies updated
- ✅ Regular security audits
- ✅ Backup before deployments

## 📚 Advanced Features

### Multi-Environment Support
```yaml
# Staging environment
staging.mysite.com → staging branch
  
# Production environment  
mysite.com → production branch
```

### Rollback Capability
- Automatic rollback on health check failure
- Manual rollback via server script
- Maintains last 3 releases for quick recovery

### Smart Database Handling
- Fresh install detection
- Migration safety checks
- CodeCanyon compatibility
- Backup verification

## 📞 Support & Maintenance

### Regular Tasks
- [ ] Monitor deployment logs weekly
- [ ] Update dependencies monthly
- [ ] Rotate secrets quarterly
- [ ] Review access permissions quarterly

### Maintenance Windows
- Schedule deployments during low-traffic periods
- Test staging environment before production
- Keep rollback procedures documented and tested

---

**Need Help?** Check the detailed setup instructions in `setup-instructions.md` or review the deployment execution checklist in the Phase 3 guides.