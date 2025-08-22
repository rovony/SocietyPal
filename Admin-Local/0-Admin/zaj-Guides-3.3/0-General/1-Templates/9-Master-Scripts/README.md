# Master Template Scripts

This directory contains master scripts for managing the entire template ecosystem of the project. These scripts provide comprehensive automation for template regeneration, selective updates, and cleanup operations.

## 📋 Table of Contents

1. [Overview](#overview)
2. [Scripts Available](#scripts-available)
3. [Usage Guide](#usage-guide)
4. [Script Details](#script-details)
5. [Safety Features](#safety-features)
6. [Troubleshooting](#troubleshooting)
7. [Integration with Workflows](#integration-with-workflows)

## 🔍 Overview

The master scripts system provides a unified interface for managing all template systems in the project. This includes:

- **Template Regeneration**: Automatically regenerate all or specific template systems
- **Interactive Management**: User-friendly interfaces for selective operations
- **Safety Controls**: Built-in safeguards and confirmation prompts
- **Cleanup Operations**: Safe removal of template-generated files
- **Status Reporting**: Comprehensive success/failure tracking

## 📁 Scripts Available

| Script | Purpose | Usage Level |
|--------|---------|-------------|
| [`regenerate-all-templates.sh`](#regenerate-all-templatessh) | Regenerate all template systems | Beginner |
| [`regenerate-selective.sh`](#regenerate-selectivesh) | Selective regeneration with options | Intermediate |
| [`cleanup-templates.sh`](#cleanup-templatessh) | Remove template files safely | Advanced |

## 🚀 Usage Guide

### Quick Start

```bash
# Navigate to master scripts directory
cd Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/9-Master-Scripts/

# Regenerate all templates (recommended for first-time setup)
./regenerate-all-templates.sh

# Interactive selective regeneration
./regenerate-selective.sh --interactive

# Cleanup specific template files
./cleanup-templates.sh tracking customization
```

### Common Workflows

#### 1. Fresh Project Setup
```bash
# Start with clean regeneration of all systems
./regenerate-all-templates.sh
```

#### 2. Update Specific Systems
```bash
# Interactive selection
./regenerate-selective.sh --interactive

# Or direct specification
./regenerate-selective.sh tracking data-persistence
```

#### 3. Clean Development Environment
```bash
# Interactive cleanup with safety prompts
./cleanup-templates.sh --interactive

# Force cleanup (DANGEROUS - use with caution)
./cleanup-templates.sh --all --force
```

## 📝 Script Details

### `regenerate-all-templates.sh`

**Purpose**: Regenerates all template systems in the correct dependency order.

**Features**:
- Automatic project root detection
- Dependency-aware execution order
- Comprehensive success/failure reporting
- Colored output for better visibility
- Error handling and recovery

**Usage**:
```bash
# Basic usage (from any directory in project)
./regenerate-all-templates.sh

# The script automatically:
# 1. Detects project root
# 2. Finds template directory
# 3. Executes in correct order:
#    - 5-Tracking-System
#    - 6-Customization-System  
#    - 7-Data-Persistence-System
#    - 8-Investment-Protection-System
```

**Template Order**:
1. **Tracking System** - Foundation for all other systems
2. **Customization System** - Core development structure
3. **Data Persistence** - File and configuration management
4. **Investment Protection** - Documentation and tracking

### `regenerate-selective.sh`

**Purpose**: Provides flexible, user-controlled regeneration of specific templates.

**Features**:
- Interactive menu system
- Command-line argument support
- Individual template selection
- Help and listing options
- Batch processing capabilities

**Usage Examples**:
```bash
# Show available templates
./regenerate-selective.sh --list

# Interactive mode (recommended)
./regenerate-selective.sh --interactive

# Direct template specification
./regenerate-selective.sh tracking customization

# Multiple templates
./regenerate-selective.sh data-persistence investment-protection

# Help information
./regenerate-selective.sh --help
```

**Interactive Mode Features**:
- Numbered menu selection
- Multiple template selection (e.g., "1,3,4")
- "all" option for complete regeneration
- Confirmation prompts
- Real-time feedback

### `cleanup-templates.sh`

**Purpose**: Safely removes template-generated files from the project.

**⚠️ DANGER**: This script permanently deletes files. Use with extreme caution.

**Features**:
- Interactive safety prompts
- Multiple confirmation levels
- Selective cleanup options
- Force mode for automated operations
- Detailed removal reporting

**Usage Examples**:
```bash
# Interactive cleanup (safest)
./cleanup-templates.sh --interactive

# Specific template cleanup
./cleanup-templates.sh tracking

# Multiple templates
./cleanup-templates.sh customization data-persistence

# List cleanup targets (no actual removal)
./cleanup-templates.sh --list

# DANGEROUS: Force removal of all template files
./cleanup-templates.sh --all --force
```

**Cleanup Targets by Template**:
- **tracking**: `Admin-Local/1-CurrentProject/Tracking/`
- **customization**: `app/Custom/`, `resources/Custom/`, config files, `webpack.custom.cjs`
- **data-persistence**: `.env.backup*`, `storage/persistence/`, `DATA_PERSISTENCE.md`
- **investment-protection**: `.investment-tracking/`, `INVESTMENT_TRACKING.md`

## 🛡️ Safety Features

### Multi-Level Confirmations
1. **Initial Warning**: Clear indication of script purpose
2. **Interactive Prompts**: Step-by-step confirmation for each operation
3. **Final Confirmation**: Ultimate safety check before irreversible operations
4. **Force Mode Protection**: Requires explicit `--force` flag for dangerous operations

### Error Handling
- **Path Validation**: Automatic project root and template directory detection
- **File Existence Checks**: Verification before attempting operations
- **Permission Validation**: Ensures scripts are executable
- **Graceful Failures**: Continues with remaining operations after individual failures

### Logging and Reporting
- **Timestamped Logs**: All operations include timestamps
- **Color-coded Output**: Success (green), warnings (yellow), errors (red)
- **Summary Reports**: Final statistics for all operations
- **Progress Tracking**: Real-time feedback during execution

## 🔧 Troubleshooting

### Common Issues

#### Script Not Found
```bash
# Make scripts executable
chmod +x *.sh

# Or individually
chmod +x regenerate-all-templates.sh
chmod +x regenerate-selective.sh
chmod +x cleanup-templates.sh
```

#### Permission Denied
```bash
# Ensure you're in the correct directory
pwd
# Should show: .../9-Master-Scripts

# Check script permissions
ls -la *.sh
```

#### Project Root Not Detected
The scripts search up to 5 directory levels for the `Admin-Local` directory. If detection fails:

```bash
# Navigate closer to project root
cd /path/to/your/project/root

# Or run from the master scripts directory
cd Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/9-Master-Scripts/
```

#### Template Script Failures
Individual template scripts may fail due to:
- Missing dependencies
- File permission issues
- Incomplete previous installations

**Solution**: Check the specific template's setup script and requirements.

### Debug Mode
For detailed troubleshooting, you can modify scripts to add debug output:

```bash
# Add this line near the top of any script
set -x  # Enable debug mode
```

## 🔄 Integration with Workflows

### CI/CD Integration
```yaml
# Example GitHub Actions integration
- name: Regenerate Templates
  run: |
    cd Admin-Local/0-Admin/zaj-Guides/0-General/1-Templates/9-Master-Scripts/
    ./regenerate-all-templates.sh
```

### Development Workflow
1. **Initial Setup**: Use `regenerate-all-templates.sh`
2. **Feature Development**: Use `regenerate-selective.sh` for specific systems
3. **Testing**: Use individual template scripts for debugging
4. **Cleanup**: Use `cleanup-templates.sh` for environment reset

### Maintenance Schedule
- **Weekly**: Verify all templates are current
- **Before Major Updates**: Full regeneration
- **After Template Changes**: Selective regeneration
- **Environment Cleanup**: As needed for testing

## 📖 Template System Overview

The master scripts manage these template systems:

### 5-Tracking-System
- **Purpose**: Linear, ADHD-friendly project tracking
- **Files Generated**: Tracking directories, session templates
- **Dependencies**: None (foundation system)

### 6-Customization-System
- **Purpose**: Protected customization framework
- **Files Generated**: Custom directories, service providers, webpack config
- **Dependencies**: Tracking system for logging

### 7-Data-Persistence-System
- **Purpose**: Zero-loss data management during updates
- **Files Generated**: Persistence directories, backup scripts
- **Dependencies**: Tracking system for change logging

### 8-Investment-Protection-System  
- **Purpose**: Documentation and ROI tracking
- **Files Generated**: Investment tracking, documentation generation
- **Dependencies**: All other systems for comprehensive tracking

## 🎯 Best Practices

### When to Use Each Script

**Use `regenerate-all-templates.sh` when**:
- Setting up a new project
- Major system updates
- Template system changes
- Recovery from corrupted state

**Use `regenerate-selective.sh` when**:
- Working with specific features
- Testing template changes
- Incremental development
- Debugging specific systems

**Use `cleanup-templates.sh` when**:
- Switching between project branches
- Testing clean installations
- Preparing for template updates
- Resolving file conflicts

### Safety Recommendations

1. **Always backup** before cleanup operations
2. **Use interactive modes** for safety
3. **Test in development** before production use
4. **Verify outputs** after regeneration
5. **Document customizations** that might be affected

## 📞 Support

For issues or questions:

1. Check the troubleshooting section above
2. Review individual template documentation
3. Verify file permissions and project structure
4. Check logs for specific error messages

---

**Last Updated**: $(date)  
**Version**: 4.0  
**Compatibility**: Template System v4.0+