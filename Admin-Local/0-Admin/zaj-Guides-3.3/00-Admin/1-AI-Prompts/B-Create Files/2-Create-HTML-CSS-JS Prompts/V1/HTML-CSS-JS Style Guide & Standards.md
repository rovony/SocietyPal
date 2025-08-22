# HTML-CSS-JS Style Guide & Standards

> **Purpose:** Establish consistent design patterns, code organization, and technical standards for all future HTML-CSS-JS interactive documentation files.

---

## **🎯 Design Philosophy**

### **Visual Design Principles**

-   **Modern Typography:** 12-14px base fonts with clean, readable hierarchy
-   **Light Theme Primary:** Creamy whites (#fefefe, #f8f6f3) with sleek black option
-   **No Emojis:** Use light SVG icons and text-based indicators instead
-   **Card-Based Layout:** Clean cards with subtle shadows and rounded corners
-   **Color-Coded Sections:** Consistent color meanings across all interfaces
-   **Responsive Design:** Mobile-first approach with desktop enhancements

### **User Experience Standards**

-   **Progressive Disclosure:** Collapsible sections to reduce cognitive load
-   **Interactive Feedback:** Hover states, transitions, and visual confirmations
-   **Persistent State:** Local storage for user progress and preferences
-   **Copy-to-Clipboard:** One-click copying for all code blocks and commands
-   **Search & Filter:** Quick navigation through large amounts of content
-   **Accessibility:** Keyboard navigation, proper contrast, semantic markup

---

## **🏗️ Technical Architecture**

### **File Structure Standards**

```
project/
├── index.html                 # Single-file architecture preferred
├── assets/                    # Only if external assets needed
│   ├── icons/                # SVG icons
│   └── fonts/                # Custom fonts (if required)
└── README.md                 # Implementation guide
```

### **Code Organization Pattern**

```html
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Meta & Title -->
        <!-- CSS Variables & Base Styles -->
        <!-- Component Styles -->
        <!-- Responsive Styles -->
        <!-- Utility Classes -->
    </head>
    <body>
        <!-- Header with controls -->
        <!-- Progress indicators -->
        <!-- Main content with tabs/sections -->
        <!-- JavaScript at bottom -->
    </body>
</html>
```

---

## **🎨 CSS Standards**

### **CSS Custom Properties (Required)**

```css
:root {
    /* Color Palette */
    --bg-primary: #fefefe;
    --bg-secondary: #f8f6f3;
    --bg-card: #ffffff;
    --bg-code: #2d3748;
    --text-primary: #2d3748;
    --text-secondary: #4a5568;
    --text-muted: #718096;
    --border-color: #e2e8f0;

    /* Accent Colors */
    --accent-primary: #4299e1; /* Blue */
    --accent-secondary: #38b2ac; /* Teal */
    --accent-success: #48bb78; /* Green */
    --accent-warning: #ed8936; /* Orange */
    --accent-danger: #f56565; /* Red */

    /* Shadows */
    --shadow-light: 0 1px 3px rgba(0, 0, 0, 0.1);
    --shadow-medium: 0 4px 6px rgba(0, 0, 0, 0.1);
    --shadow-heavy: 0 10px 15px rgba(0, 0, 0, 0.1);

    /* Spacing & Typography */
    --border-radius: 8px;
    --font-size-xs: 11px;
    --font-size-sm: 12px;
    --font-size-base: 14px;
    --font-size-lg: 16px;
    --font-size-xl: 18px;
    --font-size-2xl: 24px;
    --font-size-3xl: 32px;
}
```

### **Dark Theme Implementation**

```css
[data-theme="dark"] {
    --bg-primary: #1a202c;
    --bg-secondary: #2d3748;
    --bg-card: #2d3748;
    --bg-code: #1a202c;
    --text-primary: #f7fafc;
    --text-secondary: #e2e8f0;
    --text-muted: #a0aec0;
    --border-color: #4a5568;
}
```

### **Typography Standards**

```css
body {
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen,
        Ubuntu, Cantarell, sans-serif;
    font-size: var(--font-size-base);
    line-height: 1.6;
    color: var(--text-primary);
}

/* Code Typography */
.code-content {
    font-family: "SF Mono", Monaco, "Cascadia Code", "Roboto Mono", Consolas,
        "Courier New", monospace;
    font-size: var(--font-size-sm);
    line-height: 1.4;
}
```

### **Component Patterns**

#### **Card Components**

```css
.card {
    background: var(--bg-card);
    border: 1px solid var(--border-color);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-light);
    transition: all 0.2s ease;
}

.card:hover {
    box-shadow: var(--shadow-medium);
    transform: translateY(-1px);
}
```

#### **Button Components**

```css
.button {
    background: var(--accent-primary);
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-size: var(--font-size-sm);
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.button:hover {
    background: var(--accent-secondary);
    transform: translateY(-1px);
}
```

#### **Code Block Pattern**

```css
.code-block {
    background: var(--bg-code);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin: 1rem 0;
    position: relative;
    overflow-x: auto;
}

.code-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.copy-button {
    background: rgba(255, 255, 255, 0.1);
    border: none;
    color: #a0aec0;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: var(--font-size-xs);
    transition: all 0.2s ease;
}
```

### **Responsive Design Standards**

```css
/* Mobile First Approach */
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem;
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
    }

    .tabs {
        flex-direction: column;
    }

    .search-input {
        width: 100%;
    }
}
```

---

## **📱 JavaScript Standards**

### **Module Organization Pattern**

```javascript
// Theme Management
const ThemeManager = {
    init() {
        this.loadTheme();
        this.bindEvents();
    },

    loadTheme() {
        const savedTheme = localStorage.getItem("theme") || "light";
        // Implementation
    },

    bindEvents() {
        // Event listeners
    },
};

// Progress Management
const ProgressManager = {
    init() {
        this.loadProgress();
        this.bindEvents();
    },

    updateProgress() {
        // Calculate and update progress
    },
};

// Initialize on DOM ready
document.addEventListener("DOMContentLoaded", () => {
    ThemeManager.init();
    ProgressManager.init();
    TabManager.init();
    SearchManager.init();
});
```

### **Local Storage Standards**

```javascript
// Consistent naming conventions
const StorageKeys = {
    THEME: "theme",
    PROGRESS: "checklist-progress",
    STEP_PREFIX: "step-",
    USER_PREFERENCES: "user-preferences",
};

// Safe storage operations
const Storage = {
    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            console.warn("Storage failed:", e);
        }
    },

    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            console.warn("Storage retrieval failed:", e);
            return defaultValue;
        }
    },
};
```

### **Event Handling Patterns**

```javascript
// Delegation pattern for dynamic content
document.addEventListener("click", (e) => {
    if (e.target.matches(".copy-button")) {
        handleCopyButton(e.target);
    }

    if (e.target.matches(".step-checkbox")) {
        handleStepCheckbox(e.target);
    }

    if (e.target.matches(".tab-button")) {
        handleTabSwitch(e.target);
    }
});

// Debounced search
let searchTimeout;
searchInput.addEventListener("input", (e) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        performSearch(e.target.value);
    }, 300);
});
```

### **Copy-to-Clipboard Standards**

```javascript
// RELIABLE UNIVERSAL COPY FUNCTION
// Avoid async/await and modern APIs for universal compatibility
function copyToClipboard(text, button) {
    // Store original button state
    const originalText = button.textContent;

    // Use simple, reliable textarea method
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    textArea.setSelectionRange(0, 99999); // Mobile support

    let success = false;
    try {
        success = document.execCommand("copy");
    } catch (err) {
        success = false;
    }

    document.body.removeChild(textArea);

    // Immediate visual feedback
    if (success) {
        button.textContent = "✅ Copied!";
        button.style.background = "#48bb78";
    } else {
        button.textContent = "❌ Failed";
        button.style.background = "#f56565";
    }

    // Auto-reset after 2 seconds
    setTimeout(() => {
        button.textContent = originalText;
        button.style.background = "#667eea";
    }, 2000);
}

// Generic copy handler for any code block
function copyCode(button) {
    const codeContent = button
        .closest(".code-block")
        .querySelector(".code-content");
    const text = codeContent.textContent.trim();
    copyToClipboard(text, button);
}
```

### **Universal JavaScript Reliability Principles**

#### **Avoid Modern APIs That Can Fail**

```javascript
// ❌ DON'T: Modern APIs that fail on file:// or older browsers
async function badCopy(text) {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
        } else {
            // Complex fallback that often fails
        }
    } catch (err) {
        // Often reaches here on local files
    }
}

// ✅ DO: Simple, reliable methods that work everywhere
function goodCopy(text, button) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.select();
    const success = document.execCommand("copy");
    document.body.removeChild(textArea);
    showFeedback(button, success);
}
```

#### **Test Environment Compatibility**

-   **File Protocol (file://)**: Must work for local HTML files
-   **HTTP/HTTPS**: Must work on all server environments
-   **All Browsers**: Chrome, Firefox, Safari, Edge (including older versions)
-   **Mobile Devices**: Touch-friendly and responsive

---

## **🎨 Color System Standards**

### **Primary Color Meanings**

-   **Blue (#4299e1):** Primary actions, links, navigation
-   **Teal (#38b2ac):** Secondary actions, accents
-   **Green (#48bb78):** Success states, completed items
-   **Orange (#ed8936):** Warnings, caution states
-   **Red (#f56565):** Errors, danger states

### **Semantic Color Usage**

```css
/* Status Colors */
.status-success {
    color: var(--accent-success);
}
.status-warning {
    color: var(--accent-warning);
}
.status-danger {
    color: var(--accent-danger);
}
.status-info {
    color: var(--accent-primary);
}

/* Background Variants */
.bg-success {
    background: rgba(72, 187, 120, 0.1);
}
.bg-warning {
    background: rgba(237, 137, 54, 0.1);
}
.bg-danger {
    background: rgba(245, 101, 101, 0.1);
}
.bg-info {
    background: rgba(66, 153, 225, 0.1);
}
```

---

## **🔧 Component Library Standards**

### **Required Components**

#### **1. Header Component**

```html
<header class="header">
    <div class="header-content">
        <div>
            <h1>Page Title</h1>
            <div class="header-subtitle">Description</div>
        </div>
        <div class="header-controls">
            <div class="search-container">
                <input
                    type="text"
                    class="search-input"
                    placeholder="Search..."
                />
            </div>
            <button class="theme-toggle">Dark Mode</button>
        </div>
    </div>
</header>
```

#### **2. Progress Component**

```html
<div class="progress-container">
    <div class="progress-bar">
        <div class="progress-fill"></div>
    </div>
    <div class="progress-text">0% Complete</div>
</div>
```

#### **3. Tab System**

```html
<div class="tabs">
    <button class="tab-button active" data-tab="tab1">Tab 1</button>
    <button class="tab-button" data-tab="tab2">Tab 2</button>
</div>
```

#### **4. Collapsible Step**

```html
<div class="step">
    <div class="step-header">
        <div class="step-checkbox" data-step-id="unique-id"></div>
        <div class="step-title">Step Title</div>
        <div class="step-duration">5-10 min</div>
    </div>
    <div class="step-content">
        <!-- Step content -->
    </div>
</div>
```

#### **5. Code Block**

```html
<div class="code-block">
    <div class="code-header">
        <span class="code-lang">bash</span>
        <button class="copy-button" onclick="copyCode(this)">Copy</button>
    </div>
    <div class="code-content"># Your code here</div>
</div>
```

#### **6. Info Boxes**

```html
<div class="info-box">
    <div class="info-title">Information</div>
    <div class="info-content">Description text</div>
</div>

<div class="info-box warning">
    <div class="info-title">Warning</div>
    <div class="info-content">Warning text</div>
</div>
```

---

## **📱 Responsive Design Standards**

### **Breakpoint System**

```css
/* Mobile First */
/* Base styles: 320px+ */

@media (min-width: 480px) {
    /* Small tablets and large phones */
}

@media (min-width: 768px) {
    /* Tablets */
}

@media (min-width: 1024px) {
    /* Desktops */
}

@media (min-width: 1200px) {
    /* Large desktops */
}
```

### **Mobile Adaptations**

-   Stack horizontal layouts vertically
-   Increase touch targets to minimum 44px
-   Adjust font sizes for mobile readability
-   Simplify navigation patterns
-   Optimize code blocks for mobile scrolling

---

## **⚡ Performance Standards**

### **Optimization Requirements**

-   **Single File Architecture:** Prefer single HTML file with embedded CSS/JS
-   **Minimal Dependencies:** No external libraries unless absolutely necessary
-   **Optimized Assets:** Compress images, minify code for production
-   **Lazy Loading:** Implement for large content sections
-   **Efficient DOM Manipulation:** Use event delegation, minimize reflows

### **Loading Performance**

```javascript
// Efficient initialization
document.addEventListener("DOMContentLoaded", () => {
    // Critical functionality first
    initializeTheme();
    initializeProgress();

    // Non-critical features after
    requestIdleCallback(() => {
        initializeSearch();
        initializeTooltips();
    });
});
```

---

## **⚡ Universal JavaScript Reliability Standards**

> **Critical:** All JavaScript must work in ANY environment - local files, HTTP, HTTPS, all browsers, all devices.

### **🎯 Reliability-First Principles**

#### **1. Avoid Problematic Modern APIs**

```javascript
// ❌ AVOID: These often fail in real-world scenarios
- navigator.clipboard (fails on file://, needs HTTPS)
- async/await with clipboard (complex error handling)
- window.isSecureContext (unreliable detection)
- Complex API feature detection

// ✅ USE: Proven, reliable methods
- document.execCommand('copy') (works everywhere)
- Simple DOM manipulation
- Basic event listeners
- Traditional error handling
```

#### **2. Environment-Agnostic Development**

```javascript
// Test in ALL these scenarios:
// - Local HTML files (file:// protocol)
// - HTTP development servers
// - HTTPS production sites
// - Mobile browsers
// - Older browser versions
```

#### **3. Fail-Safe Implementation Patterns**

```javascript
// Pattern: Try simple method first, provide immediate feedback
function universalFunction(element, action) {
    const originalState = captureState(element);

    let success = false;
    try {
        success = performAction(action);
    } catch (err) {
        success = false;
    }

    provideFeedback(element, success, originalState);
}
```

### **🔧 Universal Function Library**

#### **Local Storage (Safe Implementation)**

```javascript
// Handles environments where localStorage might be disabled
const SafeStorage = {
    set(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (e) {
            console.warn("Storage unavailable:", e);
            return false;
        }
    },

    get(key, defaultValue = null) {
        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (e) {
            console.warn("Storage retrieval failed:", e);
            return defaultValue;
        }
    },
};
```

#### **Theme Switching (Universal)**

```javascript
// Works without localStorage if needed
const ThemeManager = {
    currentTheme: "light",

    init() {
        this.currentTheme = SafeStorage.get("theme", "light");
        this.applyTheme(this.currentTheme);
        this.bindEvents();
    },

    toggle() {
        this.currentTheme = this.currentTheme === "light" ? "dark" : "light";
        this.applyTheme(this.currentTheme);
        SafeStorage.set("theme", this.currentTheme);
    },

    applyTheme(theme) {
        document.documentElement.setAttribute("data-theme", theme);
    },
};
```

### **🚨 Common Pitfalls to Avoid**

#### **1. Over-Reliance on Modern Features**

```javascript
// ❌ This fails on file:// protocols
if ("clipboard" in navigator) {
    // Modern approach that often fails
}

// ✅ This works everywhere
function copyText(text, button) {
    // Use reliable method regardless of environment
}
```

#### **2. Complex Async Chains**

```javascript
// ❌ Complex error handling that can break
try {
    const result = await complexAsyncOperation();
    await anotherAsyncOperation(result);
} catch (err) {
    // Hard to debug, often fails silently
}

// ✅ Simple, predictable operations
const result = performSyncOperation();
if (result.success) {
    handleSuccess(result.data);
} else {
    handleError(result.error);
}
```

#### **3. Assuming Secure Contexts**

```javascript
// ❌ Assumes HTTPS or localhost
if (window.isSecureContext) {
    // Only works in limited environments
}

// ✅ Works in all contexts
function performAction() {
    // Use methods that work everywhere
}
```

### **🔍 Testing Requirements**

#### **Mandatory Test Environments**

1. **Local File Testing**: Open HTML file directly in browser
2. **HTTP Server**: Basic development server
3. **HTTPS Production**: Live server environment
4. **Mobile Testing**: Various devices and screen sizes
5. **Browser Testing**: Chrome, Firefox, Safari, Edge

#### **Functionality Verification Checklist**

-   [ ] Copy buttons work on all code blocks
-   [ ] Theme switching persists across page loads
-   [ ] Progress tracking saves and restores correctly
-   [ ] Search functionality works without errors
-   [ ] All interactive elements provide immediate feedback
-   [ ] No JavaScript errors in console
-   [ ] Responsive design works on all screen sizes

### **💡 Implementation Philosophy**

#### **Simplicity Over Sophistication**

-   Choose simple, proven methods over complex modern APIs
-   Prefer immediate feedback over silent failures
-   Use progressive enhancement, not feature detection
-   Build for the lowest common denominator, enhance where possible

#### **User Experience First**

-   Every action should provide immediate visual feedback
-   Failed operations should be clearly communicated
-   No silent failures or broken functionality
-   Graceful degradation in all environments

#### **Maintainability Focus**

-   Code should be readable and debuggable
-   Minimize dependencies and complex abstractions
-   Document any environment-specific behaviors
-   Use consistent patterns across all functions

---

## **🔍 Testing Standards**

### **Cross-Browser Testing**

-   **Chrome:** Latest 2 versions
-   **Firefox:** Latest 2 versions
-   **Safari:** Latest 2 versions
-   **Edge:** Latest 2 versions

### **Device Testing**

-   **Mobile:** iPhone, Android (various sizes)
-   **Tablet:** iPad, Android tablets
-   **Desktop:** Various screen sizes (1366x768 to 4K)

### **Functionality Testing**

-   **Theme switching:** Light/dark mode persistence
-   **Local storage:** Progress saving and loading
-   **Copy functionality:** All copy buttons work
-   **Search:** Real-time filtering
-   **Responsive:** All breakpoints function correctly

---

## **📋 Implementation Checklist**

### **Before Starting New Project**

-   [ ] Review this style guide thoroughly
-   [ ] Set up CSS custom properties with standard values
-   [ ] Implement base component structure
-   [ ] Test theme switching functionality
-   [ ] Verify responsive breakpoints
-   [ ] **Test copy functionality on local files (file://)**
-   [ ] **Ensure no modern API dependencies that can fail**

### **During Development**

-   [ ] Follow naming conventions consistently
-   [ ] Test on multiple devices and browsers
-   [ ] Validate HTML and CSS
-   [ ] Check accessibility standards
-   [ ] Implement proper error handling
-   [ ] **Test ALL interactive features in file:// protocol**
-   [ ] **Use only proven, reliable JavaScript methods**
-   [ ] **Provide immediate feedback for all user actions**

### **Before Deployment**

-   [ ] Test all interactive features
-   [ ] Verify local storage functionality
-   [ ] Check copy-to-clipboard features
-   [ ] Validate responsive design
-   [ ] Performance audit complete
-   [ ] **Test in all environments: file://, HTTP, HTTPS**
-   [ ] **Verify no JavaScript console errors**
-   [ ] **Confirm universal browser compatibility**

---

## **🚀 Future Enhancement Standards**

### **Planned Improvements**

-   **Animation System:** Consistent animation patterns
-   **Icon Library:** SVG icon standardization
-   **Advanced Search:** Fuzzy search capabilities
-   **Export Functions:** PDF/print-friendly versions
-   **Collaboration Features:** Share progress, team views

### **Technology Evolution**

-   **CSS Grid:** Enhanced layout capabilities
-   **Web Components:** Reusable component architecture
-   **Progressive Web App:** Offline functionality
-   **Advanced Interactions:** Touch gestures, keyboard shortcuts

---

**This style guide ensures consistency, maintainability, and scalability across all HTML-CSS-JS documentation projects.**
