# Step 04: Build Frontend Views

**Purpose:** Create custom Blade templates, CSS styling, and JavaScript interactions for the user interface while maintaining vendor code integrity.

**Duration:** 2-3 hours  
**Prerequisites:** Completed Step 03: Implement Core Features  

---

## Tracking Integration

```bash
# Detect project paths (project-agnostic)
if [ -d "Admin-Local" ]; then
    PROJECT_ROOT="$(pwd)"
elif [ -d "../Admin-Local" ]; then
    PROJECT_ROOT="$(dirname "$(pwd)")"
elif [ -d "../../Admin-Local" ]; then
    PROJECT_ROOT="$(dirname "$(dirname "$(pwd)")")"
else
    echo "Error: Cannot find Admin-Local directory"
    exit 1
fi

ADMIN_LOCAL="$PROJECT_ROOT/Admin-Local"
TRACKING_BASE="$ADMIN_LOCAL/1-CurrentProject/Tracking"

# Initialize session directory for this step
SESSION_DIR="$TRACKING_BASE/3-Customization-$(date +%Y%m%d_%H%M%S)"
mkdir -p "$SESSION_DIR"

# Create step-specific tracking files
STEP_PLAN="$SESSION_DIR/step04_frontend_views_plan.md"
STEP_BASELINE="$SESSION_DIR/step04_baseline.md"
STEP_EXECUTION="$SESSION_DIR/step04_execution.md"

# Initialize tracking files
echo "# Step 04: Build Frontend Views - Planning" > "$STEP_PLAN"
echo "**Date:** $(date)" >> "$STEP_PLAN"
echo "**Session:** $SESSION_DIR" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"

echo "# Step 04: Build Frontend Views - Baseline" > "$STEP_BASELINE"
echo "**Date:** $(date)" >> "$STEP_BASELINE"
echo "" >> "$STEP_BASELINE"

echo "# Step 04: Build Frontend Views - Execution Log" > "$STEP_EXECUTION"
echo "**Date:** $(date)" >> "$STEP_EXECUTION"
echo "" >> "$STEP_EXECUTION"

echo "Tracking initialized for Step 04 in: $SESSION_DIR"
```

---

## 4.1: Create Custom Blade Templates

### Planning Phase

```bash
# Update Step 04 tracking plan
echo "## 4.1: Create Custom Blade Templates" >> "$STEP_PLAN"
echo "- Design custom dashboard and settings pages" >> "$STEP_PLAN"
echo "- Create reusable Blade components and partials" >> "$STEP_PLAN"
echo "- Implement custom layouts extending vendor structure" >> "$STEP_PLAN"
echo "- Setup custom view composer and data binding" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"

# Record baseline
echo "## Section 4.1 Baseline" >> "$STEP_BASELINE"
echo "**Current State:** Before custom views implementation" >> "$STEP_BASELINE"
echo "**Existing Custom Views:** $(find resources/Custom/views -name "*.blade.php" 2>/dev/null | wc -l)" >> "$STEP_BASELINE"
echo "**Vendor Views:** $(find resources/views -name "*.blade.php" 2>/dev/null | wc -l)" >> "$STEP_BASELINE"
echo "" >> "$STEP_BASELINE"
```

### Required View Implementation:

**4.1.1: Core Custom Views**
- [ ] Custom dashboard main page with widgets and statistics
- [ ] Custom settings page with form controls and validation
- [ ] Custom user profile extensions and preferences
- [ ] Custom notification center and alerts system

**4.1.2: Reusable Components**
- [ ] Custom navigation components and menu extensions
- [ ] Custom form components with validation display
- [ ] Custom data table components with sorting and filtering
- [ ] Custom modal and popup components for interactions

### Execution Commands:

```bash
# Log execution start
echo "## Section 4.1 Execution" >> "$STEP_EXECUTION"
echo "**Create Custom Blade Templates Started:** $(date)" >> "$STEP_EXECUTION"

# Create custom dashboard view
cat > resources/Custom/views/dashboard/index.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Custom Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Custom Dashboard') }}</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Statistics Cards -->
                        @if(isset($stats))
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $stats['total_logins'] ?? 0 }}</h3>
                                    <p>Total Logins</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-log-in"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ $stats['settings_count'] ?? 0 }}</h3>
                                    <p>Custom Settings</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-gear-a"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ $stats['account_age_days'] ?? 0 }}</h3>
                                    <p>Account Age (Days)</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-calendar"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $stats['activity_score'] ?? 0 }}</h3>
                                    <p>Activity Score</p>
                                </div>
                                <div class="icon">
                                    <i class="ion ion-stats-bars"></i>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Recent Activities -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Recent Activities</h3>
                                </div>
                                <div class="card-body p-0">
                                    @if(isset($recent_activities) && count($recent_activities) > 0)
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Description</th>
                                                <th>Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recent_activities as $activity)
                                            <tr>
                                                <td>
                                                    <span class="badge badge-{{ $activity['type'] === 'login' ? 'success' : 'info' }}">
                                                        {{ ucfirst($activity['type']) }}
                                                    </span>
                                                </td>
                                                <td>{{ $activity['description'] }}</td>
                                                <td>{{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                    <div class="text-center p-4">
                                        <p class="text-muted">No recent activities found</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Quick Actions</h3>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group-vertical d-grid gap-2">
                                        <a href="{{ route('custom.settings') }}" class="btn btn-outline-primary">
                                            <i class="fas fa-cog"></i> Manage Settings
                                        </a>
                                        <button type="button" class="btn btn-outline-info" onclick="refreshDashboard()">
                                            <i class="fas fa-sync"></i> Refresh Dashboard
                                        </button>
                                        <button type="button" class="btn btn-outline-success" onclick="exportData()">
                                            <i class="fas fa-download"></i> Export Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function refreshDashboard() {
    showLoading();
    fetch('{{ route("api.custom.dashboard") }}', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': 'Bearer {{ auth()->user()->createToken("dashboard")->plainTextToken ?? "" }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showAlert('Error refreshing dashboard', 'danger');
        }
    })
    .catch(error => {
        console.error('Dashboard refresh error:', error);
        showAlert('Failed to refresh dashboard', 'danger');
    })
    .finally(() => {
        hideLoading();
    });
}

function exportData() {
    showAlert('Export feature coming soon', 'info');
}

function showLoading() {
    // Add loading spinner if needed
}

function hideLoading() {
    // Remove loading spinner if needed
}

function showAlert(message, type = 'info') {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    $('.container-fluid').prepend(alertHtml);
}
</script>
@endpush
EOF

echo "**SUCCESS: Custom dashboard view created**" >> "$STEP_EXECUTION"

# Create custom settings view
cat > resources/Custom/views/dashboard/settings.blade.php << 'EOF'
@extends('layouts.app')

@section('title', 'Custom Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Custom Settings') }}</h3>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('custom.settings.update') }}" id="settings-form">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <!-- Theme Settings -->
                                <div class="form-group">
                                    <label for="theme">{{ __('Theme Preference') }}</label>
                                    <select name="settings[theme]" id="theme" class="form-control">
                                        <option value="light" {{ (($settings['theme'] ?? 'light') === 'light') ? 'selected' : '' }}>
                                            Light Theme
                                        </option>
                                        <option value="dark" {{ (($settings['theme'] ?? 'light') === 'dark') ? 'selected' : '' }}>
                                            Dark Theme
                                        </option>
                                        <option value="auto" {{ (($settings['theme'] ?? 'light') === 'auto') ? 'selected' : '' }}>
                                            Auto (System)
                                        </option>
                                    </select>
                                </div>

                                <!-- Language Settings -->
                                <div class="form-group">
                                    <label for="language">{{ __('Language') }}</label>
                                    <select name="settings[language]" id="language" class="form-control">
                                        <option value="en" {{ (($settings['language'] ?? 'en') === 'en') ? 'selected' : '' }}>
                                            English
                                        </option>
                                        <option value="es" {{ (($settings['language'] ?? 'en') === 'es') ? 'selected' : '' }}>
                                            Spanish
                                        </option>
                                        <option value="fr" {{ (($settings['language'] ?? 'en') === 'fr') ? 'selected' : '' }}>
                                            French
                                        </option>
                                        <option value="de" {{ (($settings['language'] ?? 'en') === 'de') ? 'selected' : '' }}>
                                            German
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <!-- Notification Settings -->
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="hidden" name="settings[notifications]" value="0">
                                        <input class="form-check-input" type="checkbox" name="settings[notifications]" 
                                               id="notifications" value="1" 
                                               {{ (($settings['notifications'] ?? true)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="notifications">
                                            {{ __('Enable Notifications') }}
                                        </label>
                                    </div>
                                </div>

                                <!-- Timezone Settings -->
                                <div class="form-group">
                                    <label for="timezone">{{ __('Timezone') }}</label>
                                    <select name="settings[timezone]" id="timezone" class="form-control">
                                        @php
                                        $timezones = [
                                            'UTC' => 'UTC',
                                            'America/New_York' => 'Eastern Time',
                                            'America/Chicago' => 'Central Time',
                                            'America/Denver' => 'Mountain Time',
                                            'America/Los_Angeles' => 'Pacific Time',
                                            'Europe/London' => 'London',
                                            'Europe/Paris' => 'Paris',
                                            'Asia/Tokyo' => 'Tokyo'
                                        ];
                                        $currentTimezone = $settings['timezone'] ?? config('app.timezone');
                                        @endphp
                                        @foreach($timezones as $value => $label)
                                        <option value="{{ $value }}" {{ ($currentTimezone === $value) ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Current Settings Summary</h4>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-bordered">
                                            <tr>
                                                <td><strong>Theme:</strong></td>
                                                <td>{{ ucfirst($settings['theme'] ?? 'light') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Language:</strong></td>
                                                <td>{{ strtoupper($settings['language'] ?? 'en') }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Notifications:</strong></td>
                                                <td>{{ ($settings['notifications'] ?? true) ? 'Enabled' : 'Disabled' }}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Timezone:</strong></td>
                                                <td>{{ $settings['timezone'] ?? config('app.timezone') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Save Settings') }}
                            </button>
                            <button type="reset" class="btn btn-secondary">
                                <i class="fas fa-undo"></i> {{ __('Reset') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#settings-form').on('submit', function(e) {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    });

    // Preview theme changes
    $('#theme').on('change', function() {
        const theme = $(this).val();
        if (theme === 'dark') {
            $('body').addClass('dark-mode');
        } else {
            $('body').removeClass('dark-mode');
        }
    });
});
</script>
@endpush
EOF

echo "**SUCCESS: Custom settings view created**" >> "$STEP_EXECUTION"
echo "**Custom Blade templates implementation completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 4.2: Implement Custom CSS Styling

### Planning Phase

```bash
# Update Step 04 tracking plan
echo "## 4.2: Implement Custom CSS Styling" >> "$STEP_PLAN"
echo "- Create custom CSS styles that complement vendor theme" >> "$STEP_PLAN"
echo "- Implement responsive design for mobile compatibility" >> "$STEP_PLAN"
echo "- Setup CSS custom properties for theming support" >> "$STEP_PLAN"
echo "- Create utility classes for common custom patterns" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required CSS Implementation:

**4.2.1: Core Styling**
- [ ] Custom dashboard layout and widget styling
- [ ] Custom form controls and validation styling
- [ ] Custom navigation and menu extensions
- [ ] Custom modal and popup styling

**4.2.2: Responsive Design**
- [ ] Mobile-first responsive breakpoints
- [ ] Touch-friendly interface elements
- [ ] Adaptive grid system for custom components
- [ ] Progressive enhancement for older browsers

### Execution Commands:

```bash
# Log execution start
echo "## Section 4.2 Execution" >> "$STEP_EXECUTION"
echo "**Implement Custom CSS Styling Started:** $(date)" >> "$STEP_EXECUTION"

# Create custom CSS file
cat > resources/Custom/css/custom-dashboard.css << 'EOF'
/* Custom Dashboard Styles */
:root {
    --custom-primary-color: #007bff;
    --custom-success-color: #28a745;
    --custom-info-color: #17a2b8;
    --custom-warning-color: #ffc107;
    --custom-danger-color: #dc3545;
    --custom-secondary-color: #6c757d;
    --custom-light-color: #f8f9fa;
    --custom-dark-color: #343a40;
    --custom-border-radius: 0.375rem;
    --custom-box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    --custom-transition: all 0.15s ease-in-out;
}

/* Custom Dashboard Widgets */
.custom-dashboard-widget {
    background: var(--custom-light-color);
    border-radius: var(--custom-border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--custom-box-shadow);
    transition: var(--custom-transition);
}

.custom-dashboard-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.custom-dashboard-widget .widget-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--custom-dark-color);
    margin-bottom: 1rem;
}

.custom-dashboard-widget .widget-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.custom-dashboard-widget.primary .widget-value {
    color: var(--custom-primary-color);
}

.custom-dashboard-widget.success .widget-value {
    color: var(--custom-success-color);
}

.custom-dashboard-widget.info .widget-value {
    color: var(--custom-info-color);
}

.custom-dashboard-widget.warning .widget-value {
    color: var(--custom-warning-color);
}

/* Custom Settings Form */
.custom-settings-form {
    background: white;
    border-radius: var(--custom-border-radius);
    padding: 2rem;
    box-shadow: var(--custom-box-shadow);
}

.custom-form-group {
    margin-bottom: 1.5rem;
}

.custom-form-label {
    font-weight: 600;
    color: var(--custom-dark-color);
    margin-bottom: 0.5rem;
    display: block;
}

.custom-form-control {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ced4da;
    border-radius: var(--custom-border-radius);
    font-size: 1rem;
    transition: var(--custom-transition);
}

.custom-form-control:focus {
    outline: none;
    border-color: var(--custom-primary-color);
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.custom-form-control.is-invalid {
    border-color: var(--custom-danger-color);
}

.custom-form-control.is-invalid:focus {
    border-color: var(--custom-danger-color);
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Custom Buttons */
.custom-btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
    font-weight: 400;
    text-align: center;
    text-decoration: none;
    border: 1px solid transparent;
    border-radius: var(--custom-border-radius);
    cursor: pointer;
    transition: var(--custom-transition);
}

.custom-btn-primary {
    background-color: var(--custom-primary-color);
    border-color: var(--custom-primary-color);
    color: white;
}

.custom-btn-primary:hover {
    background-color: #0056b3;
    border-color: #004085;
    color: white;
}

.custom-btn-secondary {
    background-color: var(--custom-secondary-color);
    border-color: var(--custom-secondary-color);
    color: white;
}

.custom-btn-secondary:hover {
    background-color: #545b62;
    border-color: #4e555b;
    color: white;
}

/* Custom Activity Feed */
.custom-activity-feed {
    max-height: 400px;
    overflow-y: auto;
}

.custom-activity-item {
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
    transition: var(--custom-transition);
}

.custom-activity-item:hover {
    background-color: #f8f9fa;
}

.custom-activity-item:last-child {
    border-bottom: none;
}

.custom-activity-type {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    border-radius: 0.25rem;
    text-transform: uppercase;
}

.custom-activity-type.login {
    background-color: rgba(40, 167, 69, 0.1);
    color: var(--custom-success-color);
}

.custom-activity-type.settings {
    background-color: rgba(23, 162, 184, 0.1);
    color: var(--custom-info-color);
}

.custom-activity-type.custom {
    background-color: rgba(0, 123, 255, 0.1);
    color: var(--custom-primary-color);
}

/* Responsive Design */
@media (max-width: 768px) {
    .custom-dashboard-widget {
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .custom-dashboard-widget .widget-value {
        font-size: 1.5rem;
    }
    
    .custom-settings-form {
        padding: 1rem;
    }
    
    .custom-btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .custom-dashboard-widget .widget-title {
        font-size: 1rem;
    }
    
    .custom-dashboard-widget .widget-value {
        font-size: 1.25rem;
    }
}

/* Dark Theme Support */
@media (prefers-color-scheme: dark) {
    :root {
        --custom-light-color: #2c3e50;
        --custom-dark-color: #ecf0f1;
        --custom-box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.3);
    }
    
    body.dark-mode .custom-dashboard-widget {
        background: var(--custom-light-color);
        color: var(--custom-dark-color);
    }
    
    body.dark-mode .custom-settings-form {
        background: var(--custom-light-color);
        color: var(--custom-dark-color);
    }
    
    body.dark-mode .custom-form-control {
        background: #34495e;
        border-color: #4a6741;
        color: var(--custom-dark-color);
    }
}

/* Print Styles */
@media print {
    .custom-btn,
    .custom-activity-feed {
        display: none !important;
    }
    
    .custom-dashboard-widget {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ccc;
    }
}
EOF

echo "**SUCCESS: Custom CSS styling created**" >> "$STEP_EXECUTION"
echo "**Custom CSS styling implementation completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 4.3: Develop Custom JavaScript Interactions

### Planning Phase

```bash
# Update Step 04 tracking plan
echo "## 4.3: Develop Custom JavaScript Interactions" >> "$STEP_PLAN"
echo "- Create interactive dashboard widgets and charts" >> "$STEP_PLAN"
echo "- Implement AJAX form submissions and validations" >> "$STEP_PLAN"
echo "- Setup real-time notifications and updates" >> "$STEP_PLAN"
echo "- Create custom utility functions and helpers" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required JavaScript Implementation:

**4.3.1: Interactive Components**
- [ ] Dashboard data refresh and real-time updates
- [ ] Settings form validation and preview
- [ ] Dynamic content loading and pagination
- [ ] Custom modal dialogs and confirmations

**4.3.2: API Integration**
- [ ] RESTful API client for custom endpoints
- [ ] Error handling and user feedback
- [ ] Authentication token management
- [ ] Data caching and offline support

### Execution Commands:

```bash
# Log execution start
echo "## Section 4.3 Execution" >> "$STEP_EXECUTION"
echo "**Develop Custom JavaScript Interactions Started:** $(date)" >> "$STEP_EXECUTION"

# Create custom JavaScript file
cat > resources/Custom/js/custom-dashboard.js << 'EOF'
/**
 * Custom Dashboard JavaScript
 * Handles interactive elements and API communication
 */

class CustomDashboard {
    constructor() {
        this.apiBase = '/api/v1/custom';
        this.token = this.getApiToken();
        this.init();
    }

    init() {
        this.bindEventHandlers();
        this.initializeComponents();
        console.log('Custom Dashboard initialized');
    }

    bindEventHandlers() {
        // Dashboard refresh
        $(document).on('click', '[data-action="refresh-dashboard"]', (e) => {
            e.preventDefault();
            this.refreshDashboard();
        });

        // Settings form handling
        $(document).on('submit', '#settings-form', (e) => {
            this.handleSettingsForm(e);
        });

        // Theme preview
        $(document).on('change', '#theme', (e) => {
            this.previewTheme($(e.target).val());
        });

        // Activity feed auto-refresh
        setInterval(() => {
            this.refreshActivityFeed();
        }, 60000); // Refresh every minute
    }

    initializeComponents() {
        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialize date pickers if needed
        $('.custom-datepicker').each(function() {
            $(this).datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
        });

        // Initialize custom modals
        this.initModals();
    }

    async refreshDashboard() {
        try {
            this.showLoading('Refreshing dashboard...');
            
            const response = await this.apiCall('GET', '/dashboard');
            
            if (response.success) {
                this.updateDashboardData(response.data);
                this.showNotification('Dashboard refreshed successfully', 'success');
            } else {
                throw new Error(response.message || 'Failed to refresh dashboard');
            }
        } catch (error) {
            console.error('Dashboard refresh error:', error);
            this.showNotification('Failed to refresh dashboard: ' + error.message, 'error');
        } finally {
            this.hideLoading();
        }
    }

    updateDashboardData(data) {
        // Update statistics
        if (data.stats) {
            Object.keys(data.stats).forEach(key => {
                const element = $(`[data-stat="${key}"]`);
                if (element.length) {
                    element.html(data.stats[key]);
                }
            });
        }

        // Update activity feed
        if (data.activities) {
            this.updateActivityFeed(data.activities);
        }
    }

    updateActivityFeed(activities) {
        const feedContainer = $('#activity-feed');
        if (!feedContainer.length) return;

        let html = '';
        activities.forEach(activity => {
            html += `
                <div class="custom-activity-item">
                    <span class="custom-activity-type ${activity.type}">${activity.type}</span>
                    <span class="activity-description">${activity.description}</span>
                    <small class="activity-time text-muted">${this.formatTime(activity.timestamp)}</small>
                </div>
            `;
        });

        feedContainer.html(html);
    }

    async refreshActivityFeed() {
        try {
            const response = await this.apiCall('GET', '/activities?limit=5');
            if (response.success) {
                this.updateActivityFeed(response.data);
            }
        } catch (error) {
            console.warn('Failed to refresh activity feed:', error);
        }
    }

    handleSettingsForm(e) {
        const form = $(e.target);
        const submitBtn = form.find('button[type="submit"]');
        
        // Show loading state
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Saving...');

        // If AJAX is preferred
        if (form.data('ajax') === true) {
            e.preventDefault();
            this.submitSettingsViaAjax(form);
        }
    }

    async submitSettingsViaAjax(form) {
        try {
            const formData = new FormData(form[0]);
            const response = await this.apiCall('PUT', '/settings', formData);

            if (response.success) {
                this.showNotification('Settings saved successfully', 'success');
                // Update UI if needed
                this.updateSettingsDisplay(response.settings);
            } else {
                throw new Error(response.message || 'Failed to save settings');
            }
        } catch (error) {
            console.error('Settings save error:', error);
            this.showNotification('Failed to save settings: ' + error.message, 'error');
        } finally {
            // Reset button state
            const submitBtn = form.find('button[type="submit"]');
            submitBtn.prop('disabled', false);
            submitBtn.html('<i class="fas fa-save"></i> Save Settings');
        }
    }

    previewTheme(theme) {
        const body = $('body');
        
        // Remove existing theme classes
        body.removeClass('light-theme dark-theme auto-theme');
        
        // Apply new theme
        if (theme === 'dark') {
            body.addClass('dark-theme');
        } else if (theme === 'auto') {
            // Check system preference
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            body.addClass(prefersDark ? 'dark-theme' : 'light-theme');
        } else {
            body.addClass('light-theme');
        }

        this.showNotification(`Theme preview: ${theme}`, 'info');
    }

    initModals() {
        // Custom modal handling
        $(document).on('click', '[data-custom-modal]', function(e) {
            e.preventDefault();
            const modalId = $(this).data('custom-modal');
            $(`#${modalId}`).modal('show');
        });
    }

    async apiCall(method, endpoint, data = null) {
        const config = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            }
        };

        // Add authentication token if available
        if (this.token) {
            config.headers['Authorization'] = `Bearer ${this.token}`;
        }

        // Add CSRF token for Laravel
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken;
        }

        // Add data for POST/PUT requests
        if (data && ['POST', 'PUT', 'PATCH'].includes(method)) {
            if (data instanceof FormData) {
                config.body = data;
                delete config.headers['Content-Type']; // Let browser set it for FormData
            } else {
                config.body = JSON.stringify(data);
            }
        }

        const response = await fetch(`${this.apiBase}${endpoint}`, config);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    }

    getApiToken() {
        // Try to get token from meta tag or localStorage
        let token = $('meta[name="api-token"]').attr('content');
        if (!token) {
            token = localStorage.getItem('custom_api_token');
        }
        return token;
    }

    showLoading(message = 'Loading...') {
        // Create or show loading overlay
        let loader = $('#custom-loader');
        if (!loader.length) {
            loader = $(`
                <div id="custom-loader" class="custom-loading-overlay">
                    <div class="custom-loading-content">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="loading-message mt-2">${message}</div>
                    </div>
                </div>
            `);
            $('body').append(loader);
        } else {
            loader.find('.loading-message').text(message);
        }
        loader.show();
    }

    hideLoading() {
        $('#custom-loader').hide();
    }

    showNotification(message, type = 'info', duration = 5000) {
        const notification = $(`
            <div class="custom-notification alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `);

        // Add to notification container or body
        let container = $('#notification-container');
        if (!container.length) {
            container = $('<div id="notification-container" class="custom-notification-container"></div>');
            $('body').append(container);
        }

        container.append(notification);

        // Auto-dismiss after duration
        setTimeout(() => {
            notification.fadeOut(() => notification.remove());
        }, duration);
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        const now = new Date();
        const diff = now - date;

        // Less than 1 minute
        if (diff < 60000) {
            return 'Just now';
        }
        // Less than 1 hour
        else if (diff < 3600000) {
            const minutes = Math.floor(diff / 60000);
            return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        }
        // Less than 1 day
        else if (diff < 86400000) {
            const hours = Math.floor(diff / 3600000);
            return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        }
        // More than 1 day
        else {
            return date.toLocaleDateString();
        }
    }
}

// Initialize when DOM is ready
$(document).ready(function() {
    window.customDashboard = new CustomDashboard();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CustomDashboard;
}
EOF

echo "**SUCCESS: Custom JavaScript interactions created**" >> "$STEP_EXECUTION"
echo "**Custom JavaScript interactions development completed:** $(date)" >> "$STEP_EXECUTION"
```

---

## 4.4: Integrate Custom Assets with Build Process

### Planning Phase

```bash
# Update Step 04 tracking plan
echo "## 4.4: Integrate Custom Assets with Build Process" >> "$STEP_PLAN"
echo "- Update webpack.custom.cjs for asset compilation" >> "$STEP_PLAN"
echo "- Setup asset versioning and cache busting" >> "$STEP_PLAN"
echo "- Create production-optimized builds" >> "$STEP_PLAN"
echo "- Test asset loading and performance" >> "$STEP_PLAN"
echo "" >> "$STEP_PLAN"
```

### Required Build Integration:

**4.4.1: Asset Compilation**
- [ ] CSS compilation with vendor prefixes and minification
- [ ] JavaScript bundling and minification
- [ ] Image optimization and compression
- [ ] Font loading and optimization

**4.4.2: Development Workflow**
- [ ] Hot module replacement for development
- [ ] Source maps for debugging
- [ ] Asset watching and auto-compilation
- [ ] Error reporting and debugging tools

### Execution Commands:

```bash
# Log execution start
echo "## Section 4.4 Execution" >> "$STEP_EXECUTION"
echo "**Integrate Custom Assets with Build Process Started:** $(date)" >> "$STEP_EXECUTION"

# Test custom asset compilation
echo "**Testing custom asset compilation:** $(date)" >> "$STEP_EXECUTION"
if [ -f "webpack.custom.cjs" ]; then
    npm run build:custom >> "$STEP_EXECUTION" 2>&1
    if [ $? -eq 0 ]; then
        echo "✅ Custom assets compiled successfully" >> "$STEP_EXECUTION"
    else
        echo "❌ Custom asset compilation failed" >> "$STEP_EXECUTION"
    fi
else
    