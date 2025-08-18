# 🚀 SocietyPal GitHub Actions CI/CD System

This directory contains the complete GitHub Actions CI/CD implementation for the SocietyPal Laravel application, providing automated deployments to staging and production environments with zero-downtime capabilities.

## 📁 Directory Structure

```
.github/
├── README.md                      # This file - Overview and usage
├── DEPLOYMENT_SECRETS.md          # Step-by-step secrets configuration guide
├── workflows/
│   ├── deploy.yml                 # Main automated deployment workflow
│   └── manual-deploy.yml          # Manual/emergency deployment workflow
└── scripts/
    ├── check-migrations.sh        # Database migration safety validator
    ├── rollback.sh                # Emergency rollback utility
    └── validate-setup.sh          # Complete setup validation script
```

## 🎯 Quick Start

### 1. Validate Your Setup

Run the validation script to ensure everything is configured correctly:

```bash
chmod +x .github/scripts/validate-setup.sh
./.github/scripts/validate-setup.sh
```

### 2. Configure GitHub Secrets

Follow the comprehensive guide in [`DEPLOYMENT_SECRETS.md`](./DEPLOYMENT_SECRETS.md) to configure all required repository secrets.

### 3. Deploy!

-   **Automatic**: Push to `main`/`staging` (staging) or `production` (production) branches
-   **Manual**: Go to Actions tab → Run workflow

---

## 🚀 Deployment Workflows

### Primary Deployment (`deploy.yml`)

**Triggers:**

-   🔄 **Automatic**: Push to `main`, `staging`, or `production` branches
-   🎮 **Manual**: GitHub Actions "Run workflow" button

**Features:**

-   ✅ Zero-downtime deployments with atomic symlink switching
-   ✅ Multi-environment support (staging/production)
-   ✅ Database migration safety checks with rollback
-   ✅ Frontend asset building with Vite optimization
-   ✅ Laravel caching and optimization
-   ✅ Comprehensive health checks
-   ✅ Automatic rollback on failure
-   ✅ Detailed deployment reporting

### Emergency Deployment (`manual-deploy.yml`)

**Purpose:** Manual deployments and emergency rollbacks

**Features:**

-   🚨 **Emergency Rollback**: Instant rollback to previous release
-   🎯 **Targeted Deployment**: Deploy specific release versions
-   ⚡ **Force Deploy**: Bypass migration safety checks
-   🔐 **Strict Mode**: Enhanced migration validation
-   🎮 **Full Control**: Manual parameter selection

---

## 🛠️ Utility Scripts

### Migration Safety Check (`check-migrations.sh`)

Analyzes Laravel migrations for destructive operations before deployment.

**Usage:**

```bash
# Run migration safety check
./.github/scripts/check-migrations.sh

# Run with strict mode (blocks destructive operations)
MIGRATION_STRICT_MODE=true ./.github/scripts/check-migrations.sh
```

**Features:**

-   🔍 Detects destructive operations (drops, deletes, renames)
-   ⚠️ Identifies potentially risky changes
-   📊 Provides detailed safety reports
-   🛑 Optional strict mode for production safety

### Emergency Rollback (`rollback.sh`)

Standalone emergency rollback utility for critical situations.

**Usage:**

```bash
# List available releases
./.github/scripts/rollback.sh staging --list

# Show current active release
./.github/scripts/rollback.sh production --current

# Rollback to previous release
./.github/scripts/rollback.sh production --rollback

# Rollback to specific release
./.github/scripts/rollback.sh staging --rollback 20250118-143020

# Health check current deployment
./.github/scripts/rollback.sh staging --health-check
```

### Setup Validation (`validate-setup.sh`)

Comprehensive validation of the entire CI/CD setup.

**Usage:**

```bash
# Run complete setup validation
./.github/scripts/validate-setup.sh
```

**Validates:**

-   ✅ Directory structure and file existence
-   ✅ Script permissions and executability
-   ✅ Laravel project structure
-   ✅ Configuration files
-   ✅ Workflow configurations
-   ✅ Migration safety
-   ✅ Required secrets checklist

---

## 🌐 Environment Configuration

### Staging Environment

-   **Branch**: `main` or `staging`
-   **URL**: https://staging.societypal.com
-   **Database**: `u227177893_s_zaj_socpal_d`
-   **Auto-deploy**: ✅ Enabled

### Production Environment

-   **Branch**: `production`
-   **URL**: https://societypal.com
-   **Database**: `u227177893_p_zaj_socpal_d`
-   **Auto-deploy**: ✅ Enabled

---

## 🔐 Security Features

### SSH Security

-   🔑 Dedicated SSH keys for deployment
-   🔐 Encrypted storage in GitHub secrets
-   🛡️ Server-side authorized_keys validation
-   📝 Connection logging and monitoring

### Database Security

-   🔒 Encrypted credential storage
-   🎯 Limited user permissions per environment
-   🔄 Migration safety validation
-   💾 Automatic backup recommendations

### Access Control

-   👥 Repository access control
-   🌿 Branch protection (recommended)
-   📊 Deployment audit logging
-   🔍 Action-level permission management

---

## 📊 Monitoring & Health Checks

### Automatic Health Checks

-   ✅ **HTTP Response**: 200 status verification
-   ✅ **Application Bootstrap**: Laravel framework check
-   ✅ **Database Connectivity**: Connection validation
-   ✅ **Asset Accessibility**: CSS/JS availability check
-   ✅ **Performance**: Response time monitoring

### Rollback Triggers

-   ❌ **HTTP Failure**: Non-200 response codes
-   ❌ **Database Issues**: Connection or migration failures
-   ❌ **Asset Problems**: Missing or corrupted assets
-   ❌ **Framework Errors**: Laravel bootstrap failures

---

## 🚨 Emergency Procedures

### If Deployment Fails

1. **Automatic**: System will auto-rollback
2. **Manual Check**: Verify at domain URL
3. **Emergency Rollback**: Use `rollback.sh` script
4. **Investigation**: Check Actions logs for details

### If Manual Intervention Needed

```bash
# Emergency rollback via script
./.github/scripts/rollback.sh production --rollback

# Or via GitHub Actions
# Go to Actions → Manual Emergency Deployment → Run workflow
# Select "Rollback Mode" → Choose environment → Run
```

### Critical Issues

1. **SSH Access Lost**: Update SSH keys in secrets
2. **Database Corruption**: Restore from backup
3. **Server Issues**: Contact hosting provider
4. **Mass Rollback**: Multiple environment rollback available

---

## 🔧 Customization

### Adding New Environments

1. Update workflow environment mapping
2. Add new database secrets
3. Configure server paths
4. Test deployment pipeline

### Modifying Migration Checks

Edit `check-migrations.sh`:

-   Add new destructive patterns
-   Modify warning thresholds
-   Customize safety levels
-   Add environment-specific rules

### Extending Health Checks

Modify workflow health check sections:

-   Add application-specific checks
-   Include external service validation
-   Implement custom monitoring
-   Add performance benchmarks

---

## 📚 Documentation

### Primary Documentation

-   **[Implementation Plan](../Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-3-Deployment-Execution/1-Steps/Step-22-24-Deploy/B-GitHub-Actions/SocietyPal-GitHub-Actions-Implementation-Plan.md)**: Complete architectural documentation
-   **[Deployment Secrets](./DEPLOYMENT_SECRETS.md)**: Secrets configuration guide

### Additional Resources

-   **GitHub Actions**: [Official Documentation](https://docs.github.com/en/actions)
-   **Laravel Deployment**: [Official Guide](https://laravel.com/docs/deployment)
-   **Zero-Downtime**: [Best Practices](https://laravel.com/docs/deployment#zero-downtime-deployment)

---

## ✅ Success Criteria

Your CI/CD system is working correctly when:

-   [ ] All validation checks pass
-   [ ] GitHub secrets are configured
-   [ ] SSH keys are properly set up
-   [ ] Staging deployments work automatically
-   [ ] Production deployments work automatically
-   [ ] Health checks pass consistently
-   [ ] Rollback procedures work when tested
-   [ ] Migration safety checks function properly

---

## 🎉 You're Ready!

Your SocietyPal application now has enterprise-grade CI/CD with:

🚀 **Zero-downtime deployments**  
🔄 **Automatic rollbacks**  
🛡️ **Migration safety**  
🌐 **Multi-environment support**  
📊 **Comprehensive monitoring**  
🚨 **Emergency procedures**

**Happy Deploying! 🎯**
