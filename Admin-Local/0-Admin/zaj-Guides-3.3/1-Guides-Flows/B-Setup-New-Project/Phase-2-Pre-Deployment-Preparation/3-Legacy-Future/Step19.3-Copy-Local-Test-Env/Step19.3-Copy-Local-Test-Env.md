# Step 19.3: Copy Local Test Environment

## 🎯 Objective
Copy the pre-built test environment from your Admin folder to the current project to enable local testing of SSH commands, build pipelines, and deployment scripts before running them on servers.

## 📋 Prerequisites
- Admin folder with the Test-Environment folder saved
- Current project workspace ready
- Basic understanding of Docker and local development tools

## 🔄 Steps to Copy Test Environment

### Step 1: Copy Test Environment Folder
```bash
# Navigate to your Admin folder
cd /path/to/your/Admin-folder

# Copy the Test-Environment folder to current project
cp -r "Test-Environment" "/path/to/current/project/Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-19.3-Local-Test-Env"
```

### Step 2: Verify Copy Success
```bash
# Check the copied structure
ls -la "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-19.3-Local-Test-Env"

# Expected structure:
# ├── 1-Setup-Files/
# ├── 2-Playground/
# ├── 3-Docker-Environment/
# ├── 4-Local-Environment/
# └── README.md
```

### Step 3: Make Scripts Executable
```bash
# Navigate to test environment
cd "Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/2-Files/Step-19.3-Local-Test-Env"

# Make all scripts executable
find . -name "*.sh" -exec chmod +x {} \;
```

## 🚀 How to Use the Test Environment

### Option A: Docker Environment (Recommended)
```bash
cd 3-Docker-Environment

# Start the environment
./start-test-env.sh

# Test build commands
./test-command.sh 01-create-laravel-directories
./test-command.sh 02-install-php-dependencies
./test-command.sh 03-install-node-dependencies
./test-command.sh 04-build-assets-optimize

# Stop when done
./stop-test-env.sh
```

### Option B: Local Environment (No Docker)
```bash
cd 4-Local-Environment

# Setup local workspace
./setup-local-env.sh

# Test build commands
./test-local-command.sh 01-create-laravel-directories
./test-local-command.sh 02-install-php-dependencies
./test-local-command.sh 03-install-node-dependencies
./test-local-command.sh 04-build-assets-optimize

# Clean when done
./clean-local-env.sh
```

## 🧪 Testing Build Pipeline Commands

### 1. Test Laravel Directory Creation
```bash
# This tests the SSH command that creates Laravel directories on server
./test-command.sh 01-create-laravel-directories

# Expected output:
# ✅ bootstrap/cache: exists
# ✅ storage/app: exists
# ✅ storage/framework: exists
# ✅ storage/logs: exists
```

### 2. Test PHP Dependencies Installation
```bash
# This tests the SSH command that installs Composer dependencies
./test-command.sh 02-install-php-dependencies

# Expected output:
# ✅ Composer dependencies valid
# ✅ PHP dependencies installed
```

### 3. Test Node.js Dependencies Installation
```bash
# This tests the SSH command that installs npm dependencies
./test-command.sh 03-install-node-dependencies

# Expected output:
# ✅ Node.js dependencies installed
```

### 4. Test Asset Building and Optimization
```bash
# This tests the SSH command that builds and optimizes Laravel
./test-command.sh 04-build-assets-optimize

# Expected output:
# ✅ Frontend assets compiled successfully
# ✅ Laravel build and optimization completed
```

## 🔍 Testing SSH Pipeline Commands

### Test Custom SSH Commands
```bash
# Copy your custom SSH script to test workspace
cp your-ssh-script.sh test-workspace/

# Execute in container (simulates server environment)
docker exec -it laravel-build-test bash -c "cd /workspace && ./your-ssh-script.sh"
```

### Test Environment Variables
```bash
# Test how your script handles environment variables
docker exec -it laravel-build-test bash -c "cd /workspace && export TEST_VAR=value && ./your-script.sh"
```

### Test File Permissions
```bash
# Test permission-related operations
docker exec -it laravel-build-test bash -c "cd /workspace && chmod 755 your-script.sh && ./your-script.sh"
```

## 🎯 What This Helps Catch

### 1. Build Pipeline Issues
- **Version Mismatches**: PHP, Composer, Node.js version incompatibilities
- **Missing Dependencies**: Required packages not available
- **Permission Problems**: File/directory permission issues
- **Path Issues**: Incorrect file paths or working directories

### 2. SSH Pipeline Issues
- **Command Failures**: Scripts that fail in different environments
- **Environment Differences**: Missing environment variables
- **Tool Availability**: Commands not available on target system
- **Path Resolution**: Relative vs absolute path problems

### 3. Server Configuration Issues
- **PHP Extensions**: Missing required PHP extensions
- **Composer Configuration**: Composer settings conflicts
- **Node.js Setup**: npm configuration issues
- **File System**: Permission and ownership problems

## 🔄 Environment Management

### Reset Environment
```bash
# Docker environment
cd 3-Docker-Environment
./reset-test-env.sh

# Local environment
cd 4-Local-Environment
./reset-local-env.sh
```

### Clean Environment
```bash
# Docker environment
cd 3-Docker-Environment
./clean-test-env.sh

# Local environment
cd 4-Local-Environment
./clean-local-env.sh
```

## 📝 Customization

### Add New Test Scenarios
1. Create your test script in `2-Playground/`
2. Use existing test infrastructure
3. Document requirements and expected outcomes

### Modify Test Environment
1. Edit Docker configurations in `3-Docker-Environment/`
2. Modify local setup in `4-Local-Environment/`
3. Update README.md with changes

## ✅ Verification Checklist

- [ ] Test environment copied successfully
- [ ] All scripts are executable
- [ ] Docker environment starts without errors
- [ ] Local environment setup works
- [ ] All build commands test successfully
- [ ] Custom SSH commands can be tested
- [ ] Environment can be reset and cleaned

## 🚨 Troubleshooting

### Common Issues
- **Permission Denied**: Run `chmod +x *.sh` on all script files
- **Docker Not Running**: Start Docker Desktop before testing
- **Port Conflicts**: Change ports in docker-compose.yml if needed
- **Version Mismatches**: Update local tools to match requirements

### Getting Help
1. Check the logs in each environment
2. Verify tool versions match requirements
3. Ensure test workspace is properly set up
4. Check container status with `docker-compose ps`

## 🔗 Related Steps
- **Step 19.1**: Build Pipeline Research and Development
- **Step 19.2**: SSH Pipeline Development
- **Step 19.4**: Test Environment Validation
- **Step 19.5**: Pipeline Integration Testing

## 📁 Final Structure
```
Step-19.3-Local-Test-Env/
├── 1-Setup-Files/           # Core setup and configuration files
│   └── setup-test-workspace.sh
├── 2-Playground/            # Test workspace and sample projects
├── 3-Docker-Environment/    # Docker-based testing (isolated)
│   ├── docker-compose.yml
│   ├── nginx.conf
│   ├── start-test-env.sh
│   ├── stop-test-env.sh
│   ├── reset-test-env.sh
│   ├── clean-test-env.sh
│   └── test-command.sh
├── 4-Local-Environment/     # Local testing (no Docker required)
│   ├── setup-local-env.sh
│   ├── test-local-command.sh
│   ├── reset-local-env.sh
│   └── clean-local-env.sh
└── README.md                # Comprehensive usage guide
```

## 🎯 Key Benefits for This Step

1. **Isolated Testing**: Test SSH and build commands without affecting your main workspace
2. **Error Prevention**: Catch server deployment issues before they happen
3. **Version Validation**: Test with different PHP/Node/Composer versions
4. **Easy Reset**: Clean environment for consistent testing
5. **Dual Options**: Docker (isolated) or Local (simple) testing environments
