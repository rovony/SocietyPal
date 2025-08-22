# 5. Add Server

## 5.1 Server Configuration
1. Go to Project Settings → Servers
2. Click "Add New Server"
3. Server name: "Production Server"
4. Protocol: SSH/SFTP
5. Enter your server hostname/IP
6. Enter SSH port (usually 22)
7. Enter SSH username

## 5.2 SSH Key Setup
1. Upload your private SSH key
2. Test connection
3. Should show green "Connected" status
4. Save server settings

## 5.3 Deployment Path
1. Set deployment path: /home/username/domains/yoursite.com/deploy/releases/%RELEASE%
2. Set shared files: .env
3. Set shared directories: storage
4. Save deployment settings

## 5.4 Server Added
✅ Server connection working
✅ SSH access configured
✅ Deployment paths set
✅ Ready for SSH commands

**Next: 6-Add-SSH-Commands.md**
