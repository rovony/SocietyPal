# DigitalOcean Droplet Hosting Setup Guide

## SSH Connection Details

### Step 1: Create SSH Key Pair
**Option 1: Generate New SSH Key**
```bash
# Generate SSH key pair
ssh-keygen -t rsa -b 4096 -f ~/.ssh/digitalocean_key
# Press Enter for default location and optionally set passphrase

# Display public key
cat ~/.ssh/digitalocean_key.pub
```

**Option 2: Use Existing SSH Key**
```bash
# If you already have a key
cat ~/.ssh/id_rsa.pub
```

### Step 2: Add SSH Key to DigitalOcean Account
1. **Access DigitalOcean Control Panel**
2. **Navigate to SSH Keys**:
   - Click **Settings** → **Security** → **SSH Keys**
   - Or go to **Create** → **Droplets** → **SSH keys** section
3. **Add SSH Key**:
   - Click **New SSH Key**
   - Paste your public key content
   - Give it a descriptive name
   - Click **Add SSH Key**

### Step 3: Create Droplet with SSH Key
1. **Create New Droplet**: Click **Create** → **Droplets**
2. **Choose Image**: Ubuntu, CentOS, Debian, etc.
3. **Select Size**: Basic, Premium, or CPU-optimized
4. **Choose Region**: Select closest to your users
5. **Authentication**: Select your SSH key (recommended over password)
6. **Finalize Details**: Name your Droplet and click **Create**

### Step 4: Connect via SSH
**Connection Details**:
- **Server Host/IP**: Droplet's public IP address
- **SSH Username**: `root` (default) or created user
- **SSH Port**: `22` (default)
- **SSH Private Key**: Your private key file

**SSH Connection**:
```bash
# Connect with SSH key
ssh -i ~/.ssh/digitalocean_key root@your_droplet_ip

# If using default key location
ssh root@your_droplet_ip

# Example
ssh root@164.90.163.45
```

**Using PuTTY (Windows)**:
- Convert private key to `.ppk` format using PuTTYgen
- Host Name: `root@droplet_ip`
- Port: `22`
- SSH → Auth → Private key file: Select converted `.ppk` file

### Step 5: Create Non-Root User (Recommended)
```bash
# Create new user
adduser newuser

# Add to sudo group
usermod -aG sudo newuser

# Setup SSH access for new user
mkdir -p /home/newuser/.ssh
cp /root/.ssh/authorized_keys /home/newuser/.ssh/
chown -R newuser:newuser /home/newuser/.ssh
chmod 700 /home/newuser/.ssh
chmod 600 /home/newuser/.ssh/authorized_keys

# Test connection
ssh -i ~/.ssh/digitalocean_key newuser@your_droplet_ip
```

---

## Server Hosting Details

### DigitalOcean Droplet Options
| Droplet Type | vCPUs | Memory | SSD | Transfer | Price Range |
|--------------|-------|--------|-----|----------|-------------|
| **Basic** | 1-8 | 1-16GB | 25-320GB | 1-5TB | $4-$96/month |
| **Premium** | 2-32 | 4-192GB | 50-3.84TB | 4-12TB | $24-$1,152/month |
| **CPU-Optimized** | 2-32 | 4-64GB | 25-400GB | 4-9TB | $40-$640/month |

### Server Specifications
- **Hosting Provider**: DigitalOcean
- **Server OS**: Ubuntu 22.04/20.04, CentOS 7/8, Debian 11/10, Fedora
- **Web Server**: Install Apache, Nginx, or other
- **PHP Version**: Install via package manager
- **Node.js Version**: Install via package manager or NodeSource

### Pre-installed Images Available
- **One-Click Apps**: WordPress, Docker, LAMP, LEMP, Node.js
- **Marketplace Images**: Pre-configured application stacks
- **Custom Images**: Upload your own images

---

## Domain & URL Configuration

### Domain Management Options
**DigitalOcean DNS** (Free):
1. **Add Domain**: Networking → Domains → Add Domain
2. **DNS Records**: Create A, CNAME, MX records
3. **Point Nameservers**: Update domain registrar to use:
   - `ns1.digitalocean.com`
   - `ns2.digitalocean.com`
   - `ns3.digitalocean.com`

**External DNS Provider**:
- Point A record to Droplet's public IP
- Configure subdomain records as needed

### Floating IP (Static IP)
**Why Use Floating IP**:
- Static IP address that survives Droplet destruction
- Easy failover between Droplets
- $4/month when not assigned to Droplet (free when assigned)

**Setup Steps**:
1. **Create Floating IP**: Networking → Floating IPs → Assign Floating IP
2. **Select Droplet**: Choose target Droplet
3. **Update DNS**: Point domain to Floating IP instead of Droplet IP

### SSL Certificate Setup
**Let's Encrypt (Free)**:
```bash
# Install Certbot (Ubuntu)
sudo apt update
sudo apt install snapd
sudo snap install core; sudo snap refresh core
sudo snap install --classic certbot
sudo ln -s /snap/bin/certbot /usr/bin/certbot

# Get certificate for Apache
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com

# Get certificate for Nginx
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal test
sudo certbot renew --dry-run
```

---

## Database Information

### Database Options on DigitalOcean

**Managed Databases** (Recommended):
| Database Type | Features | Starting Price |
|---------------|----------|----------------|
| **MySQL** | 8.0, High availability | $15/month |
| **PostgreSQL** | 13+, Advanced features | $15/month |
| **Redis** | In-memory cache/store | $15/month |
| **MongoDB** | Document database | $15/month |

**Self-Managed on Droplet**:
- Install database directly on Droplet
- More control, but requires maintenance
- Better for development/testing

### Managed Database Setup
1. **Create Database**: Databases → Create Database Cluster
2. **Choose Engine**: MySQL, PostgreSQL, Redis, or MongoDB
3. **Select Configuration**: Size, region, VPC
4. **Connection Details**: Provided after creation
   - **Database Host**: Managed database endpoint
   - **Database Port**: Standard ports (3306, 5432, etc.)
   - **Database Name**: Created during setup
   - **Username/Password**: Set during creation

### Self-Managed Database Installation
**MySQL Setup (Ubuntu)**:
```bash
# Install MySQL
sudo apt update
sudo apt install mysql-server

# Secure installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
CREATE DATABASE myapp_production;
CREATE USER 'myapp_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON myapp_production.* TO 'myapp_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**PostgreSQL Setup (Ubuntu)**:
```bash
# Install PostgreSQL
sudo apt update
sudo apt install postgresql postgresql-contrib

# Create database and user
sudo -u postgres psql
CREATE DATABASE myapp_production;
CREATE USER myapp_user WITH ENCRYPTED PASSWORD 'secure_password';
GRANT ALL PRIVILEGES ON DATABASE myapp_production TO myapp_user;
\q
```

---

## Server Directory Structure

### Standard Ubuntu Directory Structure
```
/
├── home/
│   └── username/              (User home directory)
│       ├── applications/      (Custom applications)
│       ├── backups/          (Local backups)
│       └── .ssh/             (SSH keys)
├── var/
│   └── www/
│       └── html/             (Web server document root)
├── opt/                      (Optional software installations)
├── etc/                      (System configuration files)
└── srv/                      (Service data)
```

### Web Server Document Root
**Apache Default**:
```
/var/www/html/                (Default document root)
/var/www/yourdomain.com/      (Virtual host setup)
```

**Nginx Default**:
```
/var/www/html/                (Default document root)
/usr/share/nginx/html/        (Alternative location)
```

### Recommended Project Structure
```
/var/www/
├── yourdomain.com/
│   ├── public/               (Document root - web accessible)
│   ├── app/                  (Application code)
│   ├── config/               (Configuration files)
│   ├── logs/                 (Application logs)
│   └── backups/              (Local backups)
└── staging.yourdomain.com/   (Staging environment)
```

### File Permissions & Ownership
```bash
# Set proper ownership
sudo chown -R $USER:www-data /var/www/yourdomain.com

# Set directory permissions
find /var/www/yourdomain.com -type d -exec chmod 755 {} \;

# Set file permissions
find /var/www/yourdomain.com -type f -exec chmod 644 {} \;

# Make specific files executable (if needed)
chmod +x /var/www/yourdomain.com/scripts/deploy.sh
```

---

## Security & Authentication

### Droplet Security Hardening
**Update System**:
```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# Enable automatic security updates
sudo apt install unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades
```

**Configure Firewall (UFW)**:
```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow ssh

# Allow HTTP/HTTPS
sudo ufw allow 'Nginx Full'  # or 'Apache Full'

# Check status
sudo ufw status
```

**Fail2Ban Setup**:
```bash
# Install Fail2Ban
sudo apt install fail2ban

# Create local configuration
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Edit configuration
sudo nano /etc/fail2ban/jail.local

# Enable and start
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### SSH Security Configuration
```bash
# Edit SSH config
sudo nano /etc/ssh/sshd_config

# Recommended security settings:
Port 2222                           # Change default port
PermitRootLogin no                  # Disable root login
PasswordAuthentication no           # Key-based auth only
PubkeyAuthentication yes            # Enable public key auth
X11Forwarding no                    # Disable X11 forwarding
MaxAuthTries 3                      # Limit login attempts
ClientAliveInterval 300             # Client timeout
ClientAliveCountMax 2               # Max timeouts

# Restart SSH service
sudo systemctl restart sshd
```

### Environment Variables & Secrets
```bash
# Create environment file
sudo nano /var/www/yourdomain.com/.env

# Example configuration:
DB_HOST=managed-db-endpoint.db.ondigitalocean.com
DB_PORT=25060
DB_DATABASE=myapp_production
DB_USERNAME=doadmin
DB_PASSWORD=secure_managed_db_password

APP_URL=https://yourdomain.com
APP_ENV=production
APP_KEY=base64:generated_app_key_here

# Set proper permissions
sudo chmod 600 /var/www/yourdomain.com/.env
sudo chown www-data:www-data /var/www/yourdomain.com/.env
```

---

## DigitalOcean Features & Services

### Monitoring & Alerting
**Built-in Monitoring**:
- CPU, Memory, Disk, and Bandwidth graphs
- Available in Droplet dashboard
- Historical data up to 2 years

**Enhanced Monitoring** ($2/month per Droplet):
- Advanced metrics and alerting
- Custom dashboards
- Longer data retention

### Backups & Snapshots
**Automated Backups** (20% of Droplet cost):
- Weekly automated backups
- Retain 4 most recent backups
- Easy restoration process

**Snapshots** (Usage-based pricing):
- On-demand full Droplet images
- Create before major changes
- Use for scaling or migration

**Creating Backups/Snapshots**:
```bash
# Via DigitalOcean CLI (doctl)
doctl compute droplet snapshot create droplet-id --snapshot-name "pre-deployment-$(date +%Y%m%d)"

# Via API or Control Panel
# Droplet → Snapshots → Take Snapshot
```

### Load Balancers & Scaling
**Load Balancer Setup**:
1. **Create Load Balancer**: Networking → Load Balancers
2. **Configure Health Checks**: HTTP/HTTPS endpoint monitoring
3. **Add Droplets**: Select backend Droplets
4. **SSL Termination**: Upload certificates or use Let's Encrypt

**Horizontal Scaling**:
- Create multiple identical Droplets
- Use Load Balancer to distribute traffic
- Shared database or file storage

---

## Deployment Strategies

### Manual Deployment
**File Transfer**:
```bash
# Upload files via SCP
scp -r ./myapp/ username@droplet_ip:/var/www/yourdomain.com/

# Sync with rsync
rsync -avz --delete ./myapp/ username@droplet_ip:/var/www/yourdomain.com/

# Set permissions after upload
ssh username@droplet_ip "sudo chown -R www-data:www-data /var/www/yourdomain.com/"
```

### Git-Based Deployment
**Simple Git Deployment**:
```bash
# Clone repository on server
cd /var/www/
sudo git clone https://github.com/username/repository.git yourdomain.com
sudo chown -R www-data:www-data yourdomain.com/

# Create deployment script
#!/bin/bash
cd /var/www/yourdomain.com
git pull origin main
# Add any build steps here
sudo systemctl reload nginx
```

**Advanced Git Hooks Deployment**:
```bash
# Create bare repository for deployment
cd /var/git/
sudo git init --bare yourdomain.com.git

# Create post-receive hook
sudo nano /var/git/yourdomain.com.git/hooks/post-receive
# Add deployment logic

# Make executable
sudo chmod +x /var/git/yourdomain.com.git/hooks/post-receive
```

### Docker Deployment
**Install Docker**:
```bash
# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
sudo usermod -aG docker $USER

# Install Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/1.29.2/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose
```

---

## Monitoring & Maintenance

### Application Monitoring
**Log Management**:
```bash
# Application logs
tail -f /var/www/yourdomain.com/logs/app.log

# Web server logs
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# System logs
sudo journalctl -f
```

**Process Monitoring**:
```bash
# Install htop for better process monitoring
sudo apt install htop

# Monitor system resources
htop

# Check disk usage
df -h

# Check memory usage
free -h
```

### Backup Strategies
**Database Backups**:
```bash
# MySQL backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql

# PostgreSQL backup
pg_dump -U username database_name > backup_$(date +%Y%m%d).sql

# Automated backup script
#!/bin/bash
BACKUP_DIR="/home/user/backups"
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u username -p database_name > $BACKUP_DIR/backup_$DATE.sql
# Keep only last 7 days
find $BACKUP_DIR -name "backup_*.sql" -mtime +7 -delete
```

**File System Backups**:
```bash
# Create compressed backup
tar -czf backup_$(date +%Y%m%d).tar.gz /var/www/yourdomain.com

# Sync to remote storage (DigitalOcean Spaces)
# Install s3cmd first: sudo apt install s3cmd
s3cmd put backup_$(date +%Y%m%d).tar.gz s3://your-space/backups/
```

---

## Cost Optimization

### Right-Sizing Droplets
**Monitor Resource Usage**:
- Use DigitalOcean monitoring graphs
- Check CPU, memory, and disk usage patterns
- Resize Droplet if consistently over/under-utilized

**Resize Droplets**:
1. **Power Off**: Gracefully shutdown Droplet
2. **Resize**: Droplet → More → Resize
3. **Choose New Size**: Upgrade or downgrade
4. **Reboot**: Start with new resources

### Storage Optimization
**Block Storage Volumes**:
- Attach additional storage as needed
- Separate OS and application data
- Easily scalable and moveable between Droplets

**DigitalOcean Spaces** (Object Storage):
- Store static assets, backups, user uploads
- CDN integration available
- S3-compatible API

---

## Important Notes & Troubleshooting

### Common Issues
**SSH Connection Problems**:
```bash
# Check if SSH service is running
sudo systemctl status sshd

# Verify firewall settings
sudo ufw status

# Check SSH configuration
sudo sshd -t

# View SSH logs
sudo tail -f /var/log/auth.log
```

**Web Server Issues**:
```bash
# Check if web server is running
sudo systemctl status nginx   # or apache2

# Test configuration
sudo nginx -t                 # or sudo apache2ctl configtest

# Restart web server
sudo systemctl restart nginx  # or apache2

# Check if port is open
sudo netstat -tlnp | grep :80
```

**Database Connection Issues**:
```bash
# Test local database connection
mysql -u username -p -h localhost

# Check if database service is running
sudo systemctl status mysql   # or postgresql

# Review database logs
sudo tail -f /var/log/mysql/error.log
```

### Best Practices
**Security**:
- Regular security updates
- Use SSH keys instead of passwords
- Configure firewall properly
- Implement intrusion detection (Fail2Ban)
- Regular security audits

**Performance**:
- Monitor resource usage regularly
- Implement caching (Redis, Memcached)
- Use CDN for static content
- Optimize database queries
- Regular performance testing

**Backup & Recovery**:
- Automated backup strategy
- Test restore procedures
- Off-site backup storage
- Document recovery processes

### Support Resources
- **DigitalOcean Community**: Comprehensive tutorials and Q&A
- **24/7 Ticket Support**: Available for all accounts
- **Documentation**: Extensive official documentation
- **API & CLI Tools**: Automation and management tools
- **Marketplace**: Pre-configured applications and tools