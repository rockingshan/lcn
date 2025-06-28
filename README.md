# LCN Management System

A modern, secure, and feature-rich LCN (Logical Channel Number) management system for cable TV operators. Built with PHP, MySQL, and Tailwind CSS.

## 🚀 Features

- **Multi-City Support**: Manage LCN data across multiple cities (Kolkata, Chandipur, Berhampore, SITI Headend)
- **Channel Management**: Complete CRUD operations for channels, SIDs, and LCN mappings
- **IRD Inventory**: Track broadcaster IRD equipment with STB and VC numbers
- **IRD Challan Management**: Upload and manage IRD challan documents
- **Excel Export**: Export data to Excel format with city-specific naming
- **Activity Logging**: Comprehensive audit trail of all system activities
- **Modern UI**: Responsive design with Tailwind CSS and glassmorphism effects
- **Security**: Session-based authentication with bcrypt password hashing

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or MariaDB 10.2 or higher
- Apache/Nginx with mod_rewrite enabled
- Composer
- PHP Extensions: mysqli, mbstring, zip, gd

## 🛠️ Installation

### Development Setup

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd lcn
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure database**
   - Copy `config/database.php.example` to `config/database.php`
   - Update database credentials

4. **Import database schema**
   ```bash
   mysql -u username -p database_name < include/meghbela_lcn_db_kol.sql
   ```

5. **Set up directories**
   ```bash
   mkdir -p logs uploads/challans backups
   chmod 755 logs uploads/challans backups
   ```

6. **Access the application**
   - Navigate to your web server URL
   - Default login: admin/admin

### Production Deployment

1. **Run deployment script**
   ```bash
   ./deploy.sh production
   ```

2. **Configure production settings**
   - Update `config/production.php` with your settings
   - Configure web server (Apache/Nginx)
   - Set up SSL certificate

3. **Set up automated backups**
   ```bash
   # Add to crontab
   0 2 * * * /path/to/lcn/backup.sh daily
   ```

See [PRODUCTION_CHECKLIST.md](PRODUCTION_CHECKLIST.md) for detailed deployment instructions.

## 🏗️ Architecture

### Directory Structure
```
lcn/
├── config/                 # Configuration files
├── src/
│   ├── Controllers/       # Application controllers
│   └── LogHelper.php      # Logging utility
├── views/                 # View templates
│   └── partials/          # Reusable view components
├── uploads/               # File uploads
├── logs/                  # Application logs
├── backups/               # Database backups
├── vendor/                # Composer dependencies
├── index.php              # Front controller
├── .htaccess              # URL rewriting rules
└── composer.json          # Dependencies
```

### Key Components

- **Front Controller**: `index.php` handles all requests with FastRoute
- **Controllers**: Handle business logic and data processing
- **Views**: Template files with Tailwind CSS styling
- **Database**: MySQL with optimized queries and relationships
- **Security**: Session-based authentication with proper validation

## 🔧 Configuration

### Environment Variables
- `BASE_PATH`: Application base URL path
- `DB_HOST`: Database host
- `DB_NAME`: Database name
- `DB_USER`: Database username
- `DB_PASS`: Database password

### City Configuration
The system supports multiple cities:
- Kolkata (ID: 1)
- Chandipur (ID: 2)
- Berhampore (ID: 3)
- SITI Headend (ID: 4)

## 📊 Features Overview

### Dashboard
- View all channel mappings for current city
- Search and sort functionality
- Quick action buttons for editing, modifying LCN, and managing IRD

### Channel Management
- Add/edit channels with broadcaster information
- Manage SID (Service ID) assignments
- Create channel mappings with LCN numbers

### IRD Inventory
- Track STB (Set-Top Box) and VC (Video Controller) numbers
- Link IRD equipment to channels
- Export inventory data to Excel

### IRD Challan Management
- Upload PDF challan documents
- Associate challans with broadcasters
- Track challan dates and details

### Activity Logs
- Comprehensive audit trail
- User action tracking
- IP address logging
- Export logs to Excel

## 🔒 Security Features

- **Session Security**: HttpOnly cookies, secure session handling
- **Input Validation**: All user inputs are validated and sanitized
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Output escaping and security headers
- **File Upload Security**: Type and size validation
- **Access Control**: Session-based authentication

## 📈 Performance Optimizations

- **Database Indexing**: Optimized queries with proper indexes
- **Caching**: Browser caching for static assets
- **Compression**: Gzip compression for faster loading
- **Optimized Assets**: Minified CSS and JavaScript
- **Efficient Queries**: Optimized database queries with joins

## 🛡️ Backup & Recovery

### Automated Backups
```bash
# Daily backups
./backup.sh daily

# Weekly backups
./backup.sh weekly

# Monthly backups
./backup.sh monthly
```

### Backup Contents
- Database dump (compressed)
- Uploaded files
- Application logs
- Configuration files

## 🔧 Maintenance

### Regular Tasks
- Monitor error logs
- Check backup integrity
- Update security patches
- Performance optimization
- Database maintenance

### Troubleshooting
- Check `logs/` directory for error logs
- Verify database connectivity
- Ensure proper file permissions
- Review web server configuration

## 📞 Support

For technical support or questions:
- Check the [PRODUCTION_CHECKLIST.md](PRODUCTION_CHECKLIST.md) for deployment issues
- Review error logs in the `logs/` directory
- Ensure all requirements are met

## 📄 License

This project is proprietary software developed for Meghbela Digital.

## 🚀 Version History

### v1.0.0 (Current)
- Initial production release
- Multi-city LCN management
- IRD inventory tracking
- Excel export functionality
- Modern UI with Tailwind CSS
- Comprehensive security features
- Automated backup system

---

**Developed by**: [Your Name/Company]
**Last Updated**: [Date]
**Version**: 1.0.0
