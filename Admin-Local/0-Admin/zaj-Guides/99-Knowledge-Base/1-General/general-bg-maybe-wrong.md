## Background

- DeployHQ Variables
    - DeployHQ Variables - **27 unique variables**
        
        # DeployHQ Variables Reference
        
        ## 1. Intro
        
        > Note: The variables below are defined by the DeployHQ system and can be used in deployment scripts, notifications, and configurations. These variables provide dynamic access to project information, server details, repository data, and deployment context during the deployment process.
        > 
        
        **Total Variables Available: 27 unique variables**
        
        - Project Variables: 18
        - Server Variables: 5
        - Zero-Downtime Variables: 4
        - Universal Variables: 1 (`%release_path%` - also available on all servers)
        
        *Note: `%release_path%` appears in both Zero-Downtime and Universal categories with different usage contexts.*
        
        ## Table of Contents
        
        - [DeployHQ Variables Reference](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
            - [1. Intro](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
            - [Table of Contents](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
            - [2. DeployHQ Variables](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Project Variables](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Server Variables](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Zero-Downtime Deployment Variables](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Universal Variables](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
            - [Example Usage](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Basic Variable Usage](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Conditional Logic](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [File Operations](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Logging and Notifications](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Zero-Downtime vs Universal Release Path](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
                - [Advanced Variable Combinations](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        
        ---
        
        ## 2. DeployHQ Variables
        
        ### Project Variables
        
        Variables that refer to the project and deployment context (18 variables).
        
        | Variable | Category | Description | Example Usage |
        | --- | --- | --- | --- |
        | `%startrev%` | Revisions | Start revision reference | `'Deployment from %startrev% to %endrev%'` in notifications |
        | `%endrev%` | Revisions | End revision reference | `echo "Deploying to revision %endrev%"` in SSH commands |
        | `%startrevmsg%` | Messages | Start revision commit message *(available on deployment finish or failure)* | `'Started from: %startrevmsg%'` in notifications |
        | `%endrevmsg%` | Messages | End revision commit message *(available on deployment finish or failure)* | `'Deploying: %endrevmsg%'` to show what changes are being deployed |
        | `%tag%` | Branch & Tags | Tag related to end revision *(if present)* | `'Deploying version %tag%'` for release versioning |
        | `%branch%` | Branch & Tags | The branch of the deployment | `ENV["BRANCH"]="%branch%"` in config files |
        | `%count%` | Context | The number of deployments in the project | `'This is deployment #%count%'` for tracking frequency |
        | `%servers%` | Context | Names of the servers in this deployment | `'Deploying to servers: %servers%'` in notifications |
        | `%deployer%` | Context | User who started the deployment *(if manually deployed)* | `'Deployment initiated by %deployer%'` for attribution |
        | `%commitrange%` | Revisions | The start and end commit, separated with a hyphen | `'Changes: %commitrange%'` in notifications |
        | `%project%` | Identity | The name of this project in Deploy | `PROJECT_NAME="%project%"` in config files |
        | `%projecturl%` | URLs | The address of this project in DeployHQ | `<a href="%projecturl%">View Project</a>` for dashboard links |
        | `%projectperma%` | URLs | The permalink of this project in DeployHQ | Permanent project reference in API calls |
        | `%deploymenturl%` | URLs | The address of this deployment in DeployHQ | `<a href="%deploymenturl%">View Deployment</a>` |
        | `%status%` | Status | The current status of this deployment | `'Deployment status: %status%'` for progress tracking |
        | `%started_at%` | Timing | The ISO8601 timestamp representing the start time of the deployment | `'Started at %started_at%'` for timing logs |
        | `%completed_at%` | Timing | The ISO8601 timestamp representing the completion time of the deployment | `'Completed at %completed_at%'` for duration calculation |
        | `%deployment_overview%` | Content | The parsed and stripped deployment overview *(if provided)* | `'Overview: %deployment_overview%'` in notifications |
        
        ### Server Variables
        
        Variables that refer to a specific server or server group (5 variables).
        
        | Variable | Category | Description | Example Usage |
        | --- | --- | --- | --- |
        | `%environment%` | Environment | Server environment *(development, production, etc.)* | `ENV["RAILS_ENV"]="%environment%"` or `npm run %environment%` |
        | `%username%` | Authentication | Username used to login to the server | `chown %username%:www-data` for file permissions |
        | `%password%` | Authentication | Password used to login to the server | Automated authentication *(not recommended for security)* |
        | `%groupindex%` | Configuration | **Server group only.** The order within its group that this server is deployed *(zero-indexed)* | `if [ %groupindex% -eq 0 ]; then initialize_db; fi` |
        | `%path%` | Paths | Base server path we're deploying to | `cd %path%` or `composer install -d %path%` |
        
        ### Zero-Downtime Deployment Variables
        
        Variables that can be used on servers configured for zero-downtime deployments (4 variables).
        
        | Variable | Category | Description | Example Usage |
        | --- | --- | --- | --- |
        | `%release_path%` | Paths | Path that the release is being deployed to *(zero-downtime context)* | `cd %release_path% && npm run build` for building in release folder |
        | `%shared_path%` | Paths | Path containing shared files that will be symlinked to release | `ln -sf %shared_path%/uploads %release_path%/public/uploads` |
        | `%current_path%` | Paths | Symlink pointing to the currently active release | `if [ -L %current_path% ]; then backup_current; fi` |
        | `%previous_release_path%` | Paths | Path to the previous release | `ln -sf %previous_release_path% %current_path%` for rollbacks |
        
        ### Universal Variables
        
        Variables that can be used on all servers (1 variable).
        
        | Variable | Category | Description | Example Usage |
        | --- | --- | --- | --- |
        | `%release_path%` | Paths | Path that the release is being deployed to *(universal context)* | `cd %release_path% && php artisan migrate` for any server type |
        
        ---
        
        ## Example Usage
        
        ### Basic Variable Usage
        
        **Description:** Simple variable substitution in commands and scripts.
        
        **Example:**
        
        ```bash
        echo "=== Deployment Information ==="
        echo "Project: %project%"
        echo "Environment: %environment%"
        echo "Branch: %branch%"
        echo "Commit: %endrev%"
        echo "Deployment #%count%"
        echo "Overview: %deployment_overview%"
        
        ```
        
        **Expected Output:**
        
        ```
        === Deployment Information ===
        Project: my-laravel-app
        Environment: production
        Branch: main
        Commit: a1b2c3d4
        Deployment #42
        Overview: Bug fixes and performance improvements
        
        ```
        
        ### Conditional Logic
        
        **Description:** Using variables in conditional statements for environment-specific actions.
        
        **Example:**
        
        ```bash
        if [[ "%environment%" =~ ^(integration|testing|staging)$ ]]; then
          npm ci
          npm run build:dev
          echo "Development build completed"
        elif [ "%environment%" = "production" ]; then
          npm ci --omit=dev
          npm run build:prod
          echo "Production build completed"
        fi
        
        # Check if deployment overview exists
        if [ -n "%deployment_overview%" ]; then
          echo "Deployment notes: %deployment_overview%"
        fi
        
        ```
        
        **Expected Output:**
        
        ```
        Production build completed
        Deployment notes: Bug fixes and performance improvements
        
        ```
        
        ### File Operations
        
        **Description:** Using path variables for file operations and directory navigation.
        
        **Example:**
        
        ```bash
        # Navigate to release directory (works on all servers)
        cd %release_path%
        
        # Create deployment info file
        cat > deployment-info.txt << EOF
        Deployment #%count%
        Project: %project%
        Branch: %branch%
        Commit: %endrev%
        Deployed by: %deployer%
        Timestamp: %started_at%
        Overview: %deployment_overview%
        Server: %servers%
        Environment: %environment%
        EOF
        
        # Set permissions
        chmod 644 deployment-info.txt
        
        ```
        
        **Expected Output:**
        
        ```
        deployment-info.txt created with:
        Deployment #42
        Project: my-laravel-app
        Branch: main
        Commit: a1b2c3d4
        Deployed by: john.doe
        Timestamp: 2025-01-30T16:45:00Z
        Overview: Bug fixes and performance improvements
        Server: web-01,web-02
        Environment: production
        
        ```
        
        ### Logging and Notifications
        
        **Description:** Creating detailed logs and notifications using deployment variables.
        
        **Example:**
        
        ```bash
        # Create deployment log entry
        if [ -n "%shared_path%" ]; then
          LOG_FILE="%shared_path%/logs/deployments.log"
        else
          LOG_FILE="%release_path%/deployments.log"
        fi
        
        echo "[$(date)] Deployment #%count% - %project% (%environment%)" >> "$LOG_FILE"
        echo "  Branch: %branch% | Commit: %endrev%" >> "$LOG_FILE"
        echo "  Deployer: %deployer% | Status: %status%" >> "$LOG_FILE"
        echo "  Started: %started_at% | Completed: %completed_at%" >> "$LOG_FILE"
        echo "  Overview: %deployment_overview%" >> "$LOG_FILE"
        
        # Send Slack notification
        curl -X POST "<https://hooks.slack.com/webhook>" \\
          -H "Content-Type: application/json" \\
          -d "{
            \\"text\\": \\"🚀 Deployment Complete\\",
            \\"attachments\\": [{
              \\"color\\": \\"good\\",
              \\"fields\\": [
                {\\"title\\": \\"Project\\", \\"value\\": \\"%project%\\", \\"short\\": true},
                {\\"title\\": \\"Environment\\", \\"value\\": \\"%environment%\\", \\"short\\": true},
                {\\"title\\": \\"Branch\\", \\"value\\": \\"%branch%\\", \\"short\\": true},
                {\\"title\\": \\"Commit\\", \\"value\\": \\"%endrev%\\", \\"short\\": true},
                {\\"title\\": \\"Deployer\\", \\"value\\": \\"%deployer%\\", \\"short\\": true},
                {\\"title\\": \\"Status\\", \\"value\\": \\"%status%\\", \\"short\\": true},
                {\\"title\\": \\"Overview\\", \\"value\\": \\"%deployment_overview%\\", \\"short\\": false}
              ]
            }]
          }"
        
        ```
        
        **Expected Output:**
        
        ```
        Log entry added to deployments.log
        Slack notification sent successfully
        
        ```
        
        ### Zero-Downtime vs Universal Release Path
        
        **Description:** Demonstrating the two different contexts where `%release_path%` is used.
        
        **Zero-Downtime Context Example:**
        
        ```bash
        # Zero-downtime deployment - %release_path% used with other zero-downtime variables
        echo "Setting up zero-downtime deployment..."
        echo "Release path: %release_path%"
        echo "Shared path: %shared_path%"
        echo "Current path: %current_path%"
        
        # Link shared resources to new release
        ln -sf %shared_path%/.env %release_path%/.env
        ln -sf %shared_path%/storage %release_path%/storage
        
        # Install dependencies in new release
        cd %release_path%
        composer install --no-dev --optimize-autoloader
        
        # Build assets in release folder
        npm run build
        
        # Atomically switch to new release
        ln -sfn %release_path% %current_path%
        echo "Zero-downtime deployment complete"
        
        ```
        
        **Universal Context Example:**
        
        ```bash
        # Universal deployment - %release_path% used on any server type
        echo "Setting up standard deployment..."
        echo "Release path: %release_path%"
        echo "Base path: %path%"
        
        # Navigate to release directory (available on all servers)
        cd %release_path%
        
        # Install dependencies
        if [ -f "package.json" ]; then
          npm install --production
        fi
        
        if [ -f "composer.json" ]; then
          composer install --no-dev --optimize-autoloader
        fi
        
        # Run migrations
        php artisan migrate --force
        
        echo "Standard deployment complete"
        
        ```
        
        **Expected Output:**
        
        ```
        Zero-downtime context:
        Setting up zero-downtime deployment...
        Release path: /var/www/releases/20250130_164530
        Shared path: /var/www/shared
        Current path: /var/www/current
        Zero-downtime deployment complete
        
        Universal context:
        Setting up standard deployment...
        Release path: /var/www/html
        Base path: /var/www
        Standard deployment complete
        
        ```
        
        ### Advanced Variable Combinations
        
        **Description:** Combining multiple variables for complex deployment scenarios.
        
        **Example:**
        
        ```bash
        # Create backup with deployment context
        BACKUP_NAME="%project%_%environment%_%branch%_$(date +%Y%m%d_%H%M%S)"
        
        # Use appropriate backup location based on deployment type
        if [ -n "%shared_path%" ]; then
          # Zero-downtime deployment
          BACKUP_PATH="%shared_path%/backups/$BACKUP_NAME"
          mkdir -p "%shared_path%/backups"
        else
          # Standard deployment
          BACKUP_PATH="%release_path%/backups/$BACKUP_NAME"
          mkdir -p "%release_path%/backups"
        fi
        
        # Database backup with context
        mysqldump -u %username% -p%password% %project%_db > "$BACKUP_PATH.sql"
        
        # Create deployment manifest
        cat > "%release_path%/DEPLOYMENT_MANIFEST" << EOF
        {
          "deployment_id": %count%,
          "project": "%project%",
          "environment": "%environment%",
          "branch": "%branch%",
          "commit": {
            "hash": "%endrev%",
            "message": "%endrevmsg%",
            "range": "%commitrange%"
          },
          "deployer": "%deployer%",
          "timestamps": {
            "started": "%started_at%",
            "completed": "%completed_at%"
          },
          "servers": "%servers%",
          "overview": "%deployment_overview%",
          "paths": {
            "release": "%release_path%",
            "base": "%path%"
          },
          "urls": {
            "project": "%projecturl%",
            "deployment": "%deploymenturl%",
            "permalink": "%projectperma%"
          }
        }
        EOF
        
        # Add zero-downtime specific paths if available
        if [ -n "%shared_path%" ]; then
          cat >> "%release_path%/DEPLOYMENT_MANIFEST" << EOF
          "zero_downtime_paths": {
            "shared": "%shared_path%",
            "current": "%current_path%",
            "previous": "%previous_release_path%"
          }
        EOF
        fi
        
        # Server group handling
        if [ -n "%groupindex%" ]; then
          echo "Processing server group index: %groupindex%"
          # Add group-specific logic here
        fi
        
        ```
        
        **Expected Output:**
        
        ```
        Backup created: my-app_production_main_20250130_164530.sql
        Deployment manifest created with full context
        Processing server group index: 0
        
        ```
        
        ---
        
        *Last updated: 2025-01-30*
        
        ```markdown
        
        ```
        
    - **DeployHQ Variables: Complete Reference Guide**
        
        # DeployHQ Variables: Complete Reference Guide
        
        Based on the official DeployHQ documentation[^1](https://www.deployhq.com/support/projects/variables), there are **27 unique variables** available for use in Config files, SSH commands, and notifications. The variable `%release_path%` appears in **two different contexts**, making the total occurrence count 28.
        
        ## Variable Count Summary
        
        **Total unique variables: 27Total occurrences: 28** (due to `%release_path%` appearing twice)
        
        ![Complete DeployHQ Variables Reference Guide](https://ppl-ai-code-interpreter-files.s3.amazonaws.com/web/direct-files/6144b8d19d086625f1ac0c1666b770c6/b56dc845-c0d9-45d2-8fd2-aa751de4284a/cf5afb0a.png)
        
        Complete DeployHQ Variables Reference Guide
        
        ## Complete Variable Reference
        
        ### Project Variables (18)
        
        These variables refer to the project and deployment information[^1](https://www.deployhq.com/support/projects/variables):
        
        | Variable | Definition | Example Usage |
        | --- | --- | --- |
        | `%startrev%` | Start revision commit ref | `'Deployment from %startrev% to %endrev%'` in notifications |
        | `%endrev%` | End revision commit ref | `echo "Deploying to revision %endrev%"` in SSH commands |
        | `%startrevmsg%` | Start revision commit message (available on deployment finish or failure) | `'Started from: %startrevmsg%'` in notifications |
        | `%endrevmsg%` | End revision commit message (available on deployment finish or failure) | `'Deploying: %endrevmsg%'` to show what changes are being deployed |
        | `%tag%` | Tag related to end revision (if present) | `'Deploying version %tag%'` for release versioning |
        | `%branch%` | The branch of the deployment | `ENV["BRANCH"]="%branch%"` in config files[^1](https://www.deployhq.com/support/projects/variables) |
        | `%count%` | This number of deployments in the project | `'This is deployment #%count%'` for tracking frequency |
        | `%servers%` | Names of the servers in this deployment | `'Deploying to servers: %servers%'` in notifications |
        | `%deployer%` | User who started the deployment (if manually deployed) | `'Deployment initiated by %deployer%'` for attribution |
        | `%commitrange%` | The start and end commit, separated with a hyphen | `'Changes: %commitrange%'` in notifications |
        | `%project%` | The name of this project in DeployHQ | `PROJECT_NAME="%project%"` in config files |
        | `%projecturl%` | The address of this project in DeployHQ | `<a href="%projecturl%">View Project</a>` for dashboard links |
        | `%projectperma%` | The permalink of this project in DeployHQ | Permanent project reference in API calls |
        | `%deploymenturl%` | The address of this deployment in DeployHQ | `<a href="%deploymenturl%">View Deployment</a>` |
        | `%status%` | The current status of this deployment | `'Deployment status: %status%'` for progress tracking |
        | `%started_at%` | The ISO8601 timestamp representing the start time of the deployment | `'Started at %started_at%'` for timing logs[^2](https://www.ibm.com/docs/en/urbancode-release/6.2.4?topic=notifications-notification-template-variables) |
        | `%completed_at%` | The ISO8601 timestamp representing the completion time of the deployment | `'Completed at %completed_at%'` for duration calculation[^2](https://www.ibm.com/docs/en/urbancode-release/6.2.4?topic=notifications-notification-template-variables) |
        | `%deployment_overview%` | The parsed and stripped deployment overview (if provided) | `'Overview: %deployment_overview%'` in notifications |
        
        ### Server Variables (5)
        
        These variables refer to server or server group configuration[^1](https://www.deployhq.com/support/projects/variables):
        
        | Variable | Definition | Example Usage |
        | --- | --- | --- |
        | `%environment%` | Server environment (development, production etc.) | `ENV["RAILS_ENV"]="%environment%"`[^1](https://www.deployhq.com/support/projects/variables) or `npm run %environment%`[^3](https://www.deployhq.com/support/deployments/setting-up-zero-downtime-deployments) |
        | `%username%` | Username used to login to the server | `chown %username%:www-data` for file permissions |
        | `%password%` | Password used to login to the server | Automated authentication (not recommended for security) |
        | `%groupindex%` | Server group only. The order within its group that this server is deployed (zero-indexed) | `if [ %groupindex% -eq 0 ]; then initialize_db; fi` |
        | `%path%` | Base server path we're deploying to | `cd %path%`[^4](https://www.deployhq.com/support/api/deployments/view-config-file-deployment-preview-api) or `composer install -d %path%`[^5](https://documentation.pdq.com/PDQDeploy/19.3.254.0/notifications.htm) |
        
        ### Zero-Downtime Deployment Variables (4)
        
        These variables are available for servers configured with zero-downtime deployments[^1](https://www.deployhq.com/blog/zero-downtime-deployments-with-deployhq-a-step-by-step-guide):
        
        | Variable | Definition | Example Usage |
        | --- | --- | --- |
        | `%release_path%` | Path that the release is being deployed to (zero-downtime context) | `cd %release_path% && npm run build`[^7](https://www.deployhq.com/support/api/deployments/create-a-new-deployment) for building in release folder |
        | `%shared_path%` | Path containing shared files that will be symlinked to release | `ln -sf %shared_path%/uploads %release_path%/public/uploads` |
        | `%current_path%` | Symlink pointing to the currently active release | `if [ -L %current_path% ]; then backup_current; fi`[^7](https://www.deployhq.com/support/api/deployments/create-a-new-deployment) |
        | `%previous_release_path%` | Path to the previous release | `ln -sf %previous_release_path% %current_path%` for rollbacks |
        
        ### All Servers Variables (1)
        
        This variable can be used on all servers[^1](https://www.deployhq.com/support/projects/variables):
        
        | Variable | Definition | Example Usage |
        | --- | --- | --- |
        | `%release_path%` | Path that the release is being deployed to (universal context) | `cd %release_path% && php artisan migrate` for any server type[^8](https://www.deployhq.com/blog/customise-your-notifications) |
        
        ## Key Differences in %release_path% Usage
        
        The `%release_path%` variable appears in **two different contexts**:
        
        ### 1. Zero-Downtime Context
        
        - **Purpose**: Specifically for atomic/zero-downtime deployments[^6](https://www.deployhq.com/blog/zero-downtime-deployments-with-deployhq-a-step-by-step-guide)
        - **Behavior**: Points to a timestamped release directory (e.g., `releases/20190123120000`)
        - **Usage**: Build commands run in the release folder before switching the symlink
        - **Example**: `cd %release_path% && composer install --no-dev`[^7](https://www.deployhq.com/support/api/deployments/create-a-new-deployment)
        
        ### 2. All Servers Context
        
        - **Purpose**: Universal deployment path for any server type
        - **Behavior**: Points to the general deployment path regardless of deployment strategy
        - **Usage**: Standard deployment operations that work on any server configuration
        - **Example**: `cd %release_path% && php artisan migrate`[^8](https://www.deployhq.com/blog/customise-your-notifications)
        
        ## Usage Guidelines
        
        **Config Files**: Use variables like `%environment%` to create dynamic configurations[^1](https://www.youtube.com/watch?v=T6W_FC2Sop4)
        
        ```bash
        ENV['%environment%']
        DATABASE_URL=%environment%_database_url
        
        ```
        
        **SSH Commands**: Reference paths and deployment info[^4](https://www.deployhq.com/support/api/deployments/view-config-file-deployment-preview-api)
        
        ```bash
        cd %path%
        composer install --no-progress --optimize-autoloader
        
        ```
        
        **Notifications**: Include deployment details and links[^10](https://www.deployhq.com/support/api/deployments/create-a-new-config-file-deployment-api)
        
        ```
        Deployment of %project% to %environment% completed at %completed_at%
        View: %deploymenturl%
        
        ```
        
        **Build Pipelines**: Create conditional logic based on environment[^11](https://www.deployhq.com/blog/build-pipelines-in-deployhq-streamline-your-deployment-workflow)
        
        ```bash
        if [[ "%environment%" =~ ^(integration|testing|staging)$ ]]; then
            npm ci
        fi
        
        ```
        
        This comprehensive variable system provides flexible deployment automation, allowing you to create dynamic configurations that adapt to different environments and deployment scenarios while maintaining consistency across your deployment pipeline.
        
        <div style="text-align: center">⁂</div>
        
    
    ---
    
- **Environment Types**
    - **Environment Types - Defined in SSH Commands + DeployHQ Docs.**
        
        For SSH commands in DeployHQ, environments are defined using the `%environment%` variable and can contain any environment name you configure. The most common environment names are:
        
        **Standard Environments:**
        
        - `development` - For local/dev work
        - `testing` - For automated testing
        - `staging` - For pre-production testing
        - `production` - For live systems
        - `integration` - For integration testing
        - `qa` - For quality assurance
        
        **How are Used in SSH Commands:**
        
        1. **Conditional Logic:**
        
        ```bash
        if [[ "%environment%" == "production" ]]; then
            composer install --no-dev --optimize-autoloader
        else
            composer install
        fi
        
        ```
        
        1. **Environment-Specific Commands:**
        
        ```bash
        npm run %environment%
        export APP_ENV=%environment%
        cp .env.%environment% .env
        
        ```
        
        1. **Multi-Environment Logic:**
        
        ```bash
        if [[ "%environment%" =~ ^(integration|testing|staging)$ ]]; then
            npm ci
            php artisan migrate:fresh --seed
        else
            php artisan migrate --force
        fi
        
        ```
        
        1. **Environment-Based Paths:**
        
        ```bash
        mkdir -p /var/log/%environment%
        mysqldump database > /backups/%environment%/db_$(date +%Y%m%d).sql
        
        ```
        
        The `%environment%` variable is one of 27 available DeployHQ variables that can be substituted into SSH commands, config files, and notifications, allowing you to create flexible deployment scripts that automatically adapt based on the target environment.
        
    
    ---
    
- **SSH Commands Plan**
- Shared, and Excluded Files
    - Important Notes
        - **Images, logos, and static application assets should NOT be shared (i.e., added to shared folder in zero downtime deployment folder)** - they should be deployed with your code. The confusion comes from thinking everything in `public/` needs to be shared, but that's incorrect.
        - Summary of strategy
            - **Core Principle**: "Is this user/runtime data?" → Yes = Share it, No = Deploy as code
            - **Universal Shared List**: `.env`, `storage/`, `bootstrap/cache/`, `public/.well-known/`
            - **Pattern-Based Detection**: `public/*upload*`, `public/*avatar*`, `public/*media*`, `public/qr*`
            - **Safe Default**: When in doubt, deploy as application code (prevents data loss)
    - Definitions
        - **SHARED FOLDERS and Files (Zero-Downtime Assets):**
            
            **Definition**: Files and directories that must persist across deployments to prevent data loss and maintain application state.
            
            **Characteristics:**
            
            - User-generated content that would be lost if overwritten
            - Application runtime data (cache, sessions, logs)
            - Environment-specific configuration that shouldn't change
            - Must exist in the same location across all releases
            
            **Actions Required:**
            
            1. ✅ Add to shared folders (symlinked from `/shared/`)
            2. ✅ Add to Excluded Files or `.deployignore` (excluded from file sync)
            3. ✅ Add to `.gitignore` (excluded from version control)
        - **🚀 APPLICATION CODE (Deployable Assets)**
            
            **Definition**: Files that are part of your application's codebase and should be updated with each deployment.
            
            **Characteristics:**
            
            - Static assets that are part of the app even public (flags, logos, themes)
            - Compiled/built assets (CSS, JS, optimized images)
            - Application logic and configuration templates
            - Custom code and design changes
            
            **Actions Required:**
            
            1. ✅ Keep in version control (Git)
            2. ✅ Deploy with each release
            3. ❌ Do NOT add to shared folders
            4. ❌ Do NOT exclude from deployment
    - **Universal Exclusion-Based Strategy - The Simple Rule - LISTS** ⭐
        1. **STEP 1:** Identify what goes to SHARED folders (and therefore is excluded (or deployignore). 
        2. **STEP 2:** Everything else is APPLICATION CODE
            
            This is much cleaner than trying to predict every possible folder name
            
            ---
            
        
        **Easy 3-Step Strategy**
        
        1. **Step 1: Always add these to Shared  (Universal List)** (and therefore is excluded (or deployignore). 
            
            ```bash
            # ALWAYS SHARED - No matter what the app is:
            .env                    # Environment config
            storage/                # All Laravel storage (logs, cache, sessions, user files)
            bootstrap/cache/        # Laravel system cache
            
            # ALWAYS SHARED - User content (any naming variation):
            public/*upload*/        # Any folder with "upload" in name
            public/*avatar*/        # Any folder with "avatar" in name  
            public/*media*/         # Any folder with "media" in name
            public/qr*/             # QR codes or similar generated content
            public/.well-known/     # SSL certificates
            
            ```
            
        2. **Step 2: Everything Else = Application Code**
            - NO DECISIONS NEEDED - If it's not in Step 1, it's app code. `examples` can include:
                
                ```bash
                public/flags/           # App code (even if varies by app)
                public/img/             # App code (logos, icons, etc.)
                public/themes/          # App code (app themes)
                public/build/           # App code (compiled assets)
                public/.htaccess        # App code (web config)
                public/robots.txt       # App code (SEO config)
                vendor/                 # App code (dependencies)
                # ... literally everything else
                
                ```
                
        3. **Step 3: Verification Questions**
            - Ask yourself for ANY folder: **"If this gets overwritten during deployment, will users lose their data or will the app break functionality?"**
                - **Users lose data** → Add to Shared folder  (and therefore excluded (or added to deployignore).
                - **App breaks/missing features** → Deploy it as code
                - **Not sure** → Deploy as code (safer default)
    - **SHARED PATTERNS**: Common Patterns of Files and Folders should be in Shared Folders (and excluded)
        1. User Content Variations Pattern: Anything users create, upload etc → added to SHARED folders (and therefore is excluded (or deployignore). 
            - `public/upload*/`              # Covers: `public/upload`, `public/uploads`, `public/uploaded`
            - `public/user-upload*/`         # Covers: `public/user-uploads`, `public/user-upload`
            - `public/media*/`               # Covers: `public/media`, `public/medias`
            - `public/avatar*/`              # Covers: `public/avatar`, `public/avatars`, `public/clientAvatar`
            - `public/attachment*`/          # Covers: `public/attachments`, `public/attachment`
            - `public/document*/`            # Covers: `public/documents`, `public/document`
            - `public/file*/`                # Covers: `public/files`, `public/file`
            - `public/image*/`             # Covers: `public/images` when user-generated (not app assets)
        2. User Generated Content: 
            - public/qrcode*/              # Generated QR codes
            - public/barcode*/             # Generated barcodes
            - public/certificate*/         # Generated certificates
            - public/report*/              # Generated reports
            - public/temp*/                # Temporary files
        3. Runtime System Data
    - **Expert-Identified Edge Cases**
        
        Based on the Perplexity expert analysis:
        
        all below should be treated as code, which further validates our 3 steps strategy.
        
        - **CodeCanyon Patterns**:  `public/themes/`, `public/plugins/`, `public/assets/uploads/`
        - **Multi-tenant**: `public/tenant-{id}/`, `storage/app/tenants/`
        - **CMS-Style**: `public/gallery/`, `public/attachments/`
        - **Package-Specific**: `storage/app/livewire-tmp/`, `storage/app/public/`
        
        HOWEVER:  The pre-populated content logic is brilliant and solves the CodeCanyon demo data challenge. You're absolutely right that most edge cases are simply "`app code`" - therefore our 3-step strategy covers everything. 
        
        - **CodeCanyon themes/plugins** → App code (unless user-customizable)
        - **Multi-tenant patterns** → App code (structure) + Shared (user data)
        - **CMS galleries** → App code (system) + Shared (user uploads)
        
        **The rule remains**: "Is this user/runtime data?" → Yes = Share, No = App code
        
    - **Pre-Populated Content-And-FIRST-DEPLOYMENT Strategy (Critical Addition)**
        
        New Logic for guides steps and for AI Template:
        
        ### Step 1A: Check for Pre-Populated Content
        
        For each potential shared directory:
        
        1. **If directory has files** → Upload first during initial deployment → Then add to shared & exclude
        2. **If directory is empty** → Add to shared & exclude from first deployment
        3. **Reasoning**: Preserves demo data, admin examples, seed content that becomes user-modifiable
    - **Build Strategy Scenarios (Color-Coded)**
        
        **🏗️ Scenario A: Pre-Build (Recommended Production)**
        
        ```markdown
        ### Step X: Build Assets
        🟢 **Scenario A (Pre-Build)**: 
        - Build locally or in CI/CD
        - Include in Git: `vendor/`, `node_modules/`, `public/build/`
        - ✅ Faster deployments
        - ✅ Consistent builds
        
        🔴 **Scenario B (Build on Server)**: Skip this step
        🟡 **Scenario C (Hybrid)**: Build only CSS/JS, include vendor/
        ```
        
        **🏗️ Scenario B: Build on Server**
        
        ```markdown
        ### Step X: Install Dependencies  
        🔴 **Scenario B (Build on Server)**:
        - SSH Command: `composer install --no-dev --optimize-autoloader`
        - SSH Command: `npm ci && npm run production`
        - ⚠️ Longer deployment time
        - ⚠️ Requires Node.js on server
        
        🟢 **Scenario A (Pre-Build)**: Skip this step  
        🟡 **Scenario C (Hybrid)**: Only `composer install`
        
        ```
        
        **🏗️ Scenario C: Hybrid**
        
        ```markdown
        ### Step X: Mixed Build Strategy
        🟡 **Scenario C (Hybrid)**:
        - Pre-build: CSS/JS assets (`npm run production` in CI)
        - Server-build: Dependencies (`composer install` via SSH)
        - Include in Git: `public/build/`
        - Exclude from Git: `vendor/`, `node_modules/`
        - ⚖️ Balanced approach
        
        ```
        
    - **FINAL SYMLINKS SETUP LIST**
        - **🌐 Primary Web Entry Point (CRITICAL)**
            
            ```bash
            # Main web root symlink - THIS IS THE MOST IMPORTANT ONE
            # Points the web server to the current release's public directory
            ln -sfn %deploy_path%/current/public %home_path%/public_html
            
            # Alternative naming based on hosting provider:
            # ln -sfn %deploy_path%/current/public %home_path%/www
            # ln -sfn %deploy_path%/current/public %home_path%/htdocs
            
            ```
            
        - **📁 Core Laravel Symlinks (Standard)**
            
            ```bash
            # Laravel storage symlink (standard Laravel)
            ln -sfn %shared_path%/storage/app/public %release_path%/public/storage
            
            # Environment file
            ln -sfn %shared_path%/.env %release_path%/.env
            
            # Bootstrap cache (directory symlink)
            rm -rf %release_path%/bootstrap/cache
            ln -sfn %shared_path%/bootstrap/cache %release_path%/bootstrap/cache
            
            # Storage directory (full directory symlink)  
            rm -rf %release_path%/storage
            ln -sfn %shared_path%/storage %release_path%/storage
            
            # SSL/Domain verification
            ln -sfn %shared_path%/public/.well-known %release_path%/public/.well-known
            
            ```
            
             The `public_html` symlink is the **most critical** symlink for zero-downtime deployment and securty
            
        - **📤 User Content Symlinks (App-Specific, varies , should be decided based on manual inspection and AI template)**
            
            ```bash
            # Pattern-based (generated per app):
            ln -sfn %shared_path%/public/user-uploads %release_path%/public/user-uploads
            ln -sfn %shared_path%/public/qrcodes %release_path%/public/qrcodes
            ln -sfn %shared_path%/public/upload %release_path%/public/upload
            ln -sfn %shared_path%/public/uploads %release_path%/public/uploads
            ln -sfn %shared_path%/public/avatar %release_path%/public/avatar
            # ... (one for each detected user content directory)
            
            ```
            
    - **Directory Structure Visualization**
        
        ```markdown
        /home/username/
        ├── deploy/                           # Deployment directory
        │   ├── current -> releases/20240131-123456/  # Current release symlink
        │   ├── releases/                     # All releases
        │   │   ├── 20240131-123456/         # Latest release
        │   │   │   ├── app/
        │   │   │   ├── public/              # Laravel public directory
        │   │   │   │   ├── index.php
        │   │   │   │   ├── storage -> /shared/storage/app/public
        │   │   │   │   ├── user-uploads -> /shared/public/user-uploads
        │   │   │   │   └── .well-known -> /shared/public/.well-known
        │   │   │   ├── storage -> /shared/storage
        │   │   │   ├── bootstrap/cache -> /shared/bootstrap/cache
        │   │   │   └── .env -> /shared/.env
        │   │   └── 20240131-120000/         # Previous release
        │   └── shared/                       # Shared data
        │       ├── storage/
        │       ├── bootstrap/cache/
        │       ├── .env
        │       └── public/
        │           ├── user-uploads/
        │           ├── .well-known/
        │           └── qrcodes/
        └── public_html -> deploy/current/public/  # 🌟 WEB ROOT SYMLINK 🌟
        
        ```
        
    
- **Final Definitive Lists**
    - **📁 FINAL SHARED FOLDERS LIST - Add to Shared List**
        
        ---
        
        1. **Universal Laravel Shared (Always Apply):** 
            
            ```bash
            .env                           # Environment configuration
            storage/                       # All subdirectories (logs, cache, sessions, uploads)
            bootstrap/cache/              # Laravel bootstrap cache  
            public/.well-known/           # SSL certificates, domain verification
            
            ```
            
        2. **User Content Pattern Detection:**
            
            ```bash
            public/*upload*/              # Any directory with "upload" in name
            public/*avatar*/              # Profile images, user photos
            public/*media*/               # Media libraries, galleries  
            public/qr*/                   # Generated QR codes, dynamic content
            ## DOUBLE CHECK that we didnt mistakenly include a folder using * patterns. sanity checks.
            
            ```
            
        
    - **FINAL EXCLUSION LIST (or .deployignore)**
        - **Always Excluded (Don't Deploy):**
            
            ```bash
            # Development & Local Files
            .git/
            .gitignore
            .deployignore
            README.md
            /docs/
            /tests/
            .DS_Store
            .vscode/
            .idea/
            Admin-Local/
            .env.local
            .env.testing
            ```
            
        - Shared Directories (Symlinked, Not Synchronized)
            
            ```bash
            # Shared Directories (Symlinked, Not Synchronized)
            .env
            .env.*
            /storage/
            /bootstrap/cache/
            /public/.well-known/
            ```
            
        - User Content Patterns (Shared) (keep in mind above **re-Populated Content-And-FIRST-DEPLOYMENT.**
            
            ```bash
            # User Content Patterns (Shared)
            /public/*upload*/
            /public/*avatar*/
            /public/*media*/
            /public/qr*/
            ```
            
    - **AI Prompt Template**
        
        ```markdown
        Enhanced Analysis Section:
        ### Step 1A: Pre-Populated Content Detection
        For each potential shared directory found:
        
        **Check Directory Contents**:
        ```bash
        # Check if directory has existing files
        find public/uploads/ -type f | head -5
        find public/media/ -type f | head -5
        find public/themes/custom/ -type f | head -5
        
        markdown
        
        Apply Pre-Population Logic:
        
        Has Files → "Copy-then-Share" strategy
        First deployment: Upload existing content
        Subsequent: Add to shared folders + exclude from sync
        Use case: Demo data, admin examples, default themes
        Empty Directory → "Share-Immediately" strategy
        Add to shared folders from first deployment
        Use case: Pure user upload directories
        Generate Instructions:
        
        # Example SSH command for copy-then-share:
        if [ ! -d "%shared_path%/public/themes/custom" ]; then
            cp -r "%release_path%/public/themes/custom" "%shared_path%/public/"
        fi
        ln -sfn "%shared_path%/public/themes/custom" "%release_path%/public/themes/custom"
        
        bash
        
        ```
        
    - FINAL CORRECTED: Complete Symlinks Setup List with Hosting Provider Paths
        
        Perfect correction! The path structure varies significantly between hosting providers. Here's the complete corrected version with accurate examples.
        
        ## 🔗 **FINAL SYMLINKS SETUP LIST (HOSTING PROVIDER CORRECTED)**
        
        ### 🌐 **Primary Web Entry Point (CRITICAL)**
        
        ```bash
        # Main web root symlink - THIS IS THE MOST IMPORTANT ONE
        # Points the web server to the current release's public directory
        
        # For Hostinger: hostinger usually include public_html folder needs to be deleted first then public_html file created and symlinked.
        ln -sfn %deploy_path%/current/public %home_path%/domains/website.com/public_html
        
        # For cPanel:
        ln -sfn %deploy_path%/current/public %home_path%/website.com/public_html
        
        ```
        
        ### 📁 **Core Laravel Symlinks (Standard)**
        
        ```bash
        # Laravel storage symlink (standard Laravel)
        ln -sfn %shared_path%/storage/app/public %release_path%/public/storage
        
        # Environment file
        ln -sfn %shared_path%/.env %release_path%/.env
        
        # Bootstrap cache (directory symlink)
        rm -rf %release_path%/bootstrap/cache
        ln -sfn %shared_path%/bootstrap/cache %release_path%/bootstrap/cache
        
        # Storage directory (full directory symlink)
        rm -rf %release_path%/storage
        ln -sfn %shared_path%/storage %release_path%/storage
        
        # SSL/Domain verification
        ln -sfn %shared_path%/public/.well-known %release_path%/public/.well-known
        
        ```
        
        ### 📤 **User Content Symlinks (App-Specific)**
        
        ```bash
        # Pattern-based (generated per app, varies by app):
        ln -sfn %shared_path%/public/user-uploads %release_path%/public/user-uploads
        ln -sfn %shared_path%/public/qrcodes %release_path%/public/qrcodes
        ln -sfn %shared_path%/public/upload %release_path%/public/upload
        ln -sfn %shared_path%/public/uploads %release_path%/public/uploads
        ln -sfn %shared_path%/public/avatar %release_path%/public/avatar
        # ... (one for each detected user content directory)
        
        ```
        
        ## **Correct Path Variables Examples**
        
        ### ⚠️ **Path Variables - Hostinger Example**
        
        ```bash
        %home_path% = /home/username/
        %deploy_path% = /home/username/domains/website.com/deploy/
        %shared_path% = /home/username/domains/website.com/deploy/shared/
        %release_path% = /home/username/domains/website.com/deploy/releases/20240131-123456/
        %web_root% = /home/username/domains/website.com/public_html
        
        ```
        
        ### ⚠️ **Path Variables - cPanel Example**
        
        ```bash
        %home_path% = /home/username/
        %deploy_path% = /home/username/website.com/deploy/
        %shared_path% = /home/username/website.com/deploy/shared/
        %release_path% = /home/username/website.com/deploy/releases/20240131-123456/
        %web_root% = /home/username/website.com/public_html
        
        ```
        
        ## Corrected Directory Structure Visualization
        
        ### **Hostinger Structure:**
        
        ```
        /home/username/
        ├── domains/
        │   └── website.com/
        │       ├── deploy/                           # Deployment directory
        │       │   ├── current -> releases/20240131-123456/  # Current release symlink
        │       │   ├── releases/                     # All releases
        │       │   │   ├── 20240131-123456/         # Latest release
        │       │   │   │   ├── app/
        │       │   │   │   ├── public/              # Laravel public directory
        │       │   │   │   │   ├── index.php
        │       │   │   │   │   ├── storage -> /shared/storage/app/public
        │       │   │   │   │   ├── user-uploads -> /shared/public/user-uploads
        │       │   │   │   │   └── .well-known -> /shared/public/.well-known
        │       │   │   │   ├── storage -> /shared/storage
        │       │   │   │   ├── bootstrap/cache -> /shared/bootstrap/cache
        │       │   │   │   └── .env -> /shared/.env
        │       │   │   └── 20240131-120000/         # Previous release
        │       │   └── shared/                       # Shared data
        │       │       ├── storage/
        │       │       ├── bootstrap/cache/
        │       │       ├── .env
        │       │       └── public/
        │       │           ├── user-uploads/
        │       │           ├── .well-known/
        │       │           └── qrcodes/
        │       └── public_html -> deploy/current/public/  # 🌟 WEB ROOT SYMLINK 🌟
        └── [other domains...]
        
        ```
        
        ### **cPanel Structure:**
        
        ```
        /home/username/
        ├── website.com/
        │   ├── deploy/                           # Deployment directory
        │   │   ├── current -> releases/20240131-123456/  # Current release symlink
        │   │   ├── releases/                     # All releases
        │   │   │   ├── 20240131-123456/         # Latest release
        │   │   │   │   ├── app/
        │   │   │   │   ├── public/              # Laravel public directory
        │   │   │   │   │   ├── index.php
        │   │   │   │   │   ├── storage -> /shared/storage/app/public
        │   │   │   │   │   ├── user-uploads -> /shared/public/user-uploads
        │   │   │   │   │   └── .well-known -> /shared/public/.well-known
        │   │   │   │   ├── storage -> /shared/storage
        │   │   │   │   ├── bootstrap/cache -> /shared/bootstrap/cache
        │   │   │   │   └── .env -> /shared/.env
        │   │   │   └── 20240131-120000/         # Previous release
        │   │   └── shared/                       # Shared data
        │   │       ├── storage/
        │   │       ├── bootstrap/cache/
        │   │       ├── .env
        │   │       └── public/
        │   │           ├── user-uploads/
        │   │           ├── .well-known/
        │   │           └── qrcodes/
        │   └── public_html -> deploy/current/public/  # 🌟 WEB ROOT SYMLINK 🌟
        ├── anotherdomain.com/
        └── public_html -> website.com/public_html  # Main domain redirect (if applicable)
        
        ```
        
        ## Hosting Provider Detection Script
        
        ```bash
        #!/bin/bash
        # Auto-detect hosting provider structure
        
        DOMAIN="website.com"  # Replace with actual domain
        USERNAME=$(whoami)
        
        # Detect hosting provider structure
        if [ -d "/home/$USERNAME/domains/$DOMAIN" ]; then
            # Hostinger style
            DEPLOY_PATH="/home/$USERNAME/domains/$DOMAIN/deploy"
            WEB_ROOT="/home/$USERNAME/domains/$DOMAIN/public_html"
            echo "Detected: Hostinger structure"
        elif [ -d "/home/$USERNAME/$DOMAIN" ]; then
            # cPanel style
            DEPLOY_PATH="/home/$USERNAME/$DOMAIN/deploy"
            WEB_ROOT="/home/$USERNAME/$DOMAIN/public_html"
            echo "Detected: cPanel structure"
        else
            echo "Unknown hosting structure - manual configuration required"
        fi
        
        # Set other path variables
        SHARED_PATH="$DEPLOY_PATH/shared"
        CURRENT_PATH="$DEPLOY_PATH/current"
        
        echo "Deploy Path: $DEPLOY_PATH"
        echo "Shared Path: $SHARED_PATH"
        echo "Web Root: $WEB_ROOT"
        
        ```
        
        ## Universal SSH Command Template
        
        ```bash
        # Universal symlink command that adapts to hosting provider
        # This would be part of the DeployHQ SSH commands
        
        # Auto-detect structure and set paths
        DOMAIN="%{domain}"  # DeployHQ variable
        USERNAME=$(whoami)
        
        if [ -d "/home/$USERNAME/domains/$DOMAIN" ]; then
            # Hostinger
            WEB_ROOT="/home/$USERNAME/domains/$DOMAIN/public_html"
        elif [ -d "/home/$USERNAME/$DOMAIN" ]; then
            # cPanel
            WEB_ROOT="/home/$USERNAME/$DOMAIN/public_html"
        else
            WEB_ROOT="/home/$USERNAME/public_html"  # Fallback
        fi
        
        # Create the critical web root symlink
        ln -sfn %deploy_path%/current/public $WEB_ROOT
        echo "✅ Web root symlink created: $WEB_ROOT -> %deploy_path%/current/public"
        
        ```
        
        ## Key Differences Between Providers
        
        ### 🏢 **Hostinger Structure**
        
        - **Multi-domain**: `/home/username/domains/website.com/`
        - **Separation**: Each domain has its own directory
        - **Path Depth**: One extra level (`domains/`)
        
        ### 🏢 **cPanel Structure**
        
        - **Direct domain**: `/home/username/website.com/`
        - **Simpler**: Direct domain folders under home
        - **Traditional**: Standard shared hosting layout
        
        ### 🏢 **Universal Compatibility**
        
        Both structures work with the same deployment strategy - only the paths change. The symlink logic remains identical, making the deployment process universal while adapting to provider-specific directory structures.
        
        This correction ensures our deployment strategy works perfectly with both major hosting providers!
        
    
- HOw to setup Shared Folders
    - 
    - DeployHQ Shared Directory Setup for Laravel Zero Downtime Deployments
        
        # DeployHQ Shared Directory Setup for Laravel Zero Downtime Deployments
        
        DeployHQ automatically creates the `shared` directory structure during your first zero downtime deployment, but you need to manually create the specific folders and files your Laravel application requires. Here's how to set up your shared directories properly.
        
        ## How DeployHQ Shared Directories Work
        
        DeployHQ automatically creates three main directories during the first atomic deployment[1](https://www.deployhq.com/support/deployments/setting-up-zero-downtime-deployments):
        
        - **releases/** - Contains timestamped release directories
        - **shared/** - Contains files that persist across deployments
        - **current** - Symlink pointing to the active release
        
        During each deployment, DeployHQ automatically symlinks everything from the shared folder to the latest release[2](https://www.deployhq.com/support/faq/zero-downtime-shared-directory)[3](https://www.deployhq.com/blog/setting-up-zero-downtime-deployments). The structure matches exactly, creating symlinks like:
        
        `text/your-deployment-path/shared/storage -> /your-deployment-path/releases/20240131-123456/storage
        /your-deployment-path/shared/public/uploads -> /your-deployment-path/releases/20240131-123456/public/uploads`
        
        ## Creating Your Laravel Shared Directories
        
        ## Method 1: SSH Commands in DeployHQ (Recommended)
        
        Configure SSH commands in DeployHQ to run **before the first deployment** to create the necessary shared directories[4](https://www.deployhq.com/support/ssh-commands):
        
        **SSH Command Configuration:**
        
        1. Go to **SSH Commands** in your DeployHQ project
        2. Create a **"Before Deployment"** SSH command
        3. Set it to run on **"First deployment only"**
        4. Use this command:
        
        `bash*# Navigate to deployment path and create shared structure*
        cd %deploy_path%
        mkdir -p shared/.env
        mkdir -p shared/storage/app/public
        mkdir -p shared/storage/logs  
        mkdir -p shared/storage/framework/cache/data
        mkdir -p shared/storage/framework/sessions
        mkdir -p shared/storage/framework/testing
        mkdir -p shared/storage/framework/views
        mkdir -p shared/bootstrap/cache
        mkdir -p shared/public/.well-known
        
        *# Create directories for user content patterns*
        mkdir -p shared/public/uploads
        mkdir -p shared/public/avatars  
        mkdir -p shared/public/media
        mkdir -p shared/public/qr-codes
        
        *# Set proper permissions*
        chmod -R 775 shared/storage
        chmod -R 775 shared/bootstrap/cache
        chmod 644 shared/.env`
        
        ## Method 2: Manual SSH Creation
        
        Connect to your server via SSH and run these commands:
        
        `bash*# Navigate to your deployment directory*
        cd /home/u164914061/domains/staging.zajaly.com/deploy
        
        *# Create the shared directory structure*
        mkdir -p shared/storage/{app/public,logs,framework/{cache/data,sessions,testing,views}}
        mkdir -p shared/bootstrap/cache
        mkdir -p shared/public/.well-known
        mkdir -p shared/public/{uploads,avatars,media}
        mkdir -p shared/public/qr-codes
        
        *# Set permissions*
        chmod -R 775 shared/storage
        chmod -R 775 shared/bootstrap/cache`
        
        ## Configuring Shared Directories in DeployHQ
        
        For your specific shared folders list, configure these in DeployHQ:
        
        ## Universal Laravel Shared (Always Apply)
        
        | Directory | Configuration in DeployHQ |
        | --- | --- |
        | `.env` | Create as Config File: `shared/.env` |
        | `storage/` | Create folder: `shared/storage/` |
        | `bootstrap/cache/` | Create folder: `shared/bootstrap/cache/` |
        | `public/.well-known/` | Create folder: `shared/public/.well-known/` |
        
        ## User Content Pattern Detection
        
        | Pattern | Shared Directory | Notes |
        | --- | --- | --- |
        | `public/*upload*/` | `shared/public/uploads/` | Create specific folder for uploads |
        | `public/*avatar*/` | `shared/public/avatars/` | Profile images directory |
        | `public/*media*/` | `shared/public/media/` | Media libraries, galleries |
        | `public/qr*/` | `shared/public/qr-codes/` | Generated QR codes |
        
        **Important:** DeployHQ doesn't support wildcard patterns in shared directory creation[5](https://www.deployhq.com/support/common-deployment-errors/files-uploading-unexpectedly). You must create specific directories rather than using patterns like `public/*upload*/`.
        
        ## Repository Exclusions
        
        To prevent conflicts, exclude these directories from your repository using DeployHQ's **Excluded Files** feature[6](https://www.deployhq.com/support/excluded-files):
        
        `textstorage/**
        bootstrap/cache/**
        public/.well-known/**
        public/uploads/**
        public/avatars/**
        public/media/**
        public/qr-codes/**`
        
        ## Critical Considerations
        
        ## Avoid Conflicts
        
        DeployHQ checks each directory in shared to ensure it doesn't exist in the repository[7](https://www.deployhq.com/support/zero-downtime-deployment-issues/shared-directories-symlink-issues). If duplicates exist, it will traverse subdirectories and symlink individual files, slowing deployment. **Always exclude shared directories from your repository**.
        
        ## Wildcard Limitations
        
        DeployHQ's shared directory system doesn't support wildcard patterns during creation[5](https://www.deployhq.com/support/common-deployment-errors/files-uploading-unexpectedly). You must:
        
        - Create specific directories (e.g., `shared/public/uploads/`)
        - Use SSH commands to create multiple similar directories
        - Plan your directory structure in advance
        
        ## First Deployment Setup
        
        Run your SSH command to create directories **before the first deployment**[1](https://www.deployhq.com/support/deployments/setting-up-zero-downtime-deployments). After the first deployment, the shared directories will be automatically symlinked to each new release.
        
        ## Permissions Management
        
        Set proper permissions on shared directories, especially for Laravel's storage requirements:
        
        `bash*# Set storage permissions*
        chmod -R 775 shared/storage
        chmod -R 775 shared/bootstrap/cache
        
        *# Ensure web server can write*
        chown -R www-data:www-data shared/storage
        chown -R www-data:www-data shared/bootstrap/cache`
        
        This approach ensures your Laravel application has all necessary shared directories properly configured for zero downtime deployments while maintaining persistent data across releases.
        
        Add to follow-up
        
        1. https://www.deployhq.com/support/deployments/setting-up-zero-downtime-deployments
        2. https://www.deployhq.com/support/faq/zero-downtime-shared-directory
        3. https://www.deployhq.com/blog/setting-up-zero-downtime-deployments
        4. https://www.deployhq.com/support/ssh-commands
        5. https://www.deployhq.com/support/common-deployment-errors/files-uploading-unexpectedly
        6. https://www.deployhq.com/support/excluded-files
        7. https://www.deployhq.com/support/zero-downtime-deployment-issues/shared-directories-symlink-issues
        8. https://dev.to/doozieakshay/sop-deploying-laravel-applications-to-shared-hosting-via-git-and-cpanel-2oka
        9. https://www.deployhq.com/blog/stop-certain-files-from-being-uploaded-during-a-deployment
        10. https://github.com/deployphp/deployer/issues/572
        11. https://www.youtube.com/watch?v=oMtPkNMKpV8
        12. https://www.sitepoint.com/using-deployhq-automate-deployments/
        13. https://www.deployhq.com/blog/excluding-files-with-deployignore
        14. https://stackoverflow.com/questions/72534485/how-can-i-exclude-a-file-on-azure-devops-in-the-deployment
        15. https://www.deployhq.com/guides/laravel
        16. https://forum.getkirby.com/t/continuous-deployment-with-kirby-shared-folders-like-accounts-content-and-media/17823
        17. https://www.deployhq.com/support/api/excluded-files/create-excluded-file
        18. https://stackoverflow.com/questions/46890085/deploying-laravel-on-a-shared-hosting-sub-directory
        19. https://www.deployhq.com/guides/textpattern
        20. https://www.deployhq.com/support/api/templates/managing-template-elements/excluded-files
        21. https://cosme.dev/post/zero-down-deployment-for-laravel-apps
        22. https://www.deployhq.com/support/faq
        23. https://www.deployhq.com/support/faq/upload-ssh-key-authorized_keys
        24. https://stackoverflow.com/questions/78348381/laravel-symlink-symbolic-link-to-storage-not-working-in-production-shared-host
        25. https://www.educative.io/answers/how-to-fix-storage-link-not-working-issue-in-laravel
        26. https://intellij-support.jetbrains.com/hc/en-us/community/posts/207028595-Deployment-exclude-wildcards-not-working
        27. https://www.deployhq.com/support/faq/how-to-run-ssh-commands-without-uploading
        28. https://github.com/laravel/ideas/issues/899
        29. https://www.reddit.com/r/laravel/comments/vm0nw8/first_deployment_but_the_image_storage_link/
        30. https://www.reddit.com/r/pdq/comments/179d7un/pdq_deploy_using_a_wildcard_within_the_install/
        31. https://www.deployhq.com/blog/5-ways-to-create-ssh-keys-from-the-command-line-for-deployhq
        32. https://laravel.com/docs/12.x/filesystem
        33. https://talk.plesk.com/threads/rsync-exclude-patterns-required-to-use-wildcards-with-folder-names.368746/
        34. https://www.youtube.com/watch?v=svyN8-PjGHc
        35. https://stackoverflow.com/questions/25929225/how-to-deploy-to-github-with-file-pattern-on-travis
        36. https://www.deployhq.com/blog/setting-up-git-based-deployment-on-a-virtual-private-server-vps
        37. https://laracasts.com/discuss/channels/forge/storage-symbolic-link-laravel-forge-help
- **PHP & Laravel Commands**
    - **Generate App Key - fill .env with App Key**
        
        ```bash
        # cd app root - where .env exists
        # cd app-root
        php artisan key:generate
        ```
        
    - **Template**
        
        ```bash
        #
        
        ```
        
- Deployment Options & Stratgies
    - Variations and Strategies
        
        # Laravel Deployment Variations and Strategies Guide
        
        **Comprehensive Strategy Comparison and Implementation Guidelines**
        
        ---
        
        ## **Metadata**
        
        - **Version:** v1.0-Final
        - **Date:** August 1, 2025
        - **Last Updated:** 10:10 AM EST
        - **Purpose:** Complete reference for all 4 deployment strategies with step-by-step variations
        - **Dependencies:** Phase 1, Phase 2, Phase 3 guides
        
        ---
        
        ## **Table of Contents**
        
        1. [Strategy Overview](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        2. [🟢-A Standard Pre-Built Strategy](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        3. [🟢-B Full Pre-Built Strategy](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        4. [🔴 Build-on-Server Strategy](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        5. [🟡 Hybrid Strategy](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        6. [Strategy Comparison Matrix](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        7. [Migration Between Strategies](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        8. [Configuration Variations](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        9. [Troubleshooting by Strategy](https://www.notion.so/Install-New-CodeCanyon-App-using-Github-DeployHQ-FINAL-23f791295af5803bad0cef132b86a0c7?pvs=21)
        
        ---
        
        ## **1. Strategy Overview**
        
        ### **1.1 The Four Deployment Strategies**
        
        | Strategy | 🟢-A Standard Pre-Built | 🟢-B Full Pre-Built | 🔴 Build-on-Server | 🟡 Hybrid |  |
        | --- | --- | --- | --- | --- | --- |
        | **Build Location** | Local/CI | Local/CI | Production Server | Mixed |  |
        | **node_modules** | ❌ Excluded | ✅ Included | Server Install | Selective |  |
        | **Built Assets** | ✅ Included | ✅ Included | ❌ Build on Deploy | Mixed |  |
        | **Git Repository Size** | Medium (~50MB) | Large (200-500MB) | Small (~20MB) | Medium (~75MB) |  |
        | **Deployment Speed** | ⚡ Fast | ⚡⚡ Fastest | 🐌 Slow | 🚀 Balanced |  |
        | **Server Resources** | Low | None | High | Medium |  |
        | **Reliability** | High | Highest | Medium | High |  |
        | **Complexity** | Low | Low | Medium | High |  |
        |  | `Best for Production to avoid issues of server builds` | `[use this if we want to push all from local (CodeCanyon Apps]` |  |  |  |
        
        ### **1.2 Strategy Selection Decision Tree**
        
        ```mermaid
        flowchart TD
            A[Project Assessment] --> B{Team Size & Resources}
            B -->|Solo/Small Team| C{Internet Reliability}
            B -->|Large Team/Enterprise| D[🟢-B Full Pre-Built]
        
            C -->|Reliable| E{Server Resources}
            C -->|Unreliable| F[🟢-A Standard Pre-Built]
        
            E -->|Limited| G[🔴 Build-on-Server]
            E -->|Adequate| H{Asset Complexity}
        
            H -->|Simple| I[🟢-A Standard Pre-Built]
            H -->|Complex| J[🟡 Hybrid]
        
            D --> K[Enterprise Setup]
            F --> L[Standard Setup]
            G --> M[Simple Setup]
            I --> N[Optimized Setup]
            J --> O[Advanced Setup]
        
        ```
        
        ---
        
        ## **2. 🟢-A Standard Pre-Built Strategy**
        
        ### **2.1 Strategy Characteristics**
        
        **Best For:**
        
        - Small to medium teams
        - Consistent build environments
        - Fast deployments with reasonable Git size
        - Standard Laravel applications
        
        **Key Features:**
        
        - Assets built locally or in CI
        - node_modules excluded from deployment
        - Built assets committed to Git
        - Server-side Node.js not required for deployment
        
        ### **2.2 Local Setup Steps**
        
        ### **Phase 1 Configuration**
        
        ```bash
        # 1. Build assets locally
        npm ci
        npm run production
        
        # 2. Verify built assets
        ls -la public/build/ public/css/compiled/
        
        # 3. Git configuration (.gitignore)
        # /node_modules        # EXCLUDED
        # /public/build/       # COMMENTED OUT - Include in Git
        # /public/css/         # COMMENTED OUT - Include in Git
        
        ```
        
        ### **Phase 2 DeployHQ Configuration**
        
        ```yaml
        # Excluded Files (DeployHQ)
        /node_modules/          # Build tools not needed
        package-lock.json       # Lock file not needed
        webpack.mix.js          # Build config not needed
        vite.config.js          # Build config not needed
        /resources/sass/        # Source files not needed
        /resources/js/          # Source files not needed (built assets included)
        
        ```
        
        ### **SSH Commands Variation**
        
        - **B03**: Create shared directories (standard patterns)
        - **C06**: Skip asset building (assets pre-built)
        - **C04**: Verify built assets exist
        
        ### **2.3 Build Process**
        
        ```bash
        #!/bin/bash
        # Standard Pre-Built Build Process
        
        echo "🟢-A Standard Pre-Built: Building assets..."
        
        # Clean previous builds
        rm -rf public/build/ public/css/compiled/ public/js/compiled/
        
        # Install dependencies
        npm ci
        
        # Build for production
        npm run production
        
        # Verify build
        if [ -d "public/build" ] || [ -f "public/mix-manifest.json" ]; then
            echo "✅ Assets built successfully"
            # Commit to Git
            git add public/build/ public/css/ public/js/ public/mix-manifest.json
            git commit -m "Update pre-built assets"
        else
            echo "❌ Build failed"
            exit 1
        fi
        
        ```
        
        ### **2.4 Environment Variables**
        
        ```bash
        # .env additions for Standard Pre-Built
        BUILD_STRATEGY=pre-built
        NODE_ENV=production
        DEPLOY_ASSETS=false        # Assets already built
        BUILD_ON_DEPLOY=false      # No building during deployment
        
        ```
        
        ---
        
        ## **3. 🟢-B Full Pre-Built Strategy** `[use this if we want to push all from local (CodeCanyon Apps]`
        
        ### **3.1 Strategy Characteristics**
        
        **Best For:**
        
        - Enterprise environments
        - Air-gapped deployments
        - Ultra-fast deployments
        - Complex dependency management
        - Unreliable npm registries
        
        **Key Features:**
        
        - Everything included: built assets + node_modules
        - Zero server-side build dependencies
        - Fastest possible deployment
        - Self-contained deployment packages
        
        ### **3.2 Local Setup Steps**
        
        ### **Phase 1 Configuration**
        
        ```bash
        # 1. Build everything locally
        npm ci
        npm run production
        
        # 2. Keep node_modules for deployment
        echo "✅ node_modules will be included in deployment"
        
        # 3. Git configuration (.gitignore)
        # /node_modules        # COMMENTED OUT - Include in Git
        # /public/build/       # COMMENTED OUT - Include in Git
        
        # 4. Use Git LFS for large files (optional)
        git lfs track "node_modules/**/*.tar.gz"
        git lfs track "*.zip"
        
        ```
        
        ### **Phase 2 DeployHQ Configuration**
        
        ```yaml
        # Excluded Files (DeployHQ) - Minimal exclusions
        .git/                   # Version control
        .gitignore              # Git config
        README.md               # Documentation
        /docs/                  # Documentation
        /tests/                 # Test files
        Admin-Local/            # Local development only
        
        # Source files not needed (assets pre-built)
        /resources/sass/
        /resources/js/components/
        webpack.mix.js          # Build config not needed
        vite.config.js          # Build config not needed
        
        # KEEP THESE (Unlike standard pre-built):
        # /node_modules/        # INCLUDED - Full dependency tree
        # /public/build/        # INCLUDED - Pre-built assets
        # package.json          # INCLUDED - For server scripts
        
        ```
        
        ### **SSH Commands Variation**
        
        - **A01**: Skip Node.js checks (not needed)
        - **B03**: Create shared directories (standard patterns)
        - **C06**: Skip asset building entirely
        - **C04**: Verify self-contained deployment
        
        ### **3.3 Build Process**
        
        ```bash
        #!/bin/bash
        # Full Pre-Built Build Process
        
        echo "🟢-B Full Pre-Built: Complete build process..."
        
        # 1. Clean previous builds
        rm -rf public/build/
        rm -rf node_modules/
        
        # 2. Install all dependencies
        npm ci
        
        # 3. Build assets
        npm run production
        
        # 4. Remove dev dependencies, keep production
        npm prune --production
        
        # 5. Verify completeness
        echo "📊 Package Analysis:"
        echo "node_modules: $(du -sh node_modules/ | cut -f1)"
        echo "built assets: $(du -sh public/build/ | cut -f1)"
        echo "total size: $(du -sh . --exclude='.git' | cut -f1)"
        
        # 6. Commit everything
        git add .
        git commit -m "Full pre-built deployment package"
        
        echo "✅ Full pre-built package ready"
        
        ```
        
        ### **3.4 Environment Variables**
        
        ```bash
        # .env additions for Full Pre-Built
        BUILD_STRATEGY=full-prebuilt
        NODE_ENV=production
        DEPLOY_ASSETS=false        # Assets already built
        BUILD_ON_DEPLOY=false      # No building during deployment
        INCLUDE_NODE_MODULES=true  # node_modules included
        SERVER_NODE_REQUIRED=false # No Node.js needed on server
        
        ```
        
        ---
        
        ## **4. 🔴 Build-on-Server Strategy**
        
        ### **4.1 Strategy Characteristics**
        
        **Best For:**
        
        - Dynamic build requirements
        - Environment-specific builds
        - Smaller Git repositories
        - Teams with reliable server resources
        
        **Key Features:**
        
        - Source files deployed to server
        - Assets built during deployment
        - Fresh builds every deployment
        - Server requires Node.js and build tools
        
        ### **4.2 Local Setup Steps**
        
        ### **Phase 1 Configuration**
        
        ```bash
        # 1. Ensure NO built assets in Git
        rm -rf public/build/ public/css/compiled/ public/js/compiled/
        
        # 2. Verify source files exist
        ls -la resources/js/ resources/css/ resources/sass/
        
        # 3. Git configuration (.gitignore)
        /node_modules/          # EXCLUDED - Install on server
        /public/build/          # EXCLUDED - Build on server
        /public/css/compiled/   # EXCLUDED - Build on server
        /public/js/compiled/    # EXCLUDED - Build on server
        
        ```
        
        ### **Phase 2 DeployHQ Configuration**
        
        ```yaml
        # Excluded Files (DeployHQ) - Keep source files
        .git/                   # Version control
        .gitignore              # Git config
        README.md               # Documentation
        /docs/                  # Documentation
        /tests/                 # Test files
        Admin-Local/            # Local development only
        
        # Built assets excluded (will be built on server)
        /public/build/          # Will be built
        /public/css/compiled/   # Will be built
        /public/js/compiled/    # Will be built
        
        # KEEP THESE for server building:
        # /node_modules/        # Will be installed on server
        # /resources/           # Source files needed
        # package.json          # Build configuration needed
        # webpack.mix.js        # Build configuration needed
        # vite.config.js        # Build configuration needed
        
        ```
        
        ### **SSH Commands Variation**
        
        - **A01**: Verify Node.js, npm availability
        - **B03**: Create shared directories + install dependencies
        - **C06**: **REQUIRED** - Build assets on server
        - **C04**: Verify built assets created
        
        ### **4.3 Server Build Process**
        
        ```bash
        #!/bin/bash
        # Build-on-Server Process (C06 Command)
        
        echo "🔴 Build-on-Server: Server-side build process..."
        
        # 1. Navigate to current release
        cd "%current_path%" || exit 1
        
        # 2. Install Node dependencies
        echo "📦 Installing Node dependencies..."
        npm ci --silent --production=false
        
        # 3. Build assets
        echo "🔨 Building assets on server..."
        if npm run production --silent; then
            echo "✅ Assets built successfully"
        else
            echo "❌ Asset build failed"
            exit 1
        fi
        
        # 4. Clean up dev dependencies
        echo "🧹 Removing dev dependencies..."
        npm prune --production --silent
        
        # 5. Verify build
        if [ -d "public/build" ] || [ -f "public/mix-manifest.json" ]; then
            echo "✅ Server build completed"
        else
            echo "❌ Server build verification failed"
            exit 1
        fi
        
        ```
        
        ### **4.4 Environment Variables**
        
        ```bash
        # .env additions for Build-on-Server
        BUILD_STRATEGY=build-on-server
        NODE_ENV=production
        DEPLOY_ASSETS=true         # Assets built during deployment
        BUILD_ON_DEPLOY=true       # Building during deployment
        NPM_CONFIG_PRODUCTION=false # Allow dev dependencies for building
        
        ```
        
        ---
        
        ## **5. 🟡 Hybrid Strategy**
        
        ### **5.1 Strategy Characteristics**
        
        **Best For:**
        
        - Complex applications with mixed asset requirements
        - Large projects with core + dynamic components
        - Optimized performance requirements
        - Advanced teams with specific needs
        
        **Key Features:**
        
        - Core assets built locally (framework, vendor)
        - Dynamic assets built on server (themes, user-specific)
        - Selective dependency management
        - Optimized for specific use cases
        
        ### **5.2 Local Setup Steps**
        
        ### **Phase 1 Configuration**
        
        ```bash
        # 1. Build core assets locally
        npm run build:core      # Custom script for core assets
        
        # 2. Verify selective build
        ls -la public/css/compiled/core/
        ls -la public/js/compiled/app.js
        
        # 3. Git configuration (.gitignore)
        # Core assets included, dynamic excluded
        /public/build/chunks/           # Dynamic chunks excluded
        /public/css/compiled/dynamic/   # Dynamic CSS excluded
        /public/js/compiled/dynamic/    # Dynamic JS excluded
        # /public/css/compiled/core/    # COMMENTED OUT - Include core CSS
        # /public/js/compiled/app.js    # COMMENTED OUT - Include main app JS
        
        ```
        
        ### **Phase 2 DeployHQ Configuration**
        
        ```yaml
        # Excluded Files (DeployHQ) - Selective approach
        .git/
        .gitignore
        README.md
        /docs/
        /tests/
        Admin-Local/
        
        # Pre-built core assets (included)
        !/public/build/core/
        !/public/build/vendor/
        !/public/css/compiled/core/
        !/public/js/compiled/app.js
        
        # Dynamic assets (excluded - built on server)
        /public/build/dynamic/
        /public/build/themes/
        /public/css/compiled/dynamic/
        /public/js/compiled/chunks/
        
        # Selective node_modules (complex configuration)
        /node_modules/bootstrap/      # Pre-built locally
        /node_modules/vue/           # Pre-built locally
        !/node_modules/admin-lte/    # Keep for server build
        !/node_modules/@tailwind/    # Keep for server build
        
        # KEEP build tools for server-side dynamic builds
        # webpack.mix.js             # COMMENTED OUT - Keep for server builds
        # vite.config.js             # COMMENTED OUT - Keep for server builds
        
        ```
        
        ### **SSH Commands Variation**
        
        - **A01**: Verify Node.js for dynamic builds
        - **B03**: Create shared directories + selective npm install
        - **C06**: Build dynamic assets only
        - **C04**: Verify both pre-built and server-built assets
        
        ### **5.3 Hybrid Build Configuration**
        
        ### **Local Build (Core Assets)**
        
        ```jsx
        // vite.core.config.js
        import { defineConfig } from 'vite';
        import laravel from 'laravel-vite-plugin';
        
        export default defineConfig({
            plugins: [
                laravel({
                    input: ['resources/css/core.css', 'resources/js/app.js'],
                    refresh: true,
                }),
            ],
            build: {
                outDir: 'public/css/compiled/core',
                manifest: 'core-manifest.json',
                rollupOptions: {
                    output: {
                        entryFileNames: 'app.js',
                        chunkFileNames: 'core-[hash].js',
                    },
                },
            },
        });
        
        ```
        
        ### **Server Build (Dynamic Assets)**
        
        ```jsx
        // vite.dynamic.config.js
        import { defineConfig } from 'vite';
        import laravel from 'laravel-vite-plugin';
        
        export default defineConfig({
            plugins: [
                laravel({
                    input: ['resources/css/themes.css', 'resources/js/dynamic.js'],
                    refresh: true,
                }),
            ],
            build: {
                outDir: 'public/build/dynamic',
                manifest: 'dynamic-manifest.json',
                rollupOptions: {
                    external: ['./core-manifest.json'], // Reference core assets
                },
            },
        });
        
        ```
        
        ### **5.4 Package.json Scripts**
        
        ```json
        {
          "scripts": {
            "dev": "vite",
            "build": "npm run build:core && npm run build:dynamic",
            "build:core": "vite build --config vite.core.config.js",
            "build:dynamic": "vite build --config vite.dynamic.config.js",
            "build:themes": "vite build --config vite.themes.config.js",
            "deploy:hybrid": "npm run build:core"
          },
          "devDependencies": {
            "vite": "^4.0.0",
            "laravel-vite-plugin": "^0.7.0"
          },
          "dependencies": {
            "admin-lte": "^3.0.0",
            "@tailwindcss/forms": "^0.5.0"
          }
        }
        
        ```
        
        ### **5.5 Environment Variables**
        
        ```bash
        # .env additions for Hybrid Strategy
        BUILD_STRATEGY=hybrid
        NODE_ENV=production
        DEPLOY_ASSETS=partial      # Some assets built during deployment
        BUILD_ON_DEPLOY=true       # Dynamic building during deployment
        BUILD_CORE_LOCAL=true      # Core assets built locally
        BUILD_DYNAMIC_SERVER=true  # Dynamic assets built on server
        BUILD_THEMES=true          # Theme building enabled
        
        ```
        
        ---
        
        ## **6. Strategy Comparison Matrix**
        
        ### **6.1 Performance Metrics**
        
        | Metric | 🟢-A Standard | 🟢-B Full | 🔴 Build-Server | 🟡 Hybrid |
        | --- | --- | --- | --- | --- |
        | **Deployment Time** | 2-5 min | 1-3 min | 10-20 min | 5-10 min |
        | **First Deploy** | 5-10 min | 3-5 min | 15-30 min | 8-15 min |
        | **Git Clone Time** | 30-60s | 2-5 min | 10-20s | 45-90s |
        | **Upload Size** | 50-100MB | 200-500MB | 20-50MB | 75-150MB |
        | **Server CPU Usage** | Low | None | High | Medium |
        | **Memory Usage** | Low | None | High | Medium |
        | **Disk Space** | Medium | High | Low | Medium |
        
        ### **6.2 Reliability & Risk Assessment**
        
        | Factor | 🟢-A Standard | 🟢-B Full | 🔴 Build-Server | 🟡 Hybrid |
        | --- | --- | --- | --- | --- |
        | **Build Consistency** | ✅ High | ✅ Highest | ⚠️ Variable | ✅ High |
        | **Dependency Risk** | 🔸 Medium | ✅ None | ❌ High | 🔸 Medium |
        | **Network Dependency** | ✅ Low | ✅ None | ❌ High | 🔸 Medium |
        | **Server Failure Risk** | ✅ Low | ✅ Lowest | ❌ High | 🔸 Medium |
        | **Rollback Speed** | ✅ Fast | ✅ Fastest | 🔸 Medium | ✅ Fast |
        
        ### **6.3 Resource Requirements**
        
        | Resource | 🟢-A Standard | 🟢-B Full | 🔴 Build-Server | 🟡 Hybrid |
        | --- | --- | --- | --- | --- |
        | **Local Node.js** | ✅ Required | ✅ Required | 🔸 Optional | ✅ Required |
        | **Server Node.js** | ❌ Not Needed | ❌ Not Needed | ✅ Required | ✅ Required |
        | **CI/CD Pipeline** | 🔸 Recommended | 🔸 Recommended | 🔸 Optional | ✅ Required |
        | **Git LFS** | ❌ Not Needed | 🔸 Recommended | ❌ Not Needed | ❌ Not Needed |
        | **Bandwidth** | 🔸 Medium | ❌ High | ✅ Low | 🔸 Medium |
        
        ---
        
        ## **7. Migration Between Strategies**
        
        ### **7.1 Standard Pre-Built → Full Pre-Built**
        
        ```bash
        #!/bin/bash
        echo "🔄 Migrating: Standard Pre-Built → Full Pre-Built"
        
        # 1. Ensure assets are built
        npm run production
        
        # 2. Update .gitignore to include node_modules
        sed -i '/^\\/node_modules/d' .gitignore
        echo "# Full Pre-Built: node_modules included" >> .gitignore
        
        # 3. Add node_modules to Git
        git add node_modules/
        git add .gitignore
        
        # 4. Update environment variables
        sed -i 's/BUILD_STRATEGY=pre-built/BUILD_STRATEGY=full-prebuilt/' .env
        
        # 5. Commit changes
        git commit -m "Migrate to Full Pre-Built strategy"
        
        echo "✅ Migration completed"
        
        ```
        
        ### **7.2 Build-on-Server → Standard Pre-Built**
        
        ```bash
        #!/bin/bash
        echo "🔄 Migrating: Build-on-Server → Standard Pre-Built"
        
        # 1. Build assets locally
        npm ci
        npm run production
        
        # 2. Add built assets to Git
        git add public/build/ public/css/compiled/ public/js/compiled/
        git add public/mix-manifest.json
        
        # 3. Update .gitignore to exclude node_modules
        echo "/node_modules/" >> .gitignore
        echo "/resources/sass/" >> .gitignore
        echo "/resources/js/components/" >> .gitignore
        
        # 4. Update environment variables
        sed -i 's/BUILD_STRATEGY=build-on-server/BUILD_STRATEGY=pre-built/' .env
        
        # 5. Commit changes
        git commit -m "Migrate to Standard Pre-Built strategy"
        
        echo "✅ Migration completed"
        
        ```
        
        ### **7.3 Any Strategy → Hybrid**
        
        ```bash
        #!/bin/bash
        echo "🔄 Migrating to Hybrid Strategy"
        
        # 1. Create hybrid build configurations
        cat > vite.core.config.js << 'EOF'
        import { defineConfig } from 'vite';
        import laravel from 'laravel-vite-plugin';
        
        export default defineConfig({
            plugins: [laravel({
                input: ['resources/css/core.css', 'resources/js/app.js'],
                refresh: true,
            })],
            build: {
                outDir: 'public/css/compiled/core',
                manifest: 'core-manifest.json',
            },
        });
        EOF
        
        cat > vite.dynamic.config.js << 'EOF'
        import { defineConfig } from 'vite';
        import laravel from 'laravel-vite-plugin';
        
        export default defineConfig({
            plugins: [laravel({
                input: ['resources/css/themes.css', 'resources/js/dynamic.js'],
                refresh: true,
            })],
            build: {
                outDir: 'public/build/dynamic',
                manifest: 'dynamic-manifest.json',
            },
        });
        EOF
        
        # 2. Update package.json scripts
        npm pkg set scripts.build:core="vite build --config vite.core.config.js"
        npm pkg set scripts.build:dynamic="vite build --config vite.dynamic.config.js"
        npm pkg set scripts.deploy:hybrid="npm run build:core"
        
        # 3. Build core assets
        npm run build:core
        
        # 4. Update .gitignore for selective inclusion
        cat >> .gitignore << 'EOF'
        # Hybrid Strategy: Selective asset inclusion
        /public/build/dynamic/
        /public/css/compiled/dynamic/
        # /public/css/compiled/core/  # Include core assets
        # /public/js/compiled/app.js  # Include main app
        EOF
        
        # 5. Update environment variables
        sed -i 's/BUILD_STRATEGY=.*/BUILD_STRATEGY=hybrid/' .env
        
        # 6. Commit changes
        git add .
        git commit -m "Migrate to Hybrid strategy"
        
        echo "✅ Migration to Hybrid completed"
        
        ```
        
        ---
        
        ## **8. Configuration Variations**
        
        ### **8.1 DeployHQ SSH Commands by Strategy**
        
        ### **Standard Pre-Built Commands**
        
        ```bash
        # Skip in A01: Node.js checks (optional)
        # Standard B03: Create shared directories
        # Skip C06: Asset building
        # Enhanced C04: Verify pre-built assets
        
        ```
        
        ### **Full Pre-Built Commands**
        
        ```bash
        # Skip in A01: Node.js and npm checks
        # Standard B03: Create shared directories
        # Skip C06: Asset building entirely
        # Minimal C04: Basic health checks only
        
        ```
        
        ### **Build-on-Server Commands**
        
        ```bash
        # Required A01: Node.js, npm, disk space checks
        # Enhanced B03: Install npm dependencies
        # **REQUIRED C06**: Server-side asset building
        # Enhanced C04: Verify server-built assets
        
        ```
        
        ### **Hybrid Commands**
        
        ```bash
        # Required A01: Node.js checks for dynamic builds
        # Enhanced B03: Selective npm install
        # Modified C06: Build dynamic assets only
        # Complex C04: Verify pre-built + server-built assets
        
        ```
        
        ### **8.2 Environment Variable Variations**
        
        ```bash
        # Universal Variables (All Strategies)
        BUILD_STRATEGY=strategy_name    # pre-built, full-prebuilt, build-on-server, hybrid
        NODE_ENV=production
        APP_ENV=production
        
        # Strategy-Specific Variables
        ## Standard Pre-Built
        DEPLOY_ASSETS=false
        BUILD_ON_DEPLOY=false
        
        ## Full Pre-Built
        DEPLOY_ASSETS=false
        BUILD_ON_DEPLOY=false
        INCLUDE_NODE_MODULES=true
        SERVER_NODE_REQUIRED=false
        
        ## Build-on-Server
        DEPLOY_ASSETS=true
        BUILD_ON_DEPLOY=true
        NPM_CONFIG_PRODUCTION=false
        SKIP_INSTALL_SIMPLE_GIT_HOOKS=1
        
        ## Hybrid
        DEPLOY_ASSETS=partial
        BUILD_ON_DEPLOY=true
        BUILD_CORE_LOCAL=true
        BUILD_DYNAMIC_SERVER=true
        BUILD_THEMES=true
        BUILD_USER_ASSETS=false
        
        ```
        
        ### **8.3 DeployHQ Excluded Files by Strategy**
        
        ### **Universal Exclusions (All Strategies)**
        
        ```
        .git/
        .gitignore
        .deployignore
        README.md
        /docs/
        /tests/
        .DS_Store
        .env.local
        .env.testing
        Admin-Local/
        .vscode/
        .idea/
        
        # Core Laravel Shared (Always excluded)
        .env
        .env.*
        /storage/
        /bootstrap/cache/
        /public/.well-known/
        
        # Dynamic User Content (Always excluded)
        /public/*upload*/
        /public/*avatar*/
        /public/*media*/
        [... all user content patterns]
        
        ```
        
        ### **Strategy-Specific Exclusions**
        
        **🟢-A Standard Pre-Built ADDITIONAL:**
        
        ```
        /node_modules/
        package-lock.json
        webpack.mix.js
        vite.config.js
        /resources/sass/
        /resources/js/components/
        
        ```
        
        **🟢-B Full Pre-Built ADDITIONAL:**
        
        ```
        # Minimal additional exclusions
        /public/hot
        webpack.mix.js      # Build config not needed
        vite.config.js      # Build config not needed
        /resources/sass/    # Source files not needed
        
        ```
        
        **🔴 Build-on-Server ADDITIONAL:**
        
        ```
        /node_modules/      # Will be installed on server
        # Keep all source files and build configs
        
        ```
        
        **🟡 Hybrid ADDITIONAL:**
        
        ```
        /node_modules/
        package-lock.json
        # Selective exclusions based on build split
        /public/build/dynamic/
        /public/css/compiled/dynamic/
        
        ```
        
        ---
        
        ## **9. Troubleshooting by Strategy**
        
        ### **9.1 Standard Pre-Built Issues**
        
        ### **Assets Not Loading**
        
        ```bash
        # Diagnosis
        echo "🔍 Standard Pre-Built: Asset Loading Issues"
        
        # Check if assets are built
        ls -la public/build/ public/css/compiled/
        
        # Check if assets are in Git
        git ls-files | grep -E "(public/build|public/css|public/js)"
        
        # Verify manifest files
        cat public/mix-manifest.json 2>/dev/null || echo "Missing manifest"
        
        # Solution
        npm run production
        git add public/build/ public/css/ public/js/ public/mix-manifest.json
        git commit -m "Fix missing pre-built assets"
        
        ```
        
        ### **Build Size Too Large**
        
        ```bash
        # Analysis
        du -sh public/build/ public/css/ public/js/
        
        # Optimization
        npm run production -- --analyze
        # Review webpack-bundle-analyzer output
        # Consider splitting to Hybrid strategy
        
        ```
        
        ### **9.2 Full Pre-Built Issues**
        
        ### **Repository Size Too Large**
        
        ```bash
        # Analysis
        du -sh . --exclude='.git'
        du -sh node_modules/
        
        # Solutions
        # 1. Use Git LFS
        git lfs track "node_modules/**/*.tar.gz"
        git lfs track "*.zip"
        
        # 2. Clean unnecessary files
        npm prune --production
        find node_modules/ -name "*.md" -delete
        find node_modules/ -name "test*" -type d -exec rm -rf {} +
        
        # 3. Consider migration to Standard Pre-Built
        
        ```
        
        ### **Slow Git Operations**
        
        ```bash
        # Diagnosis
        git count-objects -vH
        
        # Solutions
        # 1. Use shallow clones in CI
        git clone --depth 1 repo-url
        
        # 2. Use Git LFS for large files
        git lfs migrate import --include="*.tar.gz,*.zip"
        
        # 3. Consider .gitattributes optimization
        echo "node_modules/** binary" >> .gitattributes
        
        ```
        
        ### **9.3 Build-on-Server Issues**
        
        ### **Build Timeouts**
        
        ```bash
        # Increase timeout in DeployHQ
        # Check server resources
        free -h
        df -h
        
        # Optimize build process
        export NODE_OPTIONS="--max-old-space-size=4096"
        npm ci --prefer-offline
        npm run production
        
        ```
        
        ### **Dependency Installation Failures**
        
        ```bash
        # Check npm configuration
        npm config list
        
        # Clear npm cache
        npm cache clean --force
        
        # Use specific npm version
        npm install -g npm@8.19.0
        npm ci --silent
        
        ```
        
        ### **Out of Memory During Build**
        
        ```bash
        # Increase Node.js memory
        export NODE_OPTIONS="--max-old-space-size=4096"
        
        # Monitor memory usage
        while true; do free -h; sleep 5; done &
        npm run production
        
        # Consider migration to Standard Pre-Built for large projects
        
        ```
        
        ### **9.4 Hybrid Issues**
        
        ### **Asset Conflicts**
        
        ```bash
        # Check manifest files
        cat public/css/compiled/core/core-manifest.json
        cat public/build/dynamic/dynamic-manifest.json
        
        # Verify no duplicates
        comm -12 <(sort core-manifest.json) <(sort dynamic-manifest.json)
        
        # Fix conflicts in build configs
        
        ```
        
        ### **Complex Build Dependencies**
        
        ```bash
        # Separate dependency management
        npm install --save-exact core-dependencies
        npm install --save-dev build-dependencies
        
        # Document build order
        echo "Build Order: core → dynamic → themes" > BUILD_ORDER.md
        
        ```
        
        ---
        
        ## **Strategy Recommendations**
        
        ### **For New Projects**
        
        1. **Start with 🟢-A Standard Pre-Built** for most cases
        2. **Use 🟢-B Full Pre-Built** for enterprise/air-gapped environments
        3. **Consider 🟡 Hybrid** for complex multi-tenant applications
        4. **Avoid 🔴 Build-on-Server** unless server resources are abundant
        
        ### **For Existing Projects**
        
        1. **Audit current pain points** before changing strategy
        2. **Test migration in staging** environment first
        3. **Document rollback procedures** for each strategy change
        4. **Consider gradual migration** for Hybrid strategy
        
        ### **For Teams**
        
        1. **🟢-A Standard Pre-Built**: Best for most development teams
        2. **🟢-B Full Pre-Built**: Best for enterprise teams with strict security
        3. **🔴 Build-on-Server**: Best for teams with limited local resources
        4. **🟡 Hybrid**: Best for advanced teams with complex requirements
        
        ---
        
        **📋 Quick Decision Matrix:**
        
        | If You Have... | Choose Strategy |
        | --- | --- |
        | Reliable internet + Standard team | 🟢-A Standard Pre-Built |
        | Enterprise security + Complex deps | 🟢-B Full Pre-Built |
        | Limited local resources + Good server | 🔴 Build-on-Server |
        | Complex app + Advanced team | 🟡 Hybrid |
        
        ---
        
        **End of Guide**
        
    - **DeployHQ Deployment Checkboxes**
        
        ## 1. What Are They?
        
        - **Copy config files to server?**
            - Decides whether DeployHQ uploads environment-specific configuration files (like `.env`) to your server during deployment.
        - **Run build commands?**
            - Controls whether the Build Pipeline steps (such as dependency installation and asset compilation) are executed as part of your deployment.
        - **Use build cache? (if available)**
            - Determines if DeployHQ reuses cached dependencies and previous build outputs for quicker builds. Useful for repeat deployments; not always needed for the first run.
        - Steps Per Deployment Strategy
            
            ## 🟢-A Standard Pre-Built Strategy
            
            - **What:** All build and asset compilation is done before code is committed. Only built assets are in the repo (excluding `node_modules`).
            - **Steps:**
                1. **Check:** Copy config files to server
                2. **Uncheck:** Run build commands (not needed, assets built already)
                3. **Uncheck:** Use build cache (not relevant)
            
            ## 🟢-B Full Pre-Built Strategy`[use this if we want to push all from local (CodeCanyon Apps]`
            
            - **What:** All dependencies and built assets (including `node_modules`) are tracked in Git.
            - **Steps:**
                1. **Check:** Copy config files to server
                2. **Uncheck:** Run build commands (everything is ready)
                3. **Uncheck:** Use build cache (not required)
            
            ## 🔴 Build-On-Server Strategy
            
            - **What:** Your repo only contains source files—dependencies (`node_modules`) and built assets are ignored by Git and must be built on the server.
            - **Steps:**
                1. **Check:** Copy config files to server
                2. **Check:** Run build commands (to install/build everything)
                3. **Check:** Use build cache (for faster subsequent builds)
                    - **Uncheck "Use build cache"** if you are troubleshooting or need a fresh environment
            
            ## 🟡 Hybrid Strategy
            
            - **What:** A mix—core assets are committed, dynamic/runtime-generated assets are built on the server.
            - **Steps:**
                1. **Check:** Copy config files to server
                2. **Check:** Run build commands (for the parts excluded from Git)
                3. **Check:** Use build cache (to speed up builds for dynamic parts)
            
            ## Summary Table
            
            | Strategy | Copy Config Files | Run Build Commands | Use Build Cache |
            | --- | --- | --- | --- |
            | Standard Pre-Built | Check | Uncheck | Uncheck |
            | Full Pre-Built | Check | Uncheck | Uncheck |
            | Build-On-Server | Check | Check | Check* |
            | Hybrid | Check | Check | Check |
            - Uncheck cache for Build-On-Server if troubleshooting or doing a clean build.
            
            ## In Summary
            
            ## Critical Issues
            
            - Skipping **"Copy config files"** or **"Run build commands"** when needed often leads to failed, broken, or missing deployments.
                - **Copy config files - Unchecked (should be checked):** Environment files like `.env` won’t be uploaded. The server may use default or stale configs, leading to broken connections or missing secrets.
                - **Copy config files - Checked (shouldn't be):** You could overwrite existing config files, but this usually isn't serious unless preserving unique server files matters.
                - **Run build commands - Unchecked (should be checked):** Key build steps (like installing dependencies or compiling assets) won’t run. The deployed code will lack essential files, breaking your app or site.
                - **Run build commands - Checked (shouldn't be):** Wastes time and may overwrite pre-built assets or introduce subtle bugs if server and dev environments differ.
            
            ## Non-critical (Mostly Performance)
            
            - **"Use build cache"** is less risky—usually only affects build speed or, rarely, troubleshooting scenarios.
                - **Unchecked (should be checked):** Deployments run slower since everything is freshly installed, but functionality isn’t affected.
                - **Checked (shouldn't be):** On first deployments, there's no downside. If troubleshooting, this may keep old or corrupted files, triggering weird errors.
            
            > Rule of thumb:
            > 
            > 
            > If you do the opposite of what’s right for your chosen deployment strategy, there is a high risk of your application not working correctly, with the severity depending on which checkbox is at fault. Always double-check before deploying!
            > 
            1. https://ppl-ai-file-upload.s3.amazonaws.com/web/direct-files/attachments/images/45810103/b2ba5add-06be-4783-b3af-42ce51bc7934/image.jpg
    - **Environment Variables (.env) Analysis for DeployHQ SSH Commands**
        
        ## **Environment Variables Analysis for DeployHQ SSH Commands**
        
        ### **🔄 Current Setup Context:**
        
        - **Build Pipeline**: DeployHQ handles building (composer2, npm ci, npm run build)
        - **SSH Commands**: Handle post-build deployment tasks (symlinks, artisan, cache)
        - **No .env exists**: Domain was reset, shared .env needs to be created
        
        ### **📋 Variable Relevance Assessment:**
        
        | Variable | Relevant? | Purpose | Recommendation |
        | --- | --- | --- | --- |
        | `BUILD_STRATEGY=build-on-server` | ✅ **YES** | Laravel app logic can check deployment method | **ADD** |
        | `NODE_ENV=production` | ✅ **YES** | Standard production optimization for Node/npm | **ADD** |
        | `DEPLOY_ASSETS=true` | ⚠️ **MAYBE** | Custom variable for app logic | **OPTIONAL** |
        | `BUILD_ON_DEPLOY=true` | ⚠️ **MAYBE** | Custom variable for deployment awareness | **OPTIONAL** |
        | `NPM_CONFIG_PRODUCTION=false` | ❌ **NO** | Only needed during build, not runtime | **SKIP** |
        
        ### **💡 Final Recommendation:**
        
        **✅ ADD to .env template:**
        
        ```bash
        # Production Environment
        NODE_ENV=production
        BUILD_STRATEGY=build-on-server
        
        # Optional: Deployment Context (if your Laravel app uses these)
        DEPLOY_ASSETS=true
        BUILD_ON_DEPLOY=true
        
        ```
        
        **❌ SKIP:**
        
        - `NPM_CONFIG_PRODUCTION=false` - This is a build-time configuration that should be set in the Build Pipeline environment, not in the application's runtime .env
        
        ### **🎯 Why These Help:**
        
        1. **`NODE_ENV=production`** - Ensures Laravel/Node libraries use production optimizations
        2. **`BUILD_STRATEGY=build-on-server`** - Your Laravel app can conditionally handle asset paths or caching strategies
        3. **Optional vars** - Useful if your application has conditional logic based on deployment method
        
        ### **🔧 Impact on SSH Commands:**
        
        - **No conflicts** with existing SSH commands (B01, B02, B03, C05)
        - **No changes needed** to current SSH command logic
        - These variables provide **runtime context** to your Laravel application, not deployment instructions
        
        **Conclusion:** These variables are complementary to our SSH commands and should be included in the .env template for the fresh deployment test.