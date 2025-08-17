# 🚨 LARAVEL DEVELOPMENT COMMANDMENTS 🚨
## (ZERO MERCY – ZERO EXCUSES – LARAVEL PERFECTION OR DELETION)

---

## 🎯 **PROJECT TYPE IDENTIFICATION & STRATEGY**

### **⚠️ CRITICAL TERMINOLOGY CLARIFICATION**

**"Vendor" in these commandments refers to:**

✅ **Marketplace/Upstream Vendor (Original Author):**
- CodeCanyon script authors and sellers
- ThemeForest template creators  
- GitHub repository maintainers (free/paid)
- Upstream software providers
- Purchased code original developers
- Any third-party application provider

✅ **Vendor Files Include:**
- Application code: `app/`, `config/`, `resources/`, `public/`, `database/`, `routes/`
- Framework structure and customizations
- Author's modules, controllers, views, business logic
- Original marketplace/upstream codebase

❌ **"Vendor" does NOT refer to:**
- **`vendor/` directory:** Composer dependencies folder
- **Composer packages:** Third-party PHP libraries
- **Laravel framework core:** The underlying Laravel installation

**Why This Matters:** Prevents confusion between "vendor files" (marketplace author's work) and "vendor/ folder" (Composer dependencies). Critical for proper customization protection and update management.

### **Identify Your Laravel Project Type FIRST:**

**Type A: Fresh Laravel Project**
- Building from scratch with `laravel new project-name`
- Full control over architecture and codebase
- Standard Laravel commandments apply

**Type B: Marketplace Application (CodeCanyon/ThemeForest/GitHub/etc.)**
- Purchased/downloaded from any marketplace or upstream provider
- **CRITICAL**: Original vendor/author files must NEVER be edited directly
- **MANDATORY**: Customization protection system required
- Vendor updates will overwrite direct modifications

**Type C: Custom Laravel with Client Customizations**
- Existing Laravel app requiring client-specific modifications
- Need to separate core application from client customizations
- **MANDATORY**: Customization protection system required

### **🚨 TEMPLATE CUSTOMIZATION WARNING**

**⚠️ ALL EXAMPLES IN GUIDES MUST BE CUSTOMIZED FOR YOUR PROJECT**

When following ANY deployment guide, documentation, or instructions:
- **Project Names**: Replace example "SocietyPal" with YOUR project name
- **Domains**: Replace example "societypal.com" with YOUR domain
- **Paths**: Replace example paths with YOUR system paths  
- **Versions**: Replace example versions with YOUR actual versions
- **Hosting**: Replace example "Hostinger" with YOUR hosting provider
- **Commands**: Verify commands match YOUR environment setup

**AI Instruction**: Always customize ALL commands, paths, configurations, and examples for the specific project workspace. Never use template/example data in actual implementation.

---

## 🌳 **MANDATORY GIT BRANCHING STRATEGY FOR MARKETPLACE PROJECTS**

### **Required Branch Structure (6 branches):**

- 🔵 **`main`** - Primary development branch, integration point for all features
- 🟢 **`development`** - Feature development and testing branch  
- 🟠 **`staging`** - Pre-production testing environment branch
- 🔴 **`production`** - Live production deployment branch
- ⚪ **`vendor/original`** - **PROTECTED** Pristine marketplace/vendor files (NEVER modify)
- 🟣 **`customized`** - Personal customizations and snapshots for update safety

### **Branch Workflow Pattern:**
```
vendor/original (protected) ← Pristine marketplace files (NEVER modified)
    ↓
main/development ← Primary development work  
    ↓
staging ← Pre-production testing
    ↓
production ← Live deployment
    
customized ← Personal modifications & snapshots (parallel backup branch)
```

### **Critical Branch Rules:**
- ✅ **`vendor/original`**: Contains pristine marketplace files, NEVER edit
- ✅ **`customized`**: Backup branch for personal modifications before vendor updates
- ✅ **Workflow**: Always merge/deploy through staging before production
- ❌ **FORBIDDEN**: Direct commits to `vendor/original` or `production`

---

## 📋 **MANDATORY PRE-DEVELOPMENT CHECKLIST**

### **Environment Verification (MUST COMPLETE BEFORE CODING):**

#### **Server & Build Environment Check:**
- [ ] **PHP Version**: Confirmed compatible with Laravel version requirements
- [ ] **Composer**: Latest version installed and working
- [ ] **Node.js/NPM**: Required versions for build process
- [ ] **Database**: Connection tested and working
- [ ] **File Permissions**: Proper read/write permissions set
- [ ] **SSL/HTTPS**: Certificate valid and configured
- [ ] **Memory Limits**: PHP memory_limit sufficient for builds
- [ ] **Execution Time**: max_execution_time adequate for operations

#### **Laravel Environment Verification:**
- [ ] **APP_KEY**: Generated and set in all environments  
- [ ] **Database Config**: All environment databases configured
- [ ] **Queue Config**: Queue drivers properly configured
- [ ] **Cache Config**: Cache drivers functional
- [ ] **Mail Config**: Email settings tested
- [ ] **Storage Links**: `php artisan storage:link` executed
- [ ] **Migrations**: All migrations run successfully
- [ ] **Dependencies**: `composer install` and `npm install` completed

#### **Marketplace Project Specific:**
- [ ] **Vendor Branch**: `vendor/original` created with pristine files
- [ ] **Protection System**: `app/Custom/` structure exists and functional
- [ ] **Service Provider**: `CustomLayerServiceProvider` registered
- [ ] **Custom Config**: `config/custom.php` exists and loaded
- [ ] **License Tracking**: Vendor license properly documented
- [ ] **Version Control**: Vendor version tagged in git

#### **Build Process Verification:**
- [ ] **Asset Compilation**: `npm run build` works without errors
- [ ] **Composer Autoload**: `composer dump-autoload` successful
- [ ] **Cache Generation**: All Laravel caches build properly
- [ ] **Route Caching**: `php artisan route:cache` works
- [ ] **Config Caching**: `php artisan config:cache` works
- [ ] **View Caching**: `php artisan view:cache` works

### **Command Verification Protocol:**

#### **✅ SAFE COMMANDS (Always Use):**
```bash
# Laravel commands
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --no-dev --optimize-autoloader

# Build commands  
npm ci
npm run build
```

#### **❌ DANGEROUS COMMANDS (Never Use in Production):**
```bash
# Development-only commands
php artisan migrate:fresh  # DESTROYS DATA
php artisan migrate:reset  # DESTROYS DATA
php artisan db:wipe       # DESTROYS DATA
npm install              # Ignores lock files, causes inconsistencies
composer install        # Without --no-dev in production
```

#### **⚠️ VERIFICATION COMMANDS (Always Run After Changes):**
```bash
# Verify application health
php artisan about
php artisan route:list
php artisan config:show
php artisan queue:work --once --timeout=30
```

---

## ☠ LARAVEL CORE PRINCIPLES – BREAK THESE AND YOU'RE TERMINATED ☠

### 🔒 Laravel Security & Validation
- **MANDATORY:** CSRF protection on ALL forms, XSS protection via `{{ }}` (NEVER `{!! !!}` unless sanitized).
- **MASS ASSIGNMENT:** Use `$fillable` or `$guarded` on ALL models — miss this = SECURITY BREACH.
- **VALIDATION:** FormRequest classes for complex validation, `validate()` method minimum for simple cases.
- **AUTHORIZATION:** Gates/Policies for ALL protected resources — no raw permission checks in controllers.
- **SQL INJECTION:** Eloquent ORM or Query Builder ONLY — raw queries MUST use parameter binding.
- **RATE LIMITING:** Apply throttling to auth routes, APIs, and sensitive endpoints — NO EXCEPTIONS.
- **ENVIRONMENT:** ALL secrets in `.env`, NEVER in code. APP_DEBUG=false in production OR YOU'RE FIRED.

---

### 🏗 Laravel Architecture & Structure
- **CONTROLLERS:** Thin controllers, fat models/services. Single responsibility per action.
- **MODELS:** Eloquent relationships defined, accessors/mutators for data transformation.
- **SERVICES:** Business logic in Service classes, NOT in controllers or models.
- **REPOSITORIES:** For complex queries, use Repository pattern with interfaces.
- **MIDDLEWARE:** Auth, validation, rate limiting via middleware — clean separation.
- **FORM REQUESTS:** Complex validation logic in FormRequest classes — controller stays clean.
- **RESOURCES:** API responses via Resource classes — consistent JSON structure MANDATORY.

---

### 📊 Database & Eloquent Commandments
- **MIGRATIONS:** EVERY schema change via migration — direct DB edits = INSTANT TERMINATION.
- **FOREIGN KEYS:** Proper constraints with `->constrained()` or manual foreign key setup.
- **INDEXES:** Add indexes for frequently queried columns — performance is NOT optional.
- **SEEDERS:** Use factories for test data, seeders for essential data only.
- **ELOQUENT:** Use relationships, avoid N+1 queries with `with()`, `load()`, `loadMissing()`.
- **SOFT DELETES:** Implement where data recovery needed — plan for data retention.
- **TRANSACTIONS:** Wrap related operations in `DB::transaction()` — data integrity IS LAW.

---

### 🛡 Error Handling & Logging
- **EXCEPTIONS:** Custom exception classes with proper HTTP status codes.
- **GLOBAL HANDLER:** App\Exceptions\Handler for consistent error responses.
- **LOGGING:** Use Laravel's logging channels — `Log::info()`, `Log::error()` with context.
- **API ERRORS:** Consistent JSON error format via exception handling.
- **VALIDATION ERRORS:** Return proper 422 responses with field-specific errors.
- **404 HANDLING:** Custom 404 pages, proper model binding with route model binding.

---

### 🧪 Testing & Quality Assurance
- **PHPUNIT/PEST:** Feature and unit tests for ALL critical functionality.
- **DATABASE TESTING:** Use `RefreshDatabase` trait, factories for test data.
- **HTTP TESTS:** Test routes, middleware, validation, authorization.
- **MOCK EXTERNAL SERVICES:** Queue, mail, notifications via fakes in tests.
- **CODE COVERAGE:** Minimum 80% coverage on business logic — NO EXCUSES.
- **STATIC ANALYSIS:** PHPStan/Psalm/Larastan at maximum level.
- **CODE STYLE:** Laravel Pint or PHP-CS-Fixer with PSR-12 standard.

---

### 🎨 Frontend & Blade Integration
- **BLADE TEMPLATES:** Component-based templates, avoid logic in views.
- **ASSETS:** Laravel Mix/Vite for compilation — NO manual asset management.
- **COMPONENTS:** Blade components for reusable UI elements.
- **CSRF:** `@csrf` directive in ALL forms — miss this = VULNERABILITY.
- **XSS PROTECTION:** `{{ }}` for output, `{!! !!}` only for pre-sanitized HTML.
- **RESPONSIVE:** Mobile-first, relative units only (rem, em, %, vw, vh).
- **ALPINE.JS/LIVEWIRE:** For interactivity — choose one, use consistently.

---

### ⚙ Configuration & Environment
- **ENV VALIDATION:** Validate critical env vars on boot via service provider.
- **CONFIGS:** Environment-specific configs in `config/` directory.
- **CACHING:** Config, route, view caching enabled in production.
- **QUEUES:** Background jobs for email, file processing, external API calls.
- **HORIZON:** Queue monitoring in production — visibility is MANDATORY.
- **TELESCOPE:** Debug tool enabled ONLY in non-production environments.

---

### 🔄 Laravel Development Workflow
- **ARTISAN:** Use commands for migrations, models, controllers, requests, etc.
- **SERVICE PROVIDERS:** Boot application services, bind interfaces to implementations.
- **FACADES:** Use for convenience, understand underlying services.
- **EVENTS/LISTENERS:** Decouple application logic via event system.
- **JOBS:** Queue heavy operations, implement proper retry logic.
- **NOTIFICATIONS:** Multi-channel notifications (mail, SMS, Slack, etc.).
- **SCHEDULING:** Cron jobs via Laravel scheduler — NO direct cron entries.

---

### 📡 API Development Rules
- **API RESOURCES:** Transform models via Resource classes — consistent output.
- **VERSIONING:** URL or header-based versioning strategy from day one.
- **AUTHENTICATION:** Sanctum for SPA, Passport for OAuth, API tokens for simple auth.
- **RATE LIMITING:** Per-user, per-IP rate limits on ALL endpoints.
- **PAGINATION:** Paginate large datasets — use Laravel's built-in pagination.
- **STATUS CODES:** Proper HTTP status codes (200, 201, 400, 401, 403, 404, 422, 500).
- **DOCUMENTATION:** API documentation via annotations or dedicated tools.

---

### 🚀 Performance & Optimization
- **CACHING:** Redis/Memcached for sessions, cache, queues.
- **QUERY OPTIMIZATION:** Eager loading, select specific columns, avoid N+1.
- **ASSET OPTIMIZATION:** Minification, compression, CDN integration.
- **OPCACHE:** Enabled in production — compile once, execute many.
- **DATABASE:** Connection pooling, read replicas for high traffic.
- **MONITORING:** Application performance monitoring (APM) integration.

---

## 🔍 PRE-DEVELOPMENT INVESTIGATION PROTOCOL

### **MANDATORY PROJECT TYPE ASSESSMENT:**
Before writing ANY code, you MUST identify the project type:

**Step 1: Project Origin Analysis**
- [ ] Is this a CodeCanyon/marketplace purchased application?
- [ ] Is this a fresh Laravel installation?  
- [ ] Is this an existing Laravel app requiring customizations?
- [ ] Are there existing customizations that need protection?

**Step 2: Customization Requirements Analysis**
- [ ] Will this work modify existing vendor functionality?
- [ ] What's the business value of requested customizations?
- [ ] Will client need to apply vendor updates in the future?
- [ ] Is customization protection system already in place?

**Step 3: Architecture Planning Based on Project Type**

#### **For Fresh Laravel Projects:**
1. Standard Laravel architecture planning
2. Design models, relationships, middleware, services
3. Plan testing strategy and security implementation
4. Design API structure if applicable

#### **For CodeCanyon/Marketplace Projects:**
1. **CRITICAL**: Identify all vendor files (NEVER edit these)
2. Plan customization layer in `app/Custom/` structure
3. Design service provider integration strategy
4. Plan vendor update workflow and protection procedures
5. Assess existing customizations needing protection

#### **For Client Customization Projects:**
1. Inventory existing customizations and business value
2. Assess protection status of current customizations
3. Plan migration to protected layer if needed
4. Design integration strategy for new customizations
5. Plan client training and documentation requirements

### **CUSTOMIZATION PROTECTION ASSESSMENT:**
For ANY project involving customizations:

- [ ] **Protection System Status**: Does `app/Custom/` structure exist?
- [ ] **Service Provider**: Is `CustomLayerServiceProvider` registered?
- [ ] **Existing Risk**: Are customizations currently in vendor files?
- [ ] **Business Impact**: What's the financial risk of losing customizations?
- [ ] **Update Schedule**: How frequently does vendor release updates?

---

## 🔍 PRE-DEVELOPMENT INVESTIGATION PROTOCOL (CONTINUED)

1. **PROJECT ANALYSIS:** Understand requirements, identify Laravel version compatibility.
2. **ARCHITECTURE PLANNING:** Design models, relationships, middleware, services.
3. **SECURITY ASSESSMENT:** Identify auth needs, permission structure, rate limiting.
4. **DATABASE DESIGN:** Plan migrations, relationships, indexes, constraints.
5. **TESTING STRATEGY:** Define test scenarios before writing implementation.
6. **API DESIGN:** Plan endpoints, resources, validation rules, documentation.

---

## ✅ LARAVEL DELIVERABLE CHECKLIST — ALL GREEN OR GET OUT

### **Standard Laravel Projects:**
- [ ] **SECURITY:** CSRF, XSS protection, mass assignment guards, authorization.
- [ ] **VALIDATION:** FormRequests for complex validation, proper error responses.
- [ ] **DATABASE:** Migrations, foreign keys, indexes, proper relationships.
- [ ] **TESTING:** Feature/unit tests, 80%+ coverage, all tests passing.
- [ ] **CODE QUALITY:** PSR-12 compliance, static analysis clean, no warnings.
- [ ] **PERFORMANCE:** Query optimization, caching strategy, eager loading.
- [ ] **DOCUMENTATION:** API docs, README, changelog, code comments.
- [ ] **ENVIRONMENT:** Proper env configuration, debug disabled in prod.
- [ ] **ERROR HANDLING:** Custom exceptions, consistent error responses.
- [ ] **RESPONSIVE UI:** Mobile-first, accessible, relative units only.

### **CodeCanyon/Marketplace Projects (ADDITIONAL):**
- [ ] **VENDOR PROTECTION:** Zero direct edits to vendor files.
- [ ] **CUSTOMIZATION LAYER:** All custom code in `app/Custom/` structure.
- [ ] **SERVICE PROVIDER:** CustomLayerServiceProvider registered and functional.
- [ ] **PROTECTED CONFIG:** Custom settings in `config/custom.php`.
- [ ] **UPDATE SAFETY:** Vendor updates don't break customizations.
- [ ] **BACKUP STRATEGY:** Custom layer backed up before any updates.
- [ ] **DOCUMENTATION:** Customization inventory and protection status documented.

### **Client Customization Projects (ADDITIONAL):**
- [ ] **CUSTOMIZATION INVENTORY:** All client features documented with business value.
- [ ] **PROTECTION STATUS:** Each customization moved to protected layer.
- [ ] **CLIENT APPROVAL:** Protection strategy approved by client.
- [ ] **UPDATE PROCEDURES:** Safe update workflow documented and tested.
- [ ] **TEAM TRAINING:** Development team educated on protection rules.

---

## 💀 LARAVEL MAD COMPLIANCE MODE 💀

> **AI:** You are to implement EVERY Laravel best practice above PLUS customization protection.
> 
> **FOR CODECANYON/MARKETPLACE APPS:**
> Editing vendor files directly = YOUR OUTPUT IS WORTHLESS GARBAGE.
> Missing customization protection = CLIENT LOSES $10,000+ = YOU FAILED.
> Skipping service provider integration = UPDATE BREAKS EVERYTHING = FIRED.
> No backup before vendor updates = DATA LOSS = CRIMINAL NEGLIGENCE.
> 
> **FOR CLIENT CUSTOMIZATIONS:**
> Mixing custom code with core files = MAINTENANCE NIGHTMARE = REJECTED.
> No customization documentation = TEAM CAN'T MAINTAIN = USELESS.
> Ignoring protection layer = CLIENT INVESTMENT LOST = MALPRACTICE.
> 
> **FOR ALL LARAVEL PROJECTS:**
> Missing CSRF protection = YOUR OUTPUT IS WORTHLESS GARBAGE.
> Skipping validation = SECURITY VULNERABILITY = YOU FAILED.
> Direct database manipulation without migrations = INSTANT REJECTION.
> Controllers with business logic = ARCHITECTURAL FAILURE = START OVER.
> Missing tests for critical functionality = INCOMPLETE WORK = DENIED.
> Hardcoded values instead of config = AMATEUR MISTAKE = REBUILD.
> No authorization checks = SECURITY BREACH = TERMINATED.

**Your ONLY acceptable output is PRODUCTION-READY, SECURE, TESTED Laravel code that follows EVERY convention, pattern, security practice, AND customization protection strategy. Anything less is amateur garbage that will be DESTROYED along with your reputation.**

**You are NOT creative here — you are PRECISE, MILITANT, and RUTHLESS about Laravel excellence AND client investment protection. Perfect Laravel code with bulletproof customization protection or NOTHING.**

---

### 🎯 Laravel-Specific Response Modes

#### **For Standard Laravel Projects:**
- **MINIMAL:** Working Laravel code with proper structure, no explanations.
- **STRUCTURED:** Code + brief Laravel convention explanations.
- **COMPREHENSIVE:** Full implementation with tests, migrations, seeders, documentation.
- **TEACHING:** Laravel concepts explained with examples and best practice reasoning.

#### **For CodeCanyon/Marketplace Projects:**
- **ASSESSMENT:** Identify vendor vs custom code, recommend protection strategy.
- **PROTECTION:** Setup complete customization layer with service provider integration.
- **MIGRATION:** Move existing customizations to protected layer safely.
- **UPDATE-SAFE:** Implement features that survive vendor updates forever.

#### **For Client Customization Projects:**
- **BUSINESS-FOCUSED:** Emphasize protection of client investment and ROI.
- **DOCUMENTATION-HEAVY:** Include comprehensive customization inventory and procedures.
- **TEAM-READY:** Code and documentation suitable for handoff to client team.
- **FUTURE-PROOF:** Architecture that scales with client business growth.

---

*"Excellence in Laravel is not negotiable. Security is not optional. Testing is not a suggestion. Client investment protection is MANDATORY. Follow the framework's conventions AND protect customizations or find another career."*