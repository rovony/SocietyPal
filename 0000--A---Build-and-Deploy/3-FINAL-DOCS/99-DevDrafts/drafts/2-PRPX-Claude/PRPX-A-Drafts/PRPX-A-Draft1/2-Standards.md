# Laravel Deployment Standards

**Version:** 2.0  
**Generated:** August 20, 2025  
**Purpose:** Ensure consistency across all Master Checklist files

---

## **Step Format Standards**

### **Required Format for All Steps**
```markdown
### Step [ID]: [Name] [step-id]
**Location:** 🟢/🟡/🔴 Run on [Location]
**Path:** `%path-variable%`
**Purpose:** [Clear explanation of what this step accomplishes]

#### **Action Steps:**
1. **[Substep Name]**
   ```bash
   # Commands here
   ```
   
   **Expected Result:**
   ```
   ✅ [What success looks like]
   ❌ [What failure looks like]
   ```
```

### **Visual Identification Tags**
- 🟢 **Local Machine**: Developer's local environment
- 🟡 **Builder VM**: Dedicated build server/CI environment
- 🔴 **Server**: Production server environment
- 🟣 **User Hooks**: Configurable commands
  - 1️⃣ **Pre-release Hook**: Before deployment switch
  - 2️⃣ **Mid-release Hook**: During deployment process
  - 3️⃣ **Post-release Hook**: After deployment completion
- 🏗️ **Builder Commands**: Build-specific operations

### **Path Variables System**
- `%path-localMachine%`: Local development machine path
- `%path-server%`: Production server deployment path
- `%path-Builder-VM%`: Build environment path
- `%path-public%`: Public web directory path

---

## **Admin-Local Structure Standards**

### **Required Directory Structure**
```
Admin-Local/
├── 0-Admin/                    # Universal tools and guides
├── 1-CurrentProject/           # Project-specific tracking
├── Deployment/
│   ├── Scripts/               # All deployment scripts
│   ├── Configs/               # Configuration files (deployment-variables.json)
│   ├── EnvFiles/              # Environment-specific files (.env.production)
│   ├── Logs/                  # Analysis and deployment logs
│   └── Backups/               # Emergency backups
```

---

## **Script Naming Conventions**

### **Analysis Scripts**
- `comprehensive-env-check.sh` - Complete environment analysis
- `universal-dependency-analyzer.sh` - Enhanced dependency detection
- `install-analysis-tools.sh` - Setup all detection tools
- `pre-deployment-validation.sh` - 10-point validation checklist

### **Build Scripts**
- `load-variables.sh` - Load deployment configuration
- `configure-build-strategy.sh` - Setup build strategy
- `execute-build-strategy.sh` - Execute build process
- `validate-build-output.sh` - Verify build integrity

### **Deployment Scripts**
- `setup-composer-strategy.sh` - Composer production config
- `run-full-analysis.sh` - Comprehensive analysis runner

---

## **Required Elements for All Steps**

### **Metadata Requirements**
1. **Step ID**: Short identifier in brackets [step-id]
2. **Location Tag**: Visual indicator (🟢🟡🔴🟣)
3. **Path Variable**: Dynamic path reference
4. **Purpose Statement**: Clear explanation
5. **Expected Results**: Success/failure indicators

### **Script Requirements**
1. **Error Handling**: All scripts must handle failures gracefully
2. **Variables Loading**: Use `source Admin-Local/Deployment/Scripts/load-variables.sh`
3. **Progress Indicators**: Clear status messages (✅❌⚠️)
4. **Verification Steps**: Validate each operation
5. **Logging**: Save detailed logs to Admin-Local/Deployment/Logs/

### **Universal Compatibility**
1. **Laravel Versions**: Support 8, 9, 10, 11, 12
2. **Frontend Frameworks**: Blade, Vue, React, Inertia
3. **Build Systems**: Laravel Mix, Vite
4. **Hosting Types**: Dedicated, VPS, Shared
5. **Deployment Scenarios**: First-time and subsequent deployments

---

## **Quality Control Checklist**

### **Pre-Publication Requirements**
- [ ] All steps use standardized format
- [ ] All paths use variables, not hardcoded values
- [ ] All scripts include error handling
- [ ] All steps have expected results
- [ ] All visual tags are correctly applied
- [ ] All commands are tested and verified
- [ ] All dependencies are properly detected
- [ ] All edge cases are handled

### **Consistency Requirements**
- [ ] Admin-Local structure is identical across sections
- [ ] Path variables are used throughout
- [ ] Visual identification tags are consistent
- [ ] Script naming follows conventions
- [ ] Error messages are helpful and clear
- [ ] Success indicators are specific and measurable

This standards document ensures all Master Checklist files maintain consistency and quality across the entire deployment system.