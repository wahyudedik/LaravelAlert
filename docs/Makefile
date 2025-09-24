# Laravel Alert Documentation Makefile

.PHONY: help dev build deploy clean lint spell test preview install

# Default target
help:
	@echo "Laravel Alert Documentation"
	@echo "=========================="
	@echo ""
	@echo "Available commands:"
	@echo "  dev       - Start development server"
	@echo "  build     - Build documentation"
	@echo "  deploy    - Deploy to GitHub Pages"
	@echo "  clean     - Clean build files"
	@echo "  lint      - Lint markdown files"
	@echo "  spell     - Check spelling"
	@echo "  test      - Run tests"
	@echo "  preview   - Preview built documentation"
	@echo "  install   - Install dependencies"
	@echo ""

# Development server
dev:
	@echo "Starting development server..."
	@mkdocs serve --dev-addr=0.0.0.0:8000

# Build documentation
build:
	@echo "Building documentation..."
	@mkdocs build

# Deploy to GitHub Pages
deploy:
	@echo "Deploying to GitHub Pages..."
	@mkdocs gh-deploy

# Clean build files
clean:
	@echo "Cleaning build files..."
	@rm -rf site/
	@rm -rf .cache/

# Lint markdown files
lint:
	@echo "Linting markdown files..."
	@markdownlint docs/*.md

# Check spelling
spell:
	@echo "Checking spelling..."
	@cspell 'docs/**/*.md'

# Run tests
test:
	@echo "No tests for documentation"

# Preview built documentation
preview:
	@echo "Previewing built documentation..."
	@mkdocs serve --dev-addr=0.0.0.0:8000

# Install dependencies
install:
	@echo "Installing dependencies..."
	@pip install -r requirements.txt

# Docker commands
docker-dev:
	@echo "Starting development server with Docker..."
	@docker-compose up docs

docker-build:
	@echo "Building documentation with Docker..."
	@docker-compose run build

docker-deploy:
	@echo "Deploying with Docker..."
	@docker-compose run deploy

# CI/CD commands
ci-install:
	@echo "Installing dependencies for CI..."
	@pip install --no-cache-dir -r requirements.txt

ci-build:
	@echo "Building documentation for CI..."
	@mkdocs build

ci-deploy:
	@echo "Deploying documentation for CI..."
	@mkdocs gh-deploy --force

# Development commands
dev-watch:
	@echo "Starting development server with auto-reload..."
	@mkdocs serve --dev-addr=0.0.0.0:8000 --livereload

dev-verbose:
	@echo "Starting development server with verbose output..."
	@mkdocs serve --dev-addr=0.0.0.0:8000 --verbose

# Build commands
build-clean:
	@echo "Building documentation with clean build..."
	@mkdocs build --clean

build-strict:
	@echo "Building documentation with strict mode..."
	@mkdocs build --strict

# Deploy commands
deploy-force:
	@echo "Force deploying to GitHub Pages..."
	@mkdocs gh-deploy --force

deploy-branch:
	@echo "Deploying to specific branch..."
	@mkdocs gh-deploy --remote-branch gh-pages

# Utility commands
check:
	@echo "Checking documentation..."
	@mkdocs build --strict
	@markdownlint docs/*.md
	@cspell 'docs/**/*.md'

validate:
	@echo "Validating documentation..."
	@mkdocs build --strict
	@echo "Documentation validation complete"

# Help for specific commands
help-dev:
	@echo "Development Commands:"
	@echo "  dev         - Start development server"
	@echo "  dev-watch   - Start with auto-reload"
	@echo "  dev-verbose - Start with verbose output"

help-build:
	@echo "Build Commands:"
	@echo "  build       - Build documentation"
	@echo "  build-clean - Build with clean build"
	@echo "  build-strict- Build with strict mode"

help-deploy:
	@echo "Deploy Commands:"
	@echo "  deploy      - Deploy to GitHub Pages"
	@echo "  deploy-force- Force deploy"
	@echo "  deploy-branch- Deploy to specific branch"

help-docker:
	@echo "Docker Commands:"
	@echo "  docker-dev   - Start with Docker"
	@echo "  docker-build - Build with Docker"
	@echo "  docker-deploy- Deploy with Docker"
