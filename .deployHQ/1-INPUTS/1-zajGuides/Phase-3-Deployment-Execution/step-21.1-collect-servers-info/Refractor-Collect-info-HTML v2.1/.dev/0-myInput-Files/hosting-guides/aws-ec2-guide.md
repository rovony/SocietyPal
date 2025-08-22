# AWS EC2 Cloud Hosting Setup Guide

## SSH Connection Details

### Step 1: Create EC2 Key Pair
**Option 1: Create Key Pair via AWS Console**
1. **Navigate to EC2 Dashboard**: AWS Console → EC2
2. **Access Key Pairs**:
   - Left sidebar → **Network & Security** → **Key Pairs**
   - Click **Create key pair**
3. **Configure Key Pair**:
   - **Name**: Choose descriptive name (e.g., `my-ec2-key`)
   - **Key pair type**: RSA (recommended)
   - **Private key file format**: `.pem` for OpenSSH, `.ppk` for PuTTY
4. **Download**: Private key downloads automatically (save securely!)

**Option 2: Import Existing Key**
1. Generate key locally: `ssh-keygen -t rsa -b 2048 -f ~/.ssh/aws_key`
2. Import public key: **Key Pairs** → **Import key pair**
3. Paste public key content

### Step 2: Launch EC2 Instance with Key Pair
1. **Launch Instance**: EC2 Dashboard → **Launch Instance**
2. **Choose AMI**: Select OS (Amazon Linux, Ubuntu, etc.)
3. **Instance Type**: Select appropriate size (t2.micro for free tier)
4. **Key Pair**: Select your created/imported key pair
5. **Security Group**: Configure SSH access (port 22)

### Step 3: Configure Security Groups
**Create/Modify Security Group**:
- **Inbound Rules**:
  - SSH (22): Your IP or 0.0.0.0/0 (less secure)
  - HTTP (80): 0.0.0.0/0 (for web access)
  - HTTPS (443): 0.0.0.0/0 (for SSL)
- **Outbound Rules**: Usually allow all

### Step 4: Connect via SSH
**Get Connection Details**:
- **Server Host/IP**: Public IPv4 address from EC2 console
- **SSH Username**: Depends on AMI:
  - Amazon Linux: `ec2-user`
  - Ubuntu: `ubuntu`
  - CentOS: `centos`
  - RHEL: `ec2-user`
- **SSH Port**: `22` (default)
- **SSH Private Key**: Your downloaded `.pem` file

**SSH Connection Commands**:
```bash
# Set correct permissions for private key
chmod 400 /path/to/your-key.pem

# Connect to instance
ssh -i /path/to/your-key.pem ec2-user@your-instance-ip

# Example
ssh -i ~/.ssh/my-ec2-key.pem ubuntu@54.123.45.67
```

**Using PuTTY (Windows)**:
1. Convert `.pem` to `.ppk` using PuTTYgen
2. Configure PuTTY:
   - Host Name: `ubuntu@54.123.45.67`
   - Port: `22`
   - SSH → Auth → Private key file: Select `.ppk` file

---

## Server Hosting Details

### AWS Service Options
| Service Type | Use Case | Management Level |
|--------------|----------|------------------|
| **EC2** | Full control virtual servers | Self-managed |
| **Lightsail** | Simple VPS hosting | Managed |
| **Elastic Beanstalk** | Application deployment | Fully managed |
| **ECS/EKS** | Container hosting | Container-managed |

### EC2 Instance Information
- **Hosting Provider**: Amazon Web Services (AWS)
- **Server OS**: Multiple options (Amazon Linux, Ubuntu, CentOS, Windows)
- **Web Server**: Install Apache, Nginx, or others
- **PHP Version**: Install desired version
- **Node.js Version**: Install via package managers

### Instance Types & Specifications
```
t3.micro:  1 vCPU, 1GB RAM   (Free tier eligible)
t3.small:  1 vCPU, 2GB RAM
t3.medium: 2 vCPU, 4GB RAM
m5.large:  2 vCPU, 8GB RAM
c5.large:  2 vCPU, 4GB RAM   (Compute optimized)
```

---

## Domain & URL Configuration

### Domain Management Options
**AWS Route 53** (Recommended):
1. **Create Hosted Zone**: For your domain
2. **DNS Records**: Point to EC2 public IP
3. **Health Checks**: Monitor instance health

**External DNS Providers**:
- Point A record to EC2 public IP address
- Configure subdomain CNAMEs as needed

### Elastic IP Configuration
**Why Use Elastic IP**:
- Static IP address for your instance
- Survives instance stops/starts
- Free when attached to running instance

**Setup Steps**:
1. **Allocate Elastic IP**: EC2 → Elastic IPs → Allocate
2. **Associate with Instance**: Actions → Associate Elastic IP address
3. **Update DNS**: Point domain to Elastic IP

### SSL Certificate Setup
**AWS Certificate Manager (ACM)**:
1. **Request Certificate**: ACM → Request certificate
2. **Domain Validation**: Via DNS or email
3. **Use with Load Balancer**: For HTTPS termination

**Let's Encrypt on Instance**:
```bash
# Install Certbot (Ubuntu example)
sudo apt update
sudo apt install certbot python3-certbot-apache

# Get certificate
sudo certbot --apache -d yourdomain.com
```

---

## Database Information

### AWS Database Options
| Service | Type | Management Level |
|---------|------|------------------|
| **RDS** | Managed SQL databases | Fully managed |
| **DynamoDB** | NoSQL database | Fully managed |
| **DocumentDB** | MongoDB-compatible | Managed |
| **Self-hosted** | Any database on EC2 | Self-managed |

### RDS Configuration (Recommended)
**Database Setup**:
- **Database Type**: MySQL, PostgreSQL, MariaDB, etc.
- **Database Host**: RDS endpoint (e.g., `mydb.123456.region.rds.amazonaws.com`)
- **Database Port**: `3306` (MySQL), `5432` (PostgreSQL)
- **Database Name**: Set during creation
- **Database Username**: Master username
- **Database Password**: Set during creation

**RDS Setup Steps**:
1. **Create RDS Instance**: RDS → Create database
2. **Configure Settings**: Engine, instance class, storage
3. **Security Groups**: Allow access from EC2 security group
4. **Parameter Groups**: Configure database settings

### Self-Hosted Database on EC2
```bash
# Install MySQL (Ubuntu)
sudo apt update
sudo apt install mysql-server
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
CREATE DATABASE myapp_db;
CREATE USER 'myapp_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON myapp_db.* TO 'myapp_user'@'localhost';
FLUSH PRIVILEGES;
```

---

## Server Directory Structure

### Standard Linux Directory Structure
```
/
├── home/
│   └── ubuntu/                 (User home directory)
│       ├── applications/       (Your applications)
│       └── .ssh/              (SSH keys)
├── var/
│   └── www/
│       └── html/              (Web server document root)
├── opt/                       (Optional software)
└── etc/                       (Configuration files)
```

### Web Server Configuration
**Apache Document Root**:
```
/var/www/html/                 (Default)
/var/www/yourdomain.com/       (Virtual host)
```

**Nginx Document Root**:
```
/var/www/html/                 (Default)
/usr/share/nginx/html/         (Alternative default)
```

### File Permissions
- **Web User**: `www-data` (Ubuntu/Debian), `apache` (CentOS/RHEL)
- **File Owner**: Usually `www-data` or your user account
- **Standard Permissions**:
  - Files: `644`
  - Directories: `755`
  - Executables: `755`

**Setting Permissions**:
```bash
# Change ownership to web user
sudo chown -R www-data:www-data /var/www/html/

# Set proper permissions
find /var/www/html/ -type d -exec chmod 755 {} \;
find /var/www/html/ -type f -exec chmod 644 {} \;
```

---

## Security & Authentication

### AWS Identity & Access Management (IAM)
**EC2 Instance Profile**:
1. **Create IAM Role**: With necessary permissions
2. **Attach to Instance**: EC2 → Actions → Security → Modify IAM role
3. **Use AWS CLI**: Access other AWS services securely

**Security Groups** (Instance Firewall):
- Acts as virtual firewall
- Control inbound/outbound traffic
- Rules based on protocol, port, source/destination

### Instance Security Best Practices
**System Updates**:
```bash
# Ubuntu/Debian
sudo apt update && sudo apt upgrade -y

# Amazon Linux/CentOS
sudo yum update -y
```

**Fail2Ban Setup** (Intrusion Prevention):
```bash
# Install and configure Fail2Ban
sudo apt install fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

**SSH Hardening**:
```bash
# Edit SSH configuration
sudo vim /etc/ssh/sshd_config

# Recommended changes:
Port 2222                    # Change default port
PermitRootLogin no          # Disable root login
PasswordAuthentication no   # Use key-based auth only
MaxAuthTries 3              # Limit login attempts
```

### Application Security
**Environment Variables**:
```bash
# Create .env file
vim ~/.env

# Example configuration:
DB_HOST=mydb.123456.region.rds.amazonaws.com
DB_DATABASE=myapp_production
DB_USERNAME=myapp_user
DB_PASSWORD=secure_database_password

AWS_ACCESS_KEY_ID=AKIA...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=us-east-1
```

---

## Deployment Strategies

### Manual Deployment
**File Transfer Methods**:
```bash
# SCP (Secure Copy)
scp -i ~/.ssh/key.pem -r ./myapp/ ubuntu@instance-ip:/var/www/html/

# RSYNC (Synchronization)
rsync -avz -e "ssh -i ~/.ssh/key.pem" ./myapp/ ubuntu@instance-ip:/var/www/html/
```

### Git-Based Deployment
**Setup Git Repository**:
```bash
# Clone repository on server
cd /var/www/html/
sudo git clone https://github.com/user/repo.git
sudo chown -R www-data:www-data repo/

# Create deployment script
#!/bin/bash
cd /var/www/html/repo
git pull origin main
# Add build steps here
sudo systemctl reload apache2
```

### AWS CodeDeploy (Advanced)
1. **Create Application**: CodeDeploy → Applications
2. **Setup Deployment Group**: Target EC2 instances
3. **Create appspec.yml**: Deployment configuration file
4. **Automated Deployment**: Integrate with CodePipeline

---

## Monitoring & Maintenance

### AWS CloudWatch Monitoring
**Built-in Metrics**:
- CPU Utilization
- Network In/Out
- Disk Read/Write Operations
- Status Check Failed

**Custom Metrics**: Install CloudWatch agent for detailed monitoring

### Backup Strategies
**EBS Snapshots**:
1. **Create Snapshot**: EC2 → Volumes → Actions → Create Snapshot
2. **Automate**: Use AWS Backup or Lambda functions
3. **Cross-Region**: Copy snapshots to other regions

**Application-Level Backups**:
```bash
# Database backup
mysqldump -h rds-endpoint -u user -p database > backup.sql

# File system backup
tar -czf /backup/files-$(date +%Y%m%d).tar.gz /var/www/html/
```

---

## Cost Optimization

### Instance Right-Sizing
- **Monitor Usage**: Use CloudWatch metrics
- **Reserved Instances**: For predictable workloads
- **Spot Instances**: For fault-tolerant applications
- **Auto Scaling**: Scale based on demand

### Storage Optimization
- **EBS Volume Types**: Choose appropriate type (gp3, gp2, io1)
- **S3 Integration**: Store static files in S3
- **CloudFront CDN**: Cache content globally

---

## Important Notes & Troubleshooting

### Common Issues
**SSH Connection Problems**:
1. Check security group rules (port 22 open)
2. Verify key file permissions (400)
3. Confirm correct username for AMI type
4. Check instance state (running)

**Web Server Not Accessible**:
1. Install and start web server
2. Configure security groups (ports 80/443)
3. Check instance firewall (ufw/firewalld)
4. Verify DNS configuration

**Performance Issues**:
1. Monitor CloudWatch metrics
2. Consider instance type upgrade
3. Optimize database queries
4. Implement caching (Redis, Memcached)

### Best Practices
**Security**:
- Regular security updates
- Use IAM roles instead of access keys
- Enable detailed monitoring
- Implement backup strategy
- Use VPC for network isolation

**Performance**:
- Use Elastic Load Balancer for high availability
- Implement auto-scaling groups
- Use CloudFront for static content
- Optimize database configuration

### Support Resources
- **AWS Documentation**: Comprehensive guides and tutorials
- **AWS Support**: Multiple support tiers available
- **AWS Forums**: Community support and discussions
- **AWS Training**: Free and paid training resources
- **AWS Well-Architected Framework**: Best practices guidelines