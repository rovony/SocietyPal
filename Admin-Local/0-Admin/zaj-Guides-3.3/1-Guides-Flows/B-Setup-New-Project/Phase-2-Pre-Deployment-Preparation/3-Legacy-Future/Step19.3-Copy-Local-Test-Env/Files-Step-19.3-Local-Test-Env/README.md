# Local Test Environment for SSH & Build Pipeline Testing

## 🎯 Purpose
This test environment allows you to test SSH commands, build pipelines, and deployment scripts locally before running them on servers or build VMs. It helps catch potential errors and configuration issues early in the development cycle.

## 📁 Structure
```
Step-19.3-Local-Test-Env/
├── 1-Setup-Files/           # Core setup and configuration files
├── 2-Playground/            # Test workspace and sample projects
├── 3-Docker-Environment/    # Docker-based testing (isolated)
├── 4-Local-Environment/     # Local testing (no Docker required)
└── README.md                # This file
```

## 🚀 Quick Start

### Option 1: Docker Environment (Recommended - Isolated)
```bash
cd 3-Docker-Environment
./start-test-env.sh
./test-command.sh 01-create-laravel-directories
```

### Option 2: Local Environment (Simple - No Docker)
```bash
cd 4-Local-Environment
./setup-local-env.sh
./test-local-command.sh 01-create-laravel-directories
```

## 🧪 What Can Be Tested

### 1. Build Pipeline Commands
- Laravel directory creation and permissions
- PHP dependency installation (Composer)
- Node.js dependency installation (npm)
- Asset compilation and optimization
- Laravel optimization commands

### 2. SSH Pipeline Commands
- File operations and permissions
- Environment variable handling
- Path and directory operations
- Command execution and error handling

### 3. Server Configuration Validation
- PHP version compatibility
- Composer version requirements
- Node.js/npm version requirements
- File permission issues
- Path and dependency problems

## 🔄 Environment Management

### Docker Environment
- `start-test-env.sh` - Start containers
- `stop-test-env.sh` - Stop containers
- `reset-test-env.sh` - Reset to clean state
- `clean-test-env.sh` - Remove everything

### Local Environment
- `setup-local-env.sh` - Setup local workspace
- `reset-local-env.sh` - Reset workspace
- `clean-local-env.sh` - Clean everything

## 📋 Usage Examples

### Test a Build Command
```bash
# Test directory creation
./test-command.sh 01-create-laravel-directories

# Test PHP dependencies
./test-command.sh 02-install-php-dependencies

# Test Node.js dependencies
./test-command.sh 03-install-node-dependencies

# Test asset building
./test-command.sh 04-build-assets-optimize
```

### Test Custom Commands
```bash
# Copy your custom script to test-workspace/
cp your-script.sh test-workspace/

# Execute in container
docker exec -it laravel-build-test bash -c "cd /workspace && ./your-script.sh"
```

## 🎯 Benefits

1. **Error Prevention**: Catch issues before they reach production
2. **Version Testing**: Test with different PHP/Node/Composer versions
3. **Isolation**: Test without affecting your main workspace
4. **Reproducibility**: Consistent testing environment
5. **Learning**: Understand how commands behave in different contexts

## 🔧 Customization

### Add New Test Scenarios
1. Create your test script in `2-Playground/`
2. Use the existing test infrastructure
3. Document any new requirements

### Modify Test Environment
1. Edit Docker configurations in `3-Docker-Environment/`
2. Modify local setup in `4-Local-Environment/`
3. Update this README with changes

## 🚨 Troubleshooting

### Common Issues
- **Docker not running**: Start Docker Desktop
- **Permission denied**: Check file permissions
- **Port conflicts**: Change ports in docker-compose.yml
- **Version mismatches**: Update your local tools

### Getting Help
1. Check the logs in each environment
2. Verify tool versions match requirements
3. Ensure test workspace is properly set up
4. Check container status with `docker-compose ps`
