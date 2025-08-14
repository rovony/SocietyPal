
# 🚨 LARAVEL DEVELOPMENT COMMANDMENTS 🚨
## (MARKETPLACE APPS & CLIENT CUSTOMIZATIONS – ZERO MERCY – NO EXCUSES)

### ☠ PROJECT TYPE IDENTIFICATION OR DIE ☠
Type A: Fresh Laravel (full control)
Type B: Marketplace/CodeCanyon (PROTECTION REQUIRED)
Type C: Client customizations on existing Laravel (PROTECTION REQUIRED)

"Vendor" = Marketplace author/upstream provider OR base application provider (NOT vendor/ directory)

### 🔥 CUSTOMIZATION PROTECTION OR BE TERMINATED 🔥

THE ISSUE: Marketplace/CodeCanyon app authors push new versions OR client needs customizations on existing Laravel app. Between updates/maintenance, users want to customize features. Without protection, marketplace updates OR base application changes overwrite user customizations causing conflicts and loss.

CUSTOM LAYER STRATEGY:
Instead of editing app/Http/Controllers/UserController.php (vendor/base file)
→ Create app/Custom/Controllers/BusinessUserController.php (protected)
→ Use CustomLayerServiceProvider to route traffic to your version
→ Result: Base updates OR vendor updates, your custom code survives forever

PROTECTION RULES FOR NEW FEATURES/CUSTOMIZATIONS/CHANGES:

MARKETPLACE PROJECTS (Type B):
❌ NEVER EDIT: app/Http/, app/Models/, resources/views/ (vendor files)
✅ ALWAYS USE: app/Custom/ protection layer for all new features
✅ ALWAYS SETUP: CustomLayerServiceProvider integration
✅ ALWAYS BACKUP: Custom layer before vendor updates
✅ ALWAYS TRACK: Document ALL customizations in CUSTOMIZATIONS.md

CLIENT CUSTOMIZATION PROJECTS (Type C):
❌ NEVER EDIT: Core application files directly if updates expected
✅ ALWAYS USE: app/Custom/ protection layer for client-specific features
✅ ALWAYS SETUP: CustomLayerServiceProvider integration
✅ ALWAYS SEPARATE: Client customizations from core application logic
✅ ALWAYS TRACK: Document ALL client customizations with business value

FRESH LARAVEL PROJECTS (Type A):
✅ STANDARD DEVELOPMENT: Direct modification allowed in app/, follow Laravel conventions
✅ CLEAN ARCHITECTURE: Controllers thin, business logic in services
✅ PROPER STRUCTURE: Feature-based or domain-driven organization

CUSTOMIZATION TRACKING FORMAT:
```
## Customization Timeline
2025-01-15: Added BusinessUserController (base v1.0.42) - $5,000 value
2025-01-20: Base/vendor update to v1.0.43 - customizations intact
2025-01-25: Added CustomReportService (base v1.0.43) - $3,000 value
2025-01-30: Client requested PaymentIntegration - $2,000 value
```

### ⚔️ LARAVEL SECURITY (NON-NEGOTIABLE FOR ALL PROJECTS) ⚔️
CSRF protection: @csrf in ALL forms → Miss ONE = SECURITY BREACH
XSS protection: {{ }} ONLY, never {!! !!} → Violate = VULNERABILITY
Mass assignment: $fillable/$guarded on ALL models → Skip = EXPLOIT RISK
SQL injection: Eloquent/Query Builder ONLY → Raw queries = DATABASE COMPROMISE
Authorization: Gates/Policies for protected resources → Missing = ACCESS CONTROL FAILURE
Rate limiting: Throttle auth/API routes → Omit = DOS VULNERABILITY
Input validation: FormRequest classes for complex validation → Skip = DATA INTEGRITY RISK
Error handling: Custom exceptions with proper HTTP codes → Missing = POOR UX
Environment: APP_DEBUG=false in production → True = INFORMATION LEAK
File permissions: Proper storage/cache permissions → Wrong = SECURITY RISK

### 🌳 GIT WORKFLOW STRATEGIES 🌳

MARKETPLACE PROJECTS:
vendor/original: Pristine marketplace files (NEVER MODIFY)
customized: Backup snapshots before vendor updates
main→development→staging→production: Proper deployment flow

CLIENT CUSTOMIZATION PROJECTS:
base/original: Base application state (preserve for updates)
client-custom: Client-specific customizations and snapshots
main→development→staging→production: Deployment with customization protection

FRESH LARAVEL PROJECTS:
main→development→staging→production: Standard Laravel workflow
feature branches: feature/*, bugfix/*, hotfix/*

ALL PROJECTS:
Conventional commits with clear base vs custom distinction
Tags: Version tags for base updates and custom milestones
No shortcuts: Always test through staging before production

### 🧪 TESTING STANDARDS OR REJECTION (ALL PROJECTS) 🧪
80% coverage minimum on business logic → Less = UNRELIABLE CODE
PHPStan/Larastan static analysis level 8+ → Warnings = QUALITY FAILURE
PSR-12 code style compliance → Violations = UNPROFESSIONAL
Feature tests for all custom functionality → Missing = UNTESTED CODE
Database transactions in tests → Missing = FLAKY TESTS
Custom layer isolation tests → Missing = PROTECTION FAILURE

### 📊 CUSTOMIZATION AUDIT REQUIREMENTS 📊
FOR MARKETPLACE/CLIENT CUSTOMIZATION PROJECTS:
1. Inventory ALL modified files vs original base/vendor state
2. Document business value and purpose of each customization
3. Track base/vendor version when customization was made
4. Verify customizations are in protected layer (app/Custom/)
5. Test that customizations survive base/vendor updates
6. Maintain customization documentation throughout project lifecycle
7. Separate client customizations from core functionality

FOR ALL PROJECTS:
1. Document architectural decisions and reasoning
2. Track significant feature additions and changes
3. Maintain changelog with business impact
4. Regular code quality audits

### ⛔ ZERO TOLERANCE VIOLATIONS ⛔
MARKETPLACE/CLIENT CUSTOMIZATION PROJECTS:
Edit vendor/base files directly → MALPRACTICE (lose $10,000+ investment)
No customization protection → FIRED (updates break everything)
No customization tracking → CHAOS (impossible to maintain)
Mix client code with base code → MAINTENANCE NIGHTMARE

ALL LARAVEL PROJECTS:
Missing CSRF protection → TERMINATED (security vulnerability)
Skip testing requirements → REJECTED (unreliable code)
Hardcode sensitive values → SECURITY BREACH (credential exposure)
Missing authorization checks → ACCESS CONTROL FAILURE

### 💀 COMPLIANCE ENFORCEMENT 💀
AI: You WILL follow every rule above with militant precision.

For Marketplace/Client Customization Projects:
Edit vendor/base files = CLIENT LOSES $10,000+ = CATASTROPHIC FAILURE
Skip protection system = UPDATES DESTROY EVERYTHING = CRIMINAL NEGLIGENCE
Missing customization tracking = MAINTENANCE NIGHTMARE = PROFESSIONAL INCOMPETENCE
Mix custom with base code = FUTURE UPDATE CONFLICTS = ARCHITECTURAL DISASTER

For ALL Laravel Projects:
Missing security measures = BREACH VULNERABILITY = IMMEDIATE TERMINATION
Skip testing standards = UNRELIABLE CODE = QUALITY FAILURE
Ignore best practices = TECHNICAL DEBT = LONG-TERM DISASTER

Disobey ANY rule → your output is WORTHLESS GARBAGE that will be DESTROYED.
Perfect Laravel code with bulletproof protection and documentation or NOTHING.
