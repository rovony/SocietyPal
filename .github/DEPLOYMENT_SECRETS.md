# 🔐 GitHub Secrets Configuration for SocietyPal

This document provides step-by-step instructions for configuring GitHub repository secrets required for the SocietyPal deployment pipeline.

## 📋 Overview

The GitHub Actions deployment workflow requires secure access to:

-   **Server Connection**: SSH access to Hostinger server
-   **Database Credentials**: Both staging and production databases
-   **Environment Configuration**: Server paths and connection details

---

## 🚀 Quick Setup Guide

### 1. Navigate to Repository Secrets

1. Go to your GitHub repository
2. Click **Settings** tab
3. Navigate to **Secrets and variables** → **Actions**
4. Click **New repository secret** for each secret below

### 2. Required Secrets Configuration

#### 🔌 Server Connection Secrets

| Secret Name      | Value                   | Description                                 |
| ---------------- | ----------------------- | ------------------------------------------- |
| `SERVER_HOST`    | `31.97.195.108`         | Hostinger server IP address                 |
| `SERVER_USER`    | `u227177893`            | SSH username for server access              |
| `SERVER_PORT`    | `65002`                 | SSH port for server connection              |
| `SERVER_SSH_KEY` | `[PRIVATE_KEY_CONTENT]` | Complete SSH private key (see format below) |

#### 🗄️ Production Database Secrets

| Secret Name        | Value                         | Description                  |
| ------------------ | ----------------------------- | ---------------------------- |
| `DB_HOST_PROD`     | `127.0.0.1`                   | Production database host     |
| `DB_PORT_PROD`     | `3306`                        | Production database port     |
| `DB_DATABASE_PROD` | `u227177893_p_zaj_socpal_d`   | Production database name     |
| `DB_USERNAME_PROD` | `u227177893_p_zaj_socpal_u`   | Production database username |
| `DB_PASSWORD_PROD` | `t5TmP9$[iG7hu2eYRWUIWH@IRF2` | Production database password |

#### 🧪 Staging Database Secrets

| Secret Name           | Value                       | Description               |
| --------------------- | --------------------------- | ------------------------- |
| `DB_HOST_STAGING`     | `127.0.0.1`                 | Staging database host     |
| `DB_PORT_STAGING`     | `3306`                      | Staging database port     |
| `DB_DATABASE_STAGING` | `u227177893_s_zaj_socpal_d` | Staging database name     |
| `DB_USERNAME_STAGING` | `u227177893_s_zaj_socpal_u` | Staging database username |
| `DB_PASSWORD_STAGING` | `V0Z^G=I2:=r^f2`            | Staging database password |

---

## 🔑 SSH Key Setup

### Generate SSH Key Pair (if not already created)

```bash
# Generate new SSH key pair
ssh-keygen -t ed25519 -C "github-actions-societypal" -f ~/.ssh/societypal_deploy

# Or use RSA if ed25519 is not supported
ssh-keygen -t rsa -b 4096 -C "github-actions-societypal" -f ~/.ssh/societypal_deploy
```

### Add Public Key to Server

1. **Copy the public key:**

    ```bash
    cat ~/.ssh/societypal_deploy.pub
    ```

2. **Add to server's authorized_keys:**

    ```bash
    # Connect to server
    ssh -p 65002 u227177893@31.97.195.108

    # Add the public key to authorized_keys
    echo "your-public-key-content-here" >> ~/.ssh/authorized_keys

    # Set proper permissions
    chmod 600 ~/.ssh/authorized_keys
    chmod 700 ~/.ssh
    ```

### Add Private Key to GitHub Secrets

1. **Copy the complete private key:**

    ```bash
    cat ~/.ssh/societypal_deploy
    ```

2. **Add to GitHub as `SERVER_SSH_KEY` secret:**
    - The key should include the header and footer:
    ```
    -----BEGIN OPENSSH PRIVATE KEY-----
    [key content here]
    -----END OPENSSH PRIVATE KEY-----
    ```

---

## ✅ Verification Steps

### 1. Test SSH Connection

```bash
# Test SSH connection with the private key
ssh -i ~/.ssh/societypal_deploy -p 65002 u227177893@31.97.195.108
```

### 2. Test Database Connections

#### Production Database Test:

```bash
mysql -h 127.0.0.1 -P 3306 -u u227177893_p_zaj_socpal_u -p u227177893_p_zaj_socpal_d
# Enter password: t5TmP9$[iG7hu2eYRWUIWH@IRF2
```

#### Staging Database Test:

```bash
mysql -h 127.0.0.1 -P 3306 -u u227177893_s_zaj_socpal_u -p u227177893_s_zaj_socpal_d
# Enter password: V0Z^G=I2:=r^f2
```

### 3. Verify Directory Structure on Server

```bash
# Check if required directories exist or will be created
ssh -p 65002 u227177893@31.97.195.108 "
    ls -la /home/u227177893/domains/
    mkdir -p /home/u227177893/domains/societypal.com/releases
    mkdir -p /home/u227177893/domains/staging.societypal.com/releases
"
```

---

## 🎯 Deployment Triggers

### Automatic Deployments

-   **Staging**: Triggered on push to `main` or `staging` branches
-   **Production**: Triggered on push to `production` branch

### Manual Deployments

1. Go to **Actions** tab in your repository
2. Select **SocietyPal Deployment Pipeline**
3. Click **Run workflow**
4. Choose environment (staging/production)
5. Click **Run workflow**

---

## 🛡️ Security Best Practices

### 1. SSH Key Security

-   ✅ Use dedicated SSH keys for deployment
-   ✅ Rotate keys regularly (every 6 months)
-   ✅ Never commit private keys to repository
-   ✅ Use GitHub's encrypted secrets storage

### 2. Database Security

-   ✅ Use strong, unique passwords
-   ✅ Limit database user permissions to required databases only
-   ✅ Regular password rotation
-   ✅ Monitor database access logs

### 3. Access Control

-   ✅ Limit repository access to authorized team members
-   ✅ Use branch protection rules
-   ✅ Require pull request reviews for production deployments
-   ✅ Enable two-factor authentication on GitHub accounts

---

## 🔧 Troubleshooting

### Common Issues

#### SSH Connection Failed

```bash
# Check SSH key format
head -n 1 ~/.ssh/societypal_deploy
# Should show: -----BEGIN OPENSSH PRIVATE KEY-----

# Test SSH connection with verbose output
ssh -vvv -i ~/.ssh/societypal_deploy -p 65002 u227177893@31.97.195.108
```

#### Database Connection Failed

```bash
# Test database connectivity from server
ssh -p 65002 u227177893@31.97.195.108 "
    mysql -h 127.0.0.1 -P 3306 -u u227177893_s_zaj_socpal_u -p'V0Z^G=I2:=r^f2' -e 'SELECT 1;'
"
```

#### Deployment Permission Issues

```bash
# Check and fix permissions on server
ssh -p 65002 u227177893@31.97.195.108 "
    find /home/u227177893/domains -type d -exec chmod 755 {} \;
    find /home/u227177893/domains -name 'public_html' -exec chmod 755 {} \;
"
```

---

## 📞 Support & Contacts

### Getting Help

-   **GitHub Actions Logs**: Check workflow run details for error messages
-   **Server Logs**: SSH to server and check deployment logs
-   **Rollback**: Use emergency rollback script if needed

### Emergency Rollback

```bash
# Download and run rollback script
curl -O https://raw.githubusercontent.com/[username]/[repo]/main/.github/scripts/rollback.sh
chmod +x rollback.sh
./rollback.sh production --rollback
```

---

## ✅ Completion Checklist

Before triggering your first deployment, ensure:

-   [ ] All 11 GitHub secrets configured correctly
-   [ ] SSH key pair generated and configured
-   [ ] Public key added to server's authorized_keys
-   [ ] Private key added to GitHub secrets
-   [ ] Database connections tested successfully
-   [ ] Server directory structure verified
-   [ ] Branch protection rules configured (optional but recommended)
-   [ ] Team members have appropriate repository access

---

## 🎉 Ready to Deploy!

Once all secrets are configured and verified, your deployment pipeline is ready to use. Push to your designated branches or manually trigger deployments through the GitHub Actions interface.

**Happy Deploying! 🚀**
