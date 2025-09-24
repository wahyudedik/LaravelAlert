#!/bin/bash

# Laravel Alert Release Script
# This script automates the release process for Laravel Alert

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Function to check prerequisites
check_prerequisites() {
    print_status "Checking prerequisites..."
    
    if ! command_exists git; then
        print_error "Git is not installed"
        exit 1
    fi
    
    if ! command_exists composer; then
        print_error "Composer is not installed"
        exit 1
    fi
    
    if ! command_exists php; then
        print_error "PHP is not installed"
        exit 1
    fi
    
    print_success "All prerequisites are met"
}

# Function to check if we're in a git repository
check_git_repo() {
    print_status "Checking git repository..."
    
    if ! git rev-parse --git-dir > /dev/null 2>&1; then
        print_error "Not in a git repository"
        exit 1
    fi
    
    print_success "Git repository found"
}

# Function to check if working directory is clean
check_working_directory() {
    print_status "Checking working directory..."
    
    if ! git diff-index --quiet HEAD --; then
        print_error "Working directory is not clean"
        print_warning "Please commit or stash your changes before releasing"
        exit 1
    fi
    
    print_success "Working directory is clean"
}

# Function to check if we're on the main branch
check_branch() {
    print_status "Checking current branch..."
    
    current_branch=$(git branch --show-current)
    if [ "$current_branch" != "main" ]; then
        print_error "Not on main branch (current: $current_branch)"
        print_warning "Please switch to main branch before releasing"
        exit 1
    fi
    
    print_success "On main branch"
}

# Function to check if remote is up to date
check_remote() {
    print_status "Checking remote repository..."
    
    git fetch origin
    
    if ! git diff HEAD origin/main --quiet; then
        print_error "Local branch is not up to date with remote"
        print_warning "Please pull the latest changes before releasing"
        exit 1
    fi
    
    print_success "Local branch is up to date with remote"
}

# Function to run tests
run_tests() {
    print_status "Running tests..."
    
    if ! composer install --no-dev --optimize-autoloader; then
        print_error "Failed to install dependencies"
        exit 1
    fi
    
    if ! ./vendor/bin/phpunit; then
        print_error "Tests failed"
        exit 1
    fi
    
    print_success "All tests passed"
}

# Function to update version
update_version() {
    local version=$1
    
    print_status "Updating version to $version..."
    
    # Update composer.json
    if command_exists jq; then
        jq ".version = \"$version\"" composer.json > composer.json.tmp && mv composer.json.tmp composer.json
    else
        print_warning "jq not found, please update composer.json manually"
    fi
    
    # Update package.json if it exists
    if [ -f "package.json" ]; then
        if command_exists jq; then
            jq ".version = \"$version\"" package.json > package.json.tmp && mv package.json.tmp package.json
        else
            print_warning "jq not found, please update package.json manually"
        fi
    fi
    
    print_success "Version updated to $version"
}

# Function to create changelog entry
create_changelog_entry() {
    local version=$1
    local date=$(date +%Y-%m-%d)
    
    print_status "Creating changelog entry for $version..."
    
    # Create temporary changelog entry
    cat > CHANGELOG.tmp << EOF
## [$version] - $date

### Added
- Release $version

### Changed
- N/A

### Deprecated
- N/A

### Removed
- N/A

### Fixed
- N/A

### Security
- N/A

EOF
    
    # Prepend to CHANGELOG.md
    cat CHANGELOG.tmp CHANGELOG.md > CHANGELOG.new && mv CHANGELOG.new CHANGELOG.md
    rm CHANGELOG.tmp
    
    print_success "Changelog entry created"
}

# Function to commit changes
commit_changes() {
    local version=$1
    
    print_status "Committing changes..."
    
    git add .
    git commit -m "chore(release): $version

- Update version to $version
- Update changelog
- Prepare for release"
    
    print_success "Changes committed"
}

# Function to create tag
create_tag() {
    local version=$1
    
    print_status "Creating tag $version..."
    
    git tag -a "v$version" -m "Release $version"
    
    print_success "Tag $version created"
}

# Function to push changes
push_changes() {
    local version=$1
    
    print_status "Pushing changes..."
    
    git push origin main
    git push origin "v$version"
    
    print_success "Changes pushed to remote"
}

# Function to create release
create_release() {
    local version=$1
    
    print_status "Creating GitHub release..."
    
    # Check if GitHub CLI is available
    if command_exists gh; then
        gh release create "v$version" \
            --title "Release $version" \
            --notes "Laravel Alert $version

## 🚀 New Features
- Enhanced alert system
- Fluent API
- Blade components
- REST API
- Real-time notifications
- Email notifications
- Performance optimizations

## 📦 Installation
\`\`\`bash
composer require wahyudedik/laravel-alert
\`\`\`

## 🚀 Quick Start
\`\`\`php
use Wahyudedik\\LaravelAlert\\Facades\\Alert;
Alert::success('Welcome to Laravel Alert!');
\`\`\`

## 📖 Documentation
- [Complete Documentation](https://wahyudedik.github.io/LaravelAlert)
- [API Reference](https://wahyudedik.github.io/LaravelAlert/api-reference)
- [Examples](https://wahyudedik.github.io/LaravelAlert/examples)

## 🤝 Contributing
We welcome contributions! Please see our [Contributing Guide](https://github.com/wahyudedik/LaravelAlert/blob/main/CONTRIBUTING.md).

## 📄 License
This project is licensed under the MIT License - see the [LICENSE](https://github.com/wahyudedik/LaravelAlert/blob/main/LICENSE) file for details." \
            --draft
    else
        print_warning "GitHub CLI not found, please create release manually"
    fi
    
    print_success "GitHub release created"
}

# Function to notify Packagist
notify_packagist() {
    print_status "Notifying Packagist..."
    
    # Check if Packagist credentials are available
    if [ -z "$PACKAGIST_USERNAME" ] || [ -z "$PACKAGIST_TOKEN" ]; then
        print_warning "Packagist credentials not found, please update manually"
        return
    fi
    
    curl -X POST \
        -H "Content-Type: application/json" \
        -d '{"repository":{"url":"https://github.com/wahyudedik/LaravelAlert"}}' \
        "https://packagist.org/api/update-package?username=$PACKAGIST_USERNAME&apiToken=$PACKAGIST_TOKEN"
    
    print_success "Packagist notified"
}

# Function to display release summary
display_summary() {
    local version=$1
    
    print_success "Release $version completed successfully!"
    echo
    echo "📦 Package: wahyudedik/laravel-alert"
    echo "🏷️  Version: $version"
    echo "📅 Date: $(date)"
    echo
    echo "🔗 Links:"
    echo "  - GitHub Release: https://github.com/wahyudedik/LaravelAlert/releases/tag/v$version"
    echo "  - Packagist: https://packagist.org/packages/wahyudedik/laravel-alert"
    echo "  - Documentation: https://wahyudedik.github.io/LaravelAlert"
    echo
    echo "📖 Installation:"
    echo "  composer require wahyudedik/laravel-alert"
    echo
    echo "🚀 Quick Start:"
    echo "  use Wahyudedik\\LaravelAlert\\Facades\\Alert;"
    echo "  Alert::success('Welcome to Laravel Alert!');"
    echo
}

# Main function
main() {
    local version=$1
    
    if [ -z "$version" ]; then
        print_error "Version is required"
        echo "Usage: $0 <version>"
        echo "Example: $0 1.0.0"
        exit 1
    fi
    
    # Validate version format
    if ! [[ $version =~ ^[0-9]+\.[0-9]+\.[0-9]+$ ]]; then
        print_error "Invalid version format. Use semantic versioning (e.g., 1.0.0)"
        exit 1
    fi
    
    print_status "Starting release process for version $version..."
    echo
    
    # Run all checks and steps
    check_prerequisites
    check_git_repo
    check_working_directory
    check_branch
    check_remote
    run_tests
    update_version "$version"
    create_changelog_entry "$version"
    commit_changes "$version"
    create_tag "$version"
    push_changes "$version"
    create_release "$version"
    notify_packagist
    display_summary "$version"
}

# Run main function with all arguments
main "$@"
