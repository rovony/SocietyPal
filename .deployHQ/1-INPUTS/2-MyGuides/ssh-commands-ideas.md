SSH Commands
New SSH Command
About SSH Commands
Dismiss
You can configure commands to run on your server using SSH here, such as to start or stop running services.

This commands can be triggered before or after your deployments.

Before Changes
Executed immediately once connected to the server.

A01-System-Preflight-Checks
All Servers

Every deployment

Edit
Remove
A02-Backup-Current-Release v1
All Servers

Every deployment

Edit
Remove
A02: Backup Current Release-v2
All Servers

Every deployment

Edit
Remove
A03: Database Backup v1
All Servers

Every deployment

Edit
Remove
A03: Database Backup v2
All Servers

Every deployment

Edit
Remove
A04: Enter Maintenance Mode v1
All Servers

Every deployment

Edit
Remove
A04: Enter Maintenance Mode v2
All Servers

Every deployment

Edit
Remove
Before Release Link
Executed after files have been uploaded/removed, but before the new release has been made active (zero-downtime only).

B01: Initialize Shared Environment
All Servers

Every deployment

Edit
Remove
B01b: DeployHQ .env Handler
All Servers

Every deployment

Edit
Remove
B02: Link Shared Environment
All Servers

Every deployment

Edit
Remove
B03: Create and Link Shared Directories v1
All Servers

Every deployment

Edit
Remove
B03: Create and Link Shared Directories v2
All Servers

Every deployment

Edit
Remove
B04: Run Database Migrations
All Servers

Every deployment

Edit
Remove
B05: Optimize Application v1
All Servers

Every deployment

Edit
Remove
B05: Optimize Application v2
All Servers

Every deployment

Edit
Remove
B05: Optimize Application-v3
All Servers

Every deployment

Edit
Remove
B06: Create Directory Structure
All Servers

Every deployment

Edit
Remove
B06: Create Directory Structure -v2 (OPTIMIZED)
All Servers

Every deployment

Edit
Remove
After Changes
Executed at the end of a deployment after files have been uploaded/removed.

C01: Create Storage Symlink
All Servers

Every deployment

Edit
Remove
C02: Restart Services
All Servers

Every deployment

Edit
Remove
C03: Exit Maintenance Mode
All Servers

Every deployment

Edit
Remove
C04: Health Checks
All Servers

Every deployment

Edit
Remove
C05: Fix Symlink Issues-v1
All Servers

Every deployment

Edit
Remove
C05: Fix Symlink Issues-v2
All Servers

Every deployment

Edit
Remove
C06: Build Assets - Build-on-Server strategy only
All Servers

Every deployment

Edit
Remove
C07: Cleanup Old Releases
All Servers

Every deployment

Edit
Remove
C08: Send Deployment Notification
All Servers

Every deployment

Edit
