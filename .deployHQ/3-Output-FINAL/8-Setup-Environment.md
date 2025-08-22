# 8. Setup Environment File

## 8.1 Access Server
1. SSH into your server
2. Navigate to: /home/username/domains/yoursite.com/deploy/shared/
3. Edit the .env file
4. Use nano or vim editor

## 8.2 Configure Production Settings
1. Set APP_ENV=production
2. Set APP_DEBUG=false
3. Set your APP_URL=https://yoursite.com
4. Generate APP_KEY if needed
5. Configure database credentials
6. Set mail settings
7. Save the file

## 8.3 Set File Permissions
1. Set .env permissions: chmod 600 .env
2. Set storage permissions: chmod -R 775 storage
3. Verify permissions are correct

## 8.4 Environment Ready
✅ .env file configured
✅ Production settings applied
✅ Database connected
✅ Mail configured

**Next: 9-Test-Deployment.md**
