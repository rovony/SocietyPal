# Example Customization: Enhanced Dashboard Widget

## Scenario

Create a custom dashboard widget that shows society statistics with enhanced UI and real-time updates.

---

## Step 1: Plan the Customization

### Requirements

-   Display society member count, upcoming events, and pending payments
-   Real-time updates using Livewire
-   Custom styling with smooth animations
-   Mobile-responsive design

### Files to Create/Modify

-   Custom Livewire component
-   Custom CSS styles
-   Custom JavaScript for interactions
-   Custom view template

---

## Step 2: Implementation

### A. Create Custom Livewire Component

**File:** `app/Custom/Livewire/DashboardStatsWidget.php`

```php
<?php

namespace App\Custom\Livewire;

use Livewire\Component;
use App\Models\Society;
use App\Models\User;
use App\Models\Event;
use App\Models\Payment;

class DashboardStatsWidget extends Component
{
    public $societyId;
    public $stats = [];

    protected $listeners = ['refreshStats'];

    public function mount($societyId = null)
    {
        $this->societyId = $societyId ?? auth()->user()->society_id;
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_members' => User::where('society_id', $this->societyId)->count(),
            'active_members' => User::where('society_id', $this->societyId)
                                  ->where('status', 'active')->count(),
            'upcoming_events' => Event::where('society_id', $this->societyId)
                                     ->where('event_date', '>=', now())
                                     ->count(),
            'pending_payments' => Payment::where('society_id', $this->societyId)
                                        ->where('status', 'pending')
                                        ->sum('amount'),
            'this_month_revenue' => Payment::where('society_id', $this->societyId)
                                          ->where('status', 'completed')
                                          ->whereMonth('created_at', now()->month)
                                          ->sum('amount'),
        ];
    }

    public function refreshStats()
    {
        $this->loadStats();
        $this->dispatch('stats-updated', $this->stats);
    }

    public function render()
    {
        return view('custom.livewire.dashboard-stats-widget');
    }
}
```

### B. Create Custom View Template

**File:** `resources/Custom/views/livewire/dashboard-stats-widget.blade.php`

```blade
<div class="custom-dashboard-widget" wire:poll.30s="refreshStats">
    <div class="widget-header">
        <h3 class="widget-title">
            <i class="fas fa-chart-line"></i>
            Society Statistics
        </h3>
        <button wire:click="refreshStats" class="refresh-btn">
            <i class="fas fa-sync-alt" wire:loading.class="fa-spin"></i>
        </button>
    </div>

    <div class="stats-grid">
        <!-- Total Members -->
        <div class="stat-card members">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['total_members'] ?? 0 }}</div>
                <div class="stat-label">Total Members</div>
                <div class="stat-sublabel">
                    {{ $stats['active_members'] ?? 0 }} active
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="stat-card events">
            <div class="stat-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $stats['upcoming_events'] ?? 0 }}</div>
                <div class="stat-label">Upcoming Events</div>
                <div class="stat-sublabel">This month</div>
            </div>
        </div>

        <!-- Pending Payments -->
        <div class="stat-card payments">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">₹{{ number_format($stats['pending_payments'] ?? 0) }}</div>
                <div class="stat-label">Pending Payments</div>
                <div class="stat-sublabel">To be collected</div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="stat-card revenue">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">₹{{ number_format($stats['this_month_revenue'] ?? 0) }}</div>
                <div class="stat-label">This Month Revenue</div>
                <div class="stat-sublabel">Collected amount</div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="widget-actions">
        <a href="{{ route('custom.members.index') }}" class="action-btn">
            <i class="fas fa-user-plus"></i>
            Manage Members
        </a>
        <a href="{{ route('custom.events.create') }}" class="action-btn">
            <i class="fas fa-plus"></i>
            Add Event
        </a>
        <a href="{{ route('custom.payments.index') }}" class="action-btn">
            <i class="fas fa-money-bill"></i>
            View Payments
        </a>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ custom_asset('css/dashboard-widget.css') }}">
@endpush

@push('scripts')
<script src="{{ custom_asset('js/dashboard-widget.js') }}"></script>
@endpush
```

### C. Create Custom CSS

**File:** `resources/Custom/css/components/dashboard-widget.scss`

```scss
.custom-dashboard-widget {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 25px;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    margin-bottom: 30px;
    position: relative;
    overflow: hidden;

    &::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
    }

    .widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        position: relative;
        z-index: 1;

        .widget-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;

            i {
                font-size: 1.3rem;
            }
        }

        .refresh-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;

            &:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }
        }
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
        position: relative;
        z-index: 1;

        @media (max-width: 768px) {
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        @media (max-width: 480px) {
            grid-template-columns: 1fr;
        }
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.2);

        &:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.25);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;

            &.members {
                background: rgba(52, 211, 153, 0.3);
            }
            &.events {
                background: rgba(251, 191, 36, 0.3);
            }
            &.payments {
                background: rgba(239, 68, 68, 0.3);
            }
            &.revenue {
                background: rgba(34, 197, 94, 0.3);
            }
        }

        .stat-content {
            .stat-number {
                font-size: 1.8rem;
                font-weight: 700;
                line-height: 1;
                margin-bottom: 5px;
            }

            .stat-label {
                font-size: 0.9rem;
                font-weight: 500;
                opacity: 0.9;
                margin-bottom: 2px;
            }

            .stat-sublabel {
                font-size: 0.75rem;
                opacity: 0.7;
            }
        }
    }

    .widget-actions {
        display: flex;
        gap: 15px;
        position: relative;
        z-index: 1;

        @media (max-width: 768px) {
            flex-direction: column;
            gap: 10px;
        }

        .action-btn {
            flex: 1;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);

            &:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: translateY(-2px);
                color: white;
                text-decoration: none;
            }

            i {
                margin-right: 8px;
            }
        }
    }
}

// Loading animation
@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

[wire\\:loading] .custom-dashboard-widget {
    .stat-number {
        animation: pulse 1.5s infinite;
    }
}
```

### D. Create Custom JavaScript

**File:** `resources/Custom/js/components/dashboard-widget.js`

```javascript
class DashboardWidget {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.startAutoRefresh();
        this.addAnimations();
    }

    bindEvents() {
        // Listen for Livewire events
        document.addEventListener("stats-updated", (event) => {
            this.animateNumbers(event.detail);
            this.showUpdateNotification();
        });

        // Add click effects to stat cards
        document.querySelectorAll(".stat-card").forEach((card) => {
            card.addEventListener("click", (e) => {
                this.addRippleEffect(e, card);
            });
        });
    }

    startAutoRefresh() {
        // Optional: Add visual countdown for next refresh
        let countdown = 30; // 30 seconds
        const timer = setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                countdown = 30;
            }
            this.updateRefreshIndicator(countdown);
        }, 1000);
    }

    addAnimations() {
        // Animate numbers on load
        const numbers = document.querySelectorAll(".stat-number");
        numbers.forEach((number) => {
            this.animateNumber(number);
        });

        // Stagger animation for cards
        const cards = document.querySelectorAll(".stat-card");
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add("fade-in-up");
        });
    }

    animateNumber(element) {
        const target = parseInt(element.textContent.replace(/[^\d]/g, ""));
        const duration = 1000;
        const step = target / (duration / 16);
        let current = 0;

        const counter = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(counter);
            }

            const formatted = element.textContent.includes("₹")
                ? `₹${Math.floor(current).toLocaleString()}`
                : Math.floor(current).toString();

            element.textContent = formatted;
        }, 16);
    }

    animateNumbers(newStats) {
        Object.keys(newStats).forEach((key) => {
            const element = document.querySelector(
                `[data-stat="${key}"] .stat-number`
            );
            if (element) {
                this.animateNumber(element);
            }
        });
    }

    addRippleEffect(event, element) {
        const ripple = document.createElement("span");
        const rect = element.getBoundingClientRect();
        const size = 100;
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.cssText = `
            position: absolute;
            left: ${x}px;
            top: ${y}px;
            width: ${size}px;
            height: ${size}px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        `;

        element.style.position = "relative";
        element.style.overflow = "hidden";
        element.appendChild(ripple);

        setTimeout(() => ripple.remove(), 600);
    }

    showUpdateNotification() {
        const notification = document.createElement("div");
        notification.className = "update-notification";
        notification.textContent = "Stats updated!";
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
            animation: slideInRight 0.3s ease-out;
        `;

        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }

    updateRefreshIndicator(seconds) {
        const indicator = document.querySelector(".refresh-indicator");
        if (indicator) {
            indicator.textContent = `Next refresh in ${seconds}s`;
        }
    }
}

// CSS animations
const style = document.createElement("style");
style.textContent = `
    @keyframes ripple {
        to { transform: scale(4); opacity: 0; }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); }
        to { transform: translateX(0); }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
`;
document.head.appendChild(style);

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
    new DashboardWidget();
});

// Export for use in other modules
window.DashboardWidget = DashboardWidget;
```

---

## Step 3: Integration

### A. Add to Main Dashboard

**File:** `resources/Custom/views/dashboard/index.blade.php`

```blade
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Custom Dashboard Widget -->
            @livewire('custom.dashboard-stats-widget')
        </div>
    </div>

    <!-- Other dashboard content -->
    <div class="row">
        <!-- Existing dashboard widgets -->
    </div>
</div>
@endsection
```

### B. Register Livewire Component

**In:** `app/Providers/CustomizationServiceProvider.php`

```php
public function boot(): void
{
    // ... existing code

    // Register custom Livewire components
    Livewire::component('custom.dashboard-stats-widget',
        \App\Custom\Livewire\DashboardStatsWidget::class);
}
```

---

## Step 4: Testing

### Manual Testing Checklist

-   [ ] Widget displays correctly on dashboard
-   [ ] Real-time updates work (30-second intervals)
-   [ ] Manual refresh button works
-   [ ] Mobile responsive design
-   [ ] Animations and interactions work
-   [ ] Data accuracy verified

### Performance Testing

-   [ ] Page load time impact measured
-   [ ] Memory usage acceptable
-   [ ] Database query optimization
-   [ ] Asset load time acceptable

---

## Step 5: Documentation

### Feature Documentation

```markdown
# Enhanced Dashboard Stats Widget

**Feature:** Real-time society statistics dashboard widget
**Version:** 1.0.0
**Dependencies:** Livewire 3.x, Font Awesome

## Business Value

-   Provides at-a-glance society overview
-   Real-time data updates
-   Improved user experience
-   Mobile-friendly interface

## Technical Implementation

-   Custom Livewire component
-   SCSS with CSS Grid layout
-   JavaScript for animations
-   Investment protection compliant
```

This example demonstrates a complete customization workflow following the established patterns and maintaining separation from vendor code.
