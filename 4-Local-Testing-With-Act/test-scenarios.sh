#!/bin/bash
# LOCAL TESTING SCENARIOS
# Pre-defined test scenarios for common deployment situations

set -e

echo "🧪 Local Testing Scenarios for Laravel Deployment"
echo "================================================"

WORKFLOW_PATH="../1-GitHub-Actions-Workflows/ultimate-deployment-dry-run.yml"

show_menu() {
    echo ""
    echo "🎯 Available Test Scenarios:"
    echo ""
    echo "1. 🚀 Quick Build Test (PHP 8.2, Node 18)"
    echo "2. 🔄 Version Compatibility Test (PHP 8.1 vs 8.2)"
    echo "3. 🎭 Faker Edge Case Test (Your specific issue)"
    echo "4. 🌐 Full Stack App Test (With frontend)"
    echo "5. 📡 API-Only App Test (No frontend)"
    echo "6. 🏗️ SaaS Installer Test (With seeders)"
    echo "7. 🔧 Staging Environment Test (With debugging)"
    echo "8. 🚨 Edge Cases Only Test"
    echo "9. 🎯 Full Comprehensive Test (Everything)"
    echo "10. 🛠️ Custom Test (Enter your own parameters)"
    echo "11. ❓ Show Act Help"
    echo "0. 🚪 Exit"
    echo ""
}

run_test() {
    local scenario="$1"
    local description="$2"
    shift 2
    local args="$*"

    echo ""
    echo "🚀 Running: $description"
    echo "📋 Command: act workflow_dispatch -W $WORKFLOW_PATH $args"
    echo ""

    if act workflow_dispatch -W "$WORKFLOW_PATH" $args; then
        echo ""
        echo "✅ Test completed successfully: $scenario"
    else
        echo ""
        echo "❌ Test failed: $scenario"
        echo "💡 Check the output above for specific issues"
    fi

    echo ""
    read -p "Press Enter to continue..."
}

while true; do
    show_menu
    read -p "Choose a scenario (0-11): " choice

    case $choice in
        1)
            run_test "Quick Build Test" "Quick build test with latest versions" \
                --input test-phase=build-only \
                --input php-version=8.2 \
                --input node-version=18
            ;;
        2)
            run_test "Version Compatibility Test" "Test PHP version mismatch detection" \
                --input test-phase=full \
                --input php-version=8.2 \
                --input php-version-server=8.1 \
                --input node-version=18
            ;;
        3)
            run_test "Faker Edge Case Test" "Test the specific Faker dependency issue" \
                --input test-phase=runtime-only \
                --input php-version=8.2 \
                --input app-type=saas-installer
            ;;
        4)
            run_test "Full Stack App Test" "Test app with frontend assets" \
                --input test-phase=full \
                --input php-version=8.2 \
                --input node-version=18 \
                --input app-type=full-stack
            ;;
        5)
            run_test "API-Only App Test" "Test API-only Laravel app" \
                --input test-phase=full \
                --input php-version=8.2 \
                --input app-type=api-only
            ;;
        6)
            run_test "SaaS Installer Test" "Test SaaS app with installer and seeders" \
                --input test-phase=full \
                --input php-version=8.2 \
                --input node-version=18 \
                --input app-type=saas-installer
            ;;
        7)
            run_test "Staging Environment Test" "Test staging with debugging tools" \
                --input test-phase=full \
                --input php-version=8.2 \
                --input node-version=18 \
                --input app-type=demo
            ;;
        8)
            run_test "Edge Cases Only Test" "Test only edge cases and compatibility" \
                --input test-phase=edge-cases-only \
                --input php-version=8.2 \
                --input php-version-server=8.1
            ;;
        9)
            run_test "Full Comprehensive Test" "Ultimate test - everything included" \
                --input test-phase=full \
                --input php-version=8.2 \
                --input php-version-server=8.1 \
                --input composer-version=2.6 \
                --input node-version=18 \
                --input app-type=auto-detect
            ;;
        10)
            echo ""
            echo "🛠️ Custom Test Configuration"
            echo "=========================="
            read -p "PHP Version (build): " php_build
            read -p "PHP Version (server): " php_server
            read -p "Node Version: " node_ver
            read -p "Test Phase (full/build-only/runtime-only/ssh-only/edge-cases-only): " phase
            read -p "App Type (auto-detect/api-only/full-stack/saas-installer/demo): " app_type

            run_test "Custom Test" "Custom test with your parameters" \
                --input test-phase="$phase" \
                --input php-version="$php_build" \
                --input php-version-server="$php_server" \
                --input node-version="$node_ver" \
                --input app-type="$app_type"
            ;;
        11)
            echo ""
            echo "📚 Act Help & Commands:"
            echo "======================"
            echo ""
            echo "Basic act commands:"
            echo "  act --list                           # List all workflows"
            echo "  act --dry-run                        # Show what would run"
            echo "  act workflow_dispatch --help         # Show workflow_dispatch help"
            echo ""
            echo "Useful flags:"
            echo "  --verbose                            # Show detailed output"
            echo "  --reuse                              # Reuse containers for faster testing"
            echo "  --rm                                 # Remove containers after run"
            echo "  --pull                               # Always pull latest images"
            echo ""
            echo "Testing specific jobs:"
            echo "  act -j ultimate-dry-run              # Run specific job"
            echo ""
            read -p "Press Enter to continue..."
            ;;
        0)
            echo "👋 Goodbye!"
            exit 0
            ;;
        *)
            echo "❌ Invalid choice. Please choose 0-11."
            ;;
    esac
done
