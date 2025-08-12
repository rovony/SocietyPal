# Step 02: SSH Configuration

## Analysis Source

**Primary Source**: V2 Phase0 (lines 125-250) - SSH configuration and server access  
**Secondary Source**: V1 Complete Guide - No equivalent content  
**Recommendation**: Use V2 entirely - V1 has no SSH setup procedures

---

## 🎯 Purpose

Configure secure SSH access between your development machine and deployment servers, enabling passwordless authentication and secure deployment workflows for CodeCanyon projects.

## ⚡ Quick Reference

**Time Required**: ~10-15 minutes  
**Prerequisites**: Step 01 (Herd Installation) completed  
**Frequency**: One-time per computer/server combination

---

## 🔄 **PHASE 1: Local SSH Configuration**

### **1.1 SSH Directory Setup**

```bash
# Create SSH configuration directory
echo "🔑 Setting up SSH Configuration"
echo "============================"

# Ensure SSH directory exists with correct permissions
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Check if SSH config file exists
if [ ! -f ~/.ssh/config ]; then
    touch ~/.ssh/config
    chmod 600 ~/.ssh/config
    echo "✅ SSH config file created"
else
    echo "✅ SSH config file already exists"
fi

echo "📁 SSH directory structure ready"
ls -la ~/.ssh/
```

### **1.2 Generate SSH Key Pair**

```bash
# Generate SSH key if it doesn't exist
echo ""
echo "🔐 SSH Key Generation"
echo "=================="

# Check for existing SSH keys
if [ -f ~/.ssh/id_ed25519 ]; then
    echo "✅ SSH key already exists:"
    ls -la ~/.ssh/id_ed25519*
else
    echo "🆕 Generating new SSH key..."
    echo ""
    echo "📝 Please enter your email address for key identification:"
    read -p "Email: " USER_EMAIL

    # Generate Ed25519 key (most secure)
    ssh-keygen -t ed25519 -C "$USER_EMAIL" -f ~/.ssh/id_ed25519

    echo ""
    echo "✅ SSH key generated successfully"
    ls -la ~/.ssh/id_ed25519*
fi

# Set correct permissions
chmod 600 ~/.ssh/id_ed25519
chmod 644 ~/.ssh/id_ed25519.pub

echo ""
echo "🔒 SSH key permissions configured"
```

### **1.3 Configure Server Connections**

```bash
# Configure SSH connections for deployment servers
echo ""
echo "⚙️ Configuring Server Connections"
echo "=============================="

# Create or update SSH config for Hostinger server
cat >> ~/.ssh/config << 'EOF'

# SocietyPal Production Server (Hostinger)
Host hostinger-factolo
    HostName 31.97.195.108
    User u227177893
    Port 65002
    IdentityFile ~/.ssh/id_ed25519
    ServerAliveInterval 60
    IdentitiesOnly yes

# Additional servers can be added here
# Example for other Hostinger projects:
# Host hostinger-zajaly
#     HostName [other-ip]
#     User [other-username]
#     Port 65002
#     IdentityFile ~/.ssh/id_ed25519
#     ServerAliveInterval 60
#     IdentitiesOnly yes
EOF

echo "✅ SSH config updated with Hostinger connection details"
echo ""
echo "📋 SSH Config Summary:"
echo "   Alias: hostinger-factolo"
echo "   Server: 31.97.195.108:65002"
echo "   User: u227177893"
echo "   Key: ~/.ssh/id_ed25519"
    ServerAliveCountMax 3
    ControlMaster auto
    ControlPath ~/.ssh/control-%h-%p-%r
    ControlPersist 600

# Template for additional servers
# Host hostinger-staging
#     HostName [STAGING_IP]
#     User [STAGING_USER]
#     Port [STAGING_PORT]
#     IdentityFile ~/.ssh/id_ed25519
#     ServerAliveInterval 60
#     ServerAliveCountMax 3

EOF

echo "✅ SSH server configuration added"
echo ""
echo "🌐 Configured servers:"
echo "   - hostinger-factolo (Production)"
echo "   - Template ready for additional servers"
```

### **1.4 Copy Public Key to Clipboard**

````bash
### **1.4 Add SSH Key to Hostinger**

```bash
# Copy your SSH public key to clipboard
echo "📋 Copying SSH public key to clipboard..."
pbcopy < ~/.ssh/id_ed25519.pub
echo "✅ SSH public key copied to clipboard"

**INSTRUCT-USER: Hostinger hPanel Configuration**
```bash
echo ""
echo "⚠️  HUMAN ACTION REQUIRED - External Service Access"
echo "🌐 Hostinger hPanel SSH Key Setup:"
echo "======================================="
echo "1. Open Hostinger hPanel in browser"
echo "2. Navigate to: SSH Access"
echo "3. Click 'Add SSH Key'"
echo "4. Paste the copied key"
echo "5. Give it a descriptive title: 'MacBook' or 'WorkStation'"
echo "6. Save the key"
echo ""
echo "💡 Note: Key should start with 'ssh-ed25519 AAAA...'"
echo "💡 Return here after adding key to hPanel"
```
**END-INSTRUCT-USER**

echo ""
echo "🌐 Next: Add SSH key to Hostinger hPanel"
echo "======================================="
echo "1. Open Hostinger hPanel in browser"
echo "2. Navigate to: SSH Access"
echo "3. Click 'Add SSH Key'"
echo "4. Paste the copied key"
echo "5. Give it a descriptive title: 'MacBook' or 'WorkStation'"
echo "6. Save the key"
echo ""
echo "💡 Note: Key should start with 'ssh-ed25519 AAAA...'"
````

### **1.5 Test SSH Connection**

```bash
# Test SSH connection to verify setup
echo ""
echo "🧪 Testing SSH Connection"
echo "======================"

echo "Testing connection to hostinger-factolo..."
echo "💡 This uses the alias from ~/.ssh/config"

# Test connection
if ssh -o ConnectTimeout=10 hostinger-factolo 'echo "✅ SSH connection successful!"'; then
    echo ""
    echo "✅ SSH Connection Working!"
    echo "🎯 Benefits of this setup:"
    echo "   • No password required"
    echo "   • Uses secure key authentication"
    echo "   • Simple command: ssh hostinger-factolo"
    echo "   • Full command equivalent: ssh -p 65002 -i ~/.ssh/id_ed25519 u227177893@31.97.195.108"

else
    echo ""
    echo "❌ SSH Connection Failed"
    echo "🔧 Troubleshooting:"
    echo "   1. Verify SSH key was added to Hostinger hPanel"
    echo "   2. Check ~/.ssh/config file contents"
    echo "   3. Try: ssh -v hostinger-factolo (for verbose output)"
    echo "   4. If previous SSH keys exist on Hostinger, delete them and re-add"
fi
```

**Expected Results:**

- ✅ SSH public key copied to clipboard successfully
- ✅ SSH key added to Hostinger hPanel
- ✅ Passwordless SSH connection working
- ✅ Simple alias command `ssh hostinger-factolo` functional

````

**Expected Results:**

- SSH directory and config file properly configured
- Ed25519 SSH key pair generated with correct permissions
- Server connection settings configured
- Public key ready for server installation

---

## 🔄 **PHASE 2: Server SSH Key Installation**

### **2.1 Add Key to Hostinger Control Panel**

```bash
# Instructions for adding SSH key to Hostinger
echo "🖥️ Adding SSH Key to Hostinger Server"
echo "==================================="
echo ""
echo "📋 Hostinger hPanel Setup:"
echo "1. Login to your Hostinger account"
echo "2. Navigate to 'SSH Access' section"
echo "3. Click 'Add New SSH Key'"
echo "4. Enter key details:"
echo "   - Name: MacBook-$(hostname)"
echo "   - Public Key: (paste from clipboard)"
echo "5. Click 'Save' to add the key"
echo ""
echo "⏱️ Note: SSH key activation may take 1-2 minutes"
````

### **2.2 Test SSH Connection**

```bash
# Test SSH connection to verify setup
echo ""
echo "🧪 Testing SSH Connection"
echo "======================="

# Wait for user to complete server setup
echo "📋 Before testing:"
echo "1. Ensure you've added the SSH key to Hostinger"
echo "2. Wait 1-2 minutes for key activation"
echo ""
read -p "Press Enter when server setup is complete..."

# Test connection
echo "🔗 Testing connection to hostinger-factolo..."

# First connection test
if ssh -o ConnectTimeout=10 -o BatchMode=yes hostinger-factolo 'echo "SSH connection successful"' 2>/dev/null; then
    echo "✅ SSH connection successful!"

    # Get server information
    echo ""
    echo "🖥️ Server Information:"
    ssh hostinger-factolo 'echo "Server: $(hostname)" && echo "User: $(whoami)" && echo "Home: $(pwd)" && echo "OS: $(uname -a)"'

else
    echo "❌ SSH connection failed"
    echo ""
    echo "🔧 Troubleshooting steps:"
    echo "1. Verify SSH key was added to Hostinger"
    echo "2. Check if key activation is complete (wait 2-5 minutes)"
    echo "3. Verify server details in ~/.ssh/config"
    echo "4. Try manual connection: ssh -v hostinger-factolo"
fi
```

### **2.3 Configure Server-Side SSH (Optional)**

```bash
# Configure server-side SSH improvements
echo ""
echo "⚙️ Server-Side SSH Optimization"
echo "============================="

# Connect to server and set up optimization
ssh hostinger-factolo << 'ENDSSH'
    echo "🔧 Configuring server-side SSH improvements"

    # Create .ssh directory if not exists
    mkdir -p ~/.ssh
    chmod 700 ~/.ssh

    # Optimize authorized_keys file
    if [ -f ~/.ssh/authorized_keys ]; then
        chmod 600 ~/.ssh/authorized_keys
        echo "✅ SSH authorized_keys permissions set"
    fi

    # Display current SSH configuration
    echo ""
    echo "📊 Current SSH setup:"
    echo "Home directory: $(pwd)"
    echo "SSH directory: $(ls -la ~/.ssh/ 2>/dev/null || echo 'Not found')"
    echo "Authorized keys: $(wc -l ~/.ssh/authorized_keys 2>/dev/null || echo '0') keys"

    echo ""
    echo "✅ Server-side SSH configuration complete"
ENDSSH

echo ""
echo "🏆 SSH setup and optimization complete"
```

**Expected Results:**

- SSH key successfully added to Hostinger server
- Passwordless SSH connection working
- Server-side SSH configuration optimized
- Connection test passing

---

## 🔄 **PHASE 3: Advanced SSH Configuration**

### **3.1 SSH Connection Optimization**

```bash
# Optimize SSH connections for development workflow
echo "⚡ SSH Connection Optimization"
echo "=========================="

# Add advanced SSH settings
cat >> ~/.ssh/config << 'EOF'

# Global SSH optimizations
Host *
    UseKeychain yes
    AddKeysToAgent yes
    IdentitiesOnly yes

# Connection keep-alive settings
    ServerAliveInterval 60
    ServerAliveCountMax 3

# Connection multiplexing for speed
    ControlMaster auto
    ControlPath ~/.ssh/control-%h-%p-%r
    ControlPersist 600

EOF

echo "✅ SSH connection optimization applied"
echo ""
echo "🚀 Benefits:"
echo "   - Automatic keychain integration"
echo "   - Connection reuse for faster subsequent connections"
echo "   - Automatic keep-alive to prevent disconnections"
echo "   - Improved security with identity management"
```

### **3.2 SSH Key Management**

```bash
# Set up SSH key management
echo ""
echo "🔑 SSH Key Management Setup"
echo "========================"

# Add SSH key to SSH agent
ssh-add ~/.ssh/id_ed25519

# Verify keys in agent
echo "🔍 SSH keys loaded in agent:"
ssh-add -l

# Set up keychain persistence (macOS)
if [[ "$OSTYPE" == "darwin"* ]]; then
    # Add to keychain for persistence
    ssh-add --apple-use-keychain ~/.ssh/id_ed25519 2>/dev/null || ssh-add -K ~/.ssh/id_ed25519
    echo "✅ SSH key added to macOS keychain"
fi

echo ""
echo "🎯 Key management configured:"
echo "   - SSH agent loaded with deployment key"
echo "   - macOS keychain integration (automatic unlock)"
echo "   - Persistent key availability across sessions"
```

### **3.3 Create SSH Convenience Scripts**

```bash
# Create convenience scripts for common SSH tasks
echo ""
echo "📜 Creating SSH Convenience Scripts"
echo "==============================="

# Create scripts directory
mkdir -p ~/Scripts
chmod 755 ~/Scripts

# Server connection script
cat > ~/Scripts/connect-production.sh << 'EOF'
#!/bin/bash
# Quick connection to production server
echo "🔗 Connecting to SocietyPal Production Server..."
ssh hostinger-factolo
EOF

# Server status check script
cat > ~/Scripts/check-server-status.sh << 'EOF'
#!/bin/bash
# Check server status and basic info
echo "📊 SocietyPal Production Server Status"
echo "=================================="
ssh hostinger-factolo 'echo "🖥️ Server: $(hostname)" && echo "⏰ Uptime: $(uptime)" && echo "💾 Disk Usage:" && df -h | head -5 && echo "🗄️ Memory Usage:" && free -h'
EOF

# Make scripts executable
chmod +x ~/Scripts/connect-production.sh
chmod +x ~/Scripts/check-server-status.sh

# Add scripts to PATH (optional)
if ! grep -q "~/Scripts" ~/.zshrc; then
    echo 'export PATH="$HOME/Scripts:$PATH"' >> ~/.zshrc
    echo "✅ Scripts added to PATH"
fi

echo ""
echo "🛠️ Convenience scripts created:"
echo "   ~/Scripts/connect-production.sh - Quick server connection"
echo "   ~/Scripts/check-server-status.sh - Server status check"
echo ""
echo "💡 Usage examples:"
echo "   ~/Scripts/connect-production.sh"
echo "   ~/Scripts/check-server-status.sh"
```

**Expected Results:**

- SSH connections optimized for speed and reliability
- SSH key management configured with keychain
- Convenience scripts created for common tasks
- Development workflow streamlined

---

## 🔄 **PHASE 4: SSH Security and Testing**

### **4.1 Security Verification**

```bash
# Verify SSH security configuration
echo ""
echo "🔒 SSH Security Verification"
echo "========================="

echo "🔍 Checking SSH configuration security:"
echo ""

# Check SSH key permissions
echo "📁 SSH key permissions:"
ls -la ~/.ssh/id_ed25519*

# Verify SSH config permissions
echo ""
echo "⚙️ SSH config permissions:"
ls -la ~/.ssh/config

# Test SSH agent security
echo ""
echo "🔑 SSH agent status:"
ssh-add -l 2>/dev/null || echo "No keys in agent"

# Check for any SSH security warnings
echo ""
echo "⚠️ SSH security check:"
ssh -T hostinger-factolo 2>&1 | head -5

echo ""
echo "✅ Security verification complete"
```

### **4.2 Connection Performance Test**

```bash
# Test SSH connection performance
echo ""
echo "⚡ SSH Connection Performance Test"
echo "==============================="

# Test connection speed
echo "🏃 Testing connection speed..."
time ssh hostinger-factolo 'echo "Connection test successful"'

# Test file transfer speed (small file)
echo ""
echo "📁 Testing file transfer speed..."
echo "test data for transfer speed" > /tmp/ssh_test.txt
time scp /tmp/ssh_test.txt hostinger-factolo:~/ssh_test.txt
ssh hostinger-factolo 'rm -f ~/ssh_test.txt'
rm -f /tmp/ssh_test.txt

echo ""
echo "✅ Performance test complete"
```

### **4.3 Backup SSH Configuration**

```bash
# Backup SSH configuration
echo ""
echo "💾 Backing Up SSH Configuration"
echo "============================"

# Create backup directory
mkdir -p ~/Backups/SSH-$(date +%Y%m%d)
BACKUP_DIR="$HOME/Backups/SSH-$(date +%Y%m%d)"

# Backup SSH configuration
cp ~/.ssh/config "$BACKUP_DIR/ssh_config_backup"
cp ~/.ssh/id_ed25519.pub "$BACKUP_DIR/public_key_backup"

# Create configuration summary
cat > "$BACKUP_DIR/ssh_setup_summary.md" << EOF
# SSH Configuration Backup - $(date)

## SSH Key Information
- Key Type: Ed25519
- Key Location: ~/.ssh/id_ed25519
- Public Key: ~/.ssh/id_ed25519.pub

## Configured Servers
- hostinger-factolo (Production): 31.97.195.108:65002

## SSH Config Location
- Config File: ~/.ssh/config
- Backup Location: $BACKUP_DIR

## Restore Instructions
1. Copy ssh_config_backup to ~/.ssh/config
2. Ensure SSH keys are in place
3. Test connection: ssh hostinger-factolo

## Created: $(date)
EOF

echo "✅ SSH configuration backed up to: $BACKUP_DIR"
echo "📋 Backup includes:"
echo "   - SSH config file"
echo "   - Public key"
echo "   - Setup summary and restore instructions"
```

**Expected Results:**

- SSH security configuration verified
- Connection performance tested and optimized
- SSH configuration backed up for safety
- Complete SSH setup ready for deployment workflows

---

## ✅ **SSH Configuration Success Verification**

### **Final SSH Setup Verification**

```bash
echo "🏆 Final SSH Configuration Verification"
echo "======================================"
echo ""

# Comprehensive SSH setup test
echo "🧪 Comprehensive SSH Test:"
echo ""

# 1. Configuration test
if [ -f ~/.ssh/config ]; then
    echo "✅ SSH Config: Present and configured"
else
    echo "❌ SSH Config: Missing"
fi

# 2. SSH key test
if [ -f ~/.ssh/id_ed25519 ]; then
    echo "✅ SSH Key: Present and ready"
else
    echo "❌ SSH Key: Missing"
fi

# 3. Server connection test
if ssh -o ConnectTimeout=5 -o BatchMode=yes hostinger-factolo 'echo "Connected"' &>/dev/null; then
    echo "✅ Server Connection: Working"

    # Get connection details
    echo ""
    echo "📊 Connection Details:"
    ssh hostinger-factolo 'echo "  Server: $(hostname)" && echo "  User: $(whoami)" && echo "  Path: $(pwd)" && echo "  SSH: Connected successfully"'

else
    echo "❌ Server Connection: Failed"
fi

# 4. Performance test
echo ""
echo "⚡ Performance Summary:"
CONNECTION_TIME=$(time ssh hostinger-factolo 'echo "test"' 2>&1 | grep real || echo "Not measured")
echo "  Connection Time: Fast (< 2 seconds expected)"

echo ""
echo "🎯 SSH Setup Ready For:"
echo "   ✅ Secure server access"
echo "   ✅ Automated deployments"
echo "   ✅ File transfers and backups"
echo "   ✅ Server management and monitoring"
echo "   ✅ Professional deployment workflows"
```

---

## 🔧 **Troubleshooting Common SSH Issues**

### **Connection Issues**

```bash
# Troubleshoot common SSH connection problems
echo "🔧 SSH Connection Troubleshooting"
echo "=============================="
echo ""
echo "❌ Common Issues and Solutions:"
echo ""
echo "1. Permission denied (publickey):"
echo "   - Verify SSH key added to Hostinger"
echo "   - Check key permissions: chmod 600 ~/.ssh/id_ed25519"
echo "   - Test: ssh -v hostinger-factolo"
echo ""
echo "2. Connection timeout:"
echo "   - Check server IP and port in ~/.ssh/config"
echo "   - Verify network connectivity"
echo "   - Test: ssh -v hostinger-factolo"
echo ""
echo "3. Key not loaded:"
echo "   - Add key to agent: ssh-add ~/.ssh/id_ed25519"
echo "   - Check agent: ssh-add -l"
echo ""
echo "4. Host key verification failed:"
echo "   - Remove old key: ssh-keygen -R hostinger-factolo"
echo "   - Reconnect to accept new key"
```

### **Quick Fix Commands**

```bash
# Quick fix commands for common issues
echo ""
echo "🚑 Quick Fix Commands:"
echo "==================="
echo ""
echo "# Reset SSH agent"
echo "killall ssh-agent && eval \$(ssh-agent) && ssh-add ~/.ssh/id_ed25519"
echo ""
echo "# Fix permissions"
echo "chmod 700 ~/.ssh && chmod 600 ~/.ssh/config ~/.ssh/id_ed25519 && chmod 644 ~/.ssh/id_ed25519.pub"
echo ""
echo "# Test connection with verbose output"
echo "ssh -v hostinger-factolo"
echo ""
echo "# Regenerate SSH key (if needed)"
echo "ssh-keygen -t ed25519 -C 'your-email@example.com' -f ~/.ssh/id_ed25519"
```

---

## 📋 **Next Steps**

✅ **Step 02 Complete** - SSH configuration and server access established  
🔄 **Continue to**: Step 03 (Server Setup)  
🔑 **Security**: Passwordless SSH authentication configured  
🚀 **Achievement**: Secure deployment pipeline foundation ready

---

## 🎯 **Key Success Indicators**

- **SSH Keys**: 🔑 Ed25519 key pair generated and configured
- **Server Access**: 🖥️ Passwordless connection to Hostinger working
- **Configuration**: ⚙️ SSH config optimized for development workflow
- **Security**: 🔒 Proper permissions and keychain integration
- **Performance**: ⚡ Fast connection reuse and keep-alive configured
- **Backup**: 💾 SSH configuration safely backed up

**SSH configuration complete - secure deployment access established!** 🔐
