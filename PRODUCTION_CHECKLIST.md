# LCN Management System - Production Deployment Checklist

## 🚀 Pre-Deployment Checklist

### **1. Database Configuration**
- [ ] Update database credentials in `config/database.php`
- [ ] Ensure database user has proper permissions
- [ ] Test database connection
- [ ] Create database backup before deployment
- [ ] Verify all tables exist and have correct structure

### **2. Server Requirements**
- [ ] PHP 7.4+ installed
- [ ] MySQL 5.7+ or MariaDB 10.2+ installed
- [ ] Apache/Nginx with mod_rewrite enabled
- [ ] Composer installed
- [ ] Required PHP extensions: mysqli, mbstring, zip, gd

### **3. Security Configuration**
- [ ] Update database passwords to strong passwords
- [ ] Configure HTTPS/SSL certificate
- [ ] Set proper file permissions (755 for directories, 644 for files)
- [ ] Remove or secure development files
- [ ] Update admin user credentials

### **4. File Structure**
- [ ] Ensure all required directories exist:
  - `logs/`
  - `uploads/challans/`
  - `backups/`
- [ ] Set proper permissions for upload directories
- [ ] Verify .htaccess file is in place

## 🔧 Deployment Steps

### **Step 1: Prepare Production Environment**
```bash
# Clone or upload code to production server
# Run deployment script
./deploy.sh production
```

### **Step 2: Configure Database**
1. Update `config/database.php` with production credentials
2. Import database schema if needed
3. Test database connection

### **Step 3: Configure Web Server**

#### **Apache Configuration**
```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/lcn
    
    <Directory /path/to/lcn>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/lcn_error.log
    CustomLog ${APACHE_LOG_DIR}/lcn_access.log combined
</VirtualHost>
```

#### **Nginx Configuration**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/lcn;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### **Step 4: SSL/HTTPS Setup**
1. Obtain SSL certificate (Let's Encrypt recommended)
2. Configure HTTPS redirect
3. Update .htaccess file (uncomment HTTPS rules)

### **Step 5: Final Configuration**
1. Update `config/production.php` with correct settings
2. Set up automated backups
3. Configure log rotation
4. Test all functionality

## 🔒 Security Hardening

### **File Permissions**
```bash
# Set directory permissions
find . -type d -exec chmod 755 {} \;

# Set file permissions
find . -type f -exec chmod 644 {} \;

# Set special permissions
chmod 755 uploads/
chmod 755 logs/
chmod 755 backups/
```

### **Database Security**
- [ ] Use dedicated database user with minimal privileges
- [ ] Enable database logging
- [ ] Regular database backups
- [ ] Monitor database access

### **Application Security**
- [ ] Enable error logging (disable display_errors)
- [ ] Configure session security
- [ ] Implement rate limiting
- [ ] Regular security updates

## 📊 Monitoring & Maintenance

### **Log Monitoring**
- [ ] Set up log rotation
- [ ] Monitor error logs
- [ ] Set up alerts for critical errors
- [ ] Regular log analysis

### **Backup Strategy**
```bash
# Daily backups
0 2 * * * /path/to/lcn/backup.sh daily

# Weekly backups
0 3 * * 0 /path/to/lcn/backup.sh weekly

# Monthly backups
0 4 1 * * /path/to/lcn/backup.sh monthly
```

### **Performance Optimization**
- [ ] Enable OPcache
- [ ] Configure MySQL query cache
- [ ] Enable gzip compression
- [ ] Optimize images and assets

## 🧪 Testing Checklist

### **Functional Testing**
- [ ] User login/logout
- [ ] City switching functionality
- [ ] CRUD operations for all modules
- [ ] Excel export functionality
- [ ] File upload functionality
- [ ] Search and filter functionality

### **Security Testing**
- [ ] SQL injection prevention
- [ ] XSS protection
- [ ] CSRF protection
- [ ] File upload security
- [ ] Session security

### **Performance Testing**
- [ ] Page load times
- [ ] Database query performance
- [ ] File upload performance
- [ ] Concurrent user testing

## 🚨 Emergency Procedures

### **Rollback Plan**
1. Keep previous version backup
2. Database rollback procedure
3. File system rollback
4. DNS rollback (if applicable)

### **Disaster Recovery**
1. Database recovery procedure
2. File system recovery
3. Configuration recovery
4. Communication plan

## 📞 Support Information

### **Contact Details**
- **System Administrator**: [Your Name/Contact]
- **Database Administrator**: [DBA Contact]
- **Hosting Provider**: [Provider Contact]
- **Emergency Contact**: [Emergency Contact]

### **Documentation**
- [ ] User manual
- [ ] Admin manual
- [ ] API documentation (if applicable)
- [ ] Troubleshooting guide

## ✅ Post-Deployment Verification

### **Immediate Checks**
- [ ] All pages load correctly
- [ ] Database connections work
- [ ] File uploads function
- [ ] Excel exports work
- [ ] User authentication works
- [ ] Logs are being written

### **24-Hour Monitoring**
- [ ] Monitor error logs
- [ ] Check performance metrics
- [ ] Verify backup processes
- [ ] Test user workflows
- [ ] Monitor server resources

### **Weekly Maintenance**
- [ ] Review error logs
- [ ] Check backup integrity
- [ ] Monitor disk space
- [ ] Update security patches
- [ ] Performance optimization

---

**Last Updated**: [Date]
**Version**: 1.0
**Prepared By**: [Your Name] 