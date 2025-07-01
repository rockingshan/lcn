#!/bin/bash
# LCN Management System - Production Deployment Script
# Usage: ./deploy.sh [production|staging]

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
PROJECT_NAME="LCN Management System"
BACKUP_DIR="./backups"
LOG_DIR="./logs"
UPLOAD_DIR="./uploads"

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to create backup
create_backup() {
    print_status "Creating database backup..."
    timestamp=$(date +"%Y%m%d_%H%M%S")
    backup_file="${BACKUP_DIR}/db_backup_${timestamp}.sql"
    
    if [ ! -d "$BACKUP_DIR" ]; then
        mkdir -p "$BACKUP_DIR"
    fi
    
    # Create database backup (update credentials as needed)
    mysqldump -u root -p meghbela_lcn_db_kol > "$backup_file" 2>/dev/null || {
        print_warning "Database backup failed. Continuing..."
    }
    
    print_status "Backup created: $backup_file"
}

# Function to set proper permissions
set_permissions() {
    print_status "Setting proper file permissions..."
    
    # Set directory permissions
    find . -type d -exec chmod 755 {} \;
    
    # Set file permissions
    find . -type f -exec chmod 644 {} \;
    
    # Set executable permissions for scripts
    chmod +x deploy.sh
    chmod +x backup.sh
    
    # Set special permissions for uploads and logs
    chmod 755 uploads/
    chmod 755 logs/
    chmod 755 backups/
    
    print_status "Permissions set successfully"
}

# Function to create required directories
create_directories() {
    print_status "Creating required directories..."
    
    mkdir -p logs
    mkdir -p uploads/challans
    mkdir -p backups
    
    print_status "Directories created successfully"
}

# Function to install dependencies
install_dependencies() {
    print_status "Installing Composer dependencies..."
    
    if command -v composer &> /dev/null; then
        composer install --no-dev --optimize-autoloader
        print_status "Dependencies installed successfully"
    else
        print_error "Composer not found. Please install Composer first."
        exit 1
    fi
}

# Function to optimize for production
optimize_production() {
    print_status "Optimizing for production..."
    
    # Copy production configuration
    if [ -f "config/production.php" ]; then
        cp config/production.php config/app.php
        print_status "Production configuration applied"
    fi
    
    # Clear any cache files
    find . -name "*.cache" -delete 2>/dev/null || true
    find . -name "*.tmp" -delete 2>/dev/null || true
    
    print_status "Production optimization completed"
}

# Function to run security checks
security_checks() {
    print_status "Running security checks..."
    
    # Check for sensitive files
    if [ -f ".env" ]; then
        print_warning "Found .env file. Ensure it's properly configured for production."
    fi
    
    # Check file permissions
    if [ -r "config/database.php" ]; then
        print_warning "Database config is readable. Consider restricting access."
    fi
    
    print_status "Security checks completed"
}

# Function to test the application
test_application() {
    print_status "Testing application..."
    
    # Test database connection
    php -r "
    require_once 'config/database.php';
    try {
        \$auth = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (\$auth->connect_error) {
            throw new Exception('Database connection failed');
        }
        echo 'Database connection: OK\n';
    } catch (Exception \$e) {
        echo 'Database connection: FAILED\n';
        exit(1);
    }
    " || {
        print_error "Database connection test failed"
        exit 1
    }
    
    print_status "Application test completed successfully"
}

# Main deployment function
deploy() {
    local environment=$1
    
    print_status "Starting deployment for $PROJECT_NAME ($environment)"
    
    # Create backup
    create_backup
    
    # Create directories
    create_directories
    
    # Install dependencies
    install_dependencies
    
    # Set permissions
    set_permissions
    
    # Optimize for production
    if [ "$environment" = "production" ]; then
        optimize_production
    fi
    
    # Run security checks
    security_checks
    
    # Test application
    test_application
    
    print_status "Deployment completed successfully!"
    print_status "Please update your database credentials in config/database.php"
    print_status "Ensure your web server is configured to use the .htaccess file"
}

# Check if environment is specified
if [ $# -eq 0 ]; then
    print_error "Please specify environment: ./deploy.sh [production|staging]"
    exit 1
fi

# Run deployment
deploy "$1" 