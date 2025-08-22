# 3. Configure Build Environment

## 3.1 Set Language Versions
1. Go to Project → Build Configuration
2. Set PHP Version: 8.2
3. Set Node.js Version: 18.x LTS
4. Set Composer Version: 2.x latest
5. Save changes

## 3.2 Configure Build Cache
1. Click "Build Cache Files"
2. Add cache entry: ~/.composer/cache
3. Add cache entry: ~/.npm
4. Add cache entry: vendor
5. Save cache settings

## 3.3 Add SSH Known Hosts
1. Click "Build Known Hosts"
2. Add host: github.com
3. Add SSH key for GitHub
4. Save settings

## 3.4 Build Environment Ready
✅ PHP 8.2 configured
✅ Node.js 18.x configured  
✅ Build cache enabled
✅ SSH security configured

**Next: 4-Add-Build-Commands.md**
