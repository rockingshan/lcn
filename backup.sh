#!/bin/bash
# LCN Management System - Automated Backup Script
# Usage: ./backup.sh [daily|weekly|monthly]

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
DB_NAME="meghbela_lcn_db_kol"
DB_USER="root"
DB_PASS=""

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

# Function to create database backup
backup_database() {
    local backup_type=$1
    local timestamp=$(date +"%Y%m%d_%H%M%S")
    local backup_file="${BACKUP_DIR}/db_${backup_type}_${timestamp}.sql"
    
    print_status "Creating database backup..."
    
    # Create backup directory if it doesn't exist
    mkdir -p "$BACKUP_DIR"
    
    # Create database backup
    if [ -n "$DB_PASS" ]; then
        mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$backup_file" 2>/dev/null || {
            print_error "Database backup failed"
            return 1
        }
    else
        mysqldump -u "$DB_USER" "$DB_NAME" > "$backup_file" 2>/dev/null || {
            print_error "Database backup failed"
            return 1
        }
    fi
    
    # Compress the backup
    gzip "$backup_file"
    print_status "Database backup created: ${backup_file}.gz"
    
    echo "${backup_file}.gz"
}

# Function to backup uploads
backup_uploads() {
    local backup_type=$1
    local timestamp=$(date +"%Y%m%d_%H%M%S")
    local backup_file="${BACKUP_DIR}/uploads_${backup_type}_${timestamp}.tar.gz"
    
    print_status "Creating uploads backup..."
    
    if [ -d "$UPLOAD_DIR" ]; then
        tar -czf "$backup_file" -C "$(dirname "$UPLOAD_DIR")" "$(basename "$UPLOAD_DIR")" 2>/dev/null || {
            print_error "Uploads backup failed"
            return 1
        }
        print_status "Uploads backup created: $backup_file"
        echo "$backup_file"
    else
        print_warning "Uploads directory not found"
        echo ""
    fi
}

# Function to backup logs
backup_logs() {
    local backup_type=$1
    local timestamp=$(date +"%Y%m%d_%H%M%S")
    local backup_file="${BACKUP_DIR}/logs_${backup_type}_${timestamp}.tar.gz"
    
    print_status "Creating logs backup..."
    
    if [ -d "$LOG_DIR" ]; then
        tar -czf "$backup_file" -C "$(dirname "$LOG_DIR")" "$(basename "$LOG_DIR")" 2>/dev/null || {
            print_error "Logs backup failed"
            return 1
        }
        print_status "Logs backup created: $backup_file"
        echo "$backup_file"
    else
        print_warning "Logs directory not found"
        echo ""
    fi
}

# Function to clean old backups
cleanup_old_backups() {
    local backup_type=$1
    local retention_days=30
    
    case $backup_type in
        "daily")
            retention_days=7
            ;;
        "weekly")
            retention_days=30
            ;;
        "monthly")
            retention_days=365
            ;;
    esac
    
    print_status "Cleaning up backups older than $retention_days days..."
    
    find "$BACKUP_DIR" -name "*_${backup_type}_*.gz" -mtime +$retention_days -delete 2>/dev/null || true
    find "$BACKUP_DIR" -name "*_${backup_type}_*.sql" -mtime +$retention_days -delete 2>/dev/null || true
    
    print_status "Cleanup completed"
}

# Function to log backup
log_backup() {
    local backup_type=$1
    local db_backup=$2
    local uploads_backup=$3
    local logs_backup=$4
    
    local log_file="${LOG_DIR}/backup.log"
    local timestamp=$(date +"%Y-%m-%d %H:%M:%S")
    
    mkdir -p "$LOG_DIR"
    
    echo "[$timestamp] Backup completed - Type: $backup_type" >> "$log_file"
    if [ -n "$db_backup" ]; then
        echo "[$timestamp] Database: $db_backup" >> "$log_file"
    fi
    if [ -n "$uploads_backup" ]; then
        echo "[$timestamp] Uploads: $uploads_backup" >> "$log_file"
    fi
    if [ -n "$logs_backup" ]; then
        echo "[$timestamp] Logs: $logs_backup" >> "$log_file"
    fi
    echo "---" >> "$log_file"
}

# Function to send notification (optional)
send_notification() {
    local backup_type=$1
    local status=$2
    
    # This is a placeholder for email/SMS notifications
    # You can implement your preferred notification method here
    
    if [ "$status" = "success" ]; then
        print_status "Backup completed successfully"
    else
        print_error "Backup failed"
    fi
}

# Main backup function
backup() {
    local backup_type=$1
    
    if [ -z "$backup_type" ]; then
        backup_type="daily"
    fi
    
    print_status "Starting $backup_type backup for $PROJECT_NAME"
    
    # Create backup directory
    mkdir -p "$BACKUP_DIR"
    
    # Perform backups
    db_backup=$(backup_database "$backup_type")
    uploads_backup=$(backup_uploads "$backup_type")
    logs_backup=$(backup_logs "$backup_type")
    
    # Clean up old backups
    cleanup_old_backups "$backup_type"
    
    # Log the backup
    log_backup "$backup_type" "$db_backup" "$uploads_backup" "$logs_backup"
    
    # Send notification
    send_notification "$backup_type" "success"
    
    print_status "$backup_type backup completed successfully!"
}

# Check if backup type is specified
if [ $# -eq 0 ]; then
    backup "daily"
else
    case $1 in
        "daily"|"weekly"|"monthly")
            backup "$1"
            ;;
        *)
            print_error "Invalid backup type. Use: daily, weekly, or monthly"
            exit 1
            ;;
    esac
fi 