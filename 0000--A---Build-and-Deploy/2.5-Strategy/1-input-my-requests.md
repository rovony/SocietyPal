- 2- My Comments
    
    ### **Comments1**
    
    1. Phase 1 improvements:
    - Start from Step of pushing the code to Github (gitignoring the build artifacts per laravel with and without js standards) so i understand better from that point.
    - include variable `%path%` in each step or as u see fit if better per substep as below, with example- this will help ensure we are on right location before we start the step. Also any other paths can be defined based on it in the step, if needs to navigate to inside or outside.
        - including:
            - `%path-localMachine%`: the path at which the step comands start at (like its the location)
                - Example:
            - `%path-server%`: the path at which the step commands start at (like its the location)
            - `%path-Builder-VM%`: not sure if we need buider path if we use VM, but if we do the build on local machine
            
            and include an initial step to define and identify these path variables to ensure the whole flow doesnt break. it should be 1 value for the whole setup like 1 `%path-localMachine%` and 1 
            
    - `add variables` to have set at start to make this more dynamic, like github url, commit-start, end - also see and add variables based on deployHQ. and maybe add initial step to define variables.
    - to help me as a begginer try to include explanation, improve the flow, no gaps that makes my wonder or confused or mis step - i mean dont assume that i would know something because its know as i am a begginer, must be begginer friendly.
    - Consider that some devdependiecies would be needed for production, composer and npm, should we handle this before the github commit, do people try to identify these and include in production depencicies on local machine before generating lock files, if so how to identify (like if we work on other ppl apps maybe hard to identify as we didnt develop, any command to indetify ) or is the way to handle by commands in the building part.
    - 
    1. 2nd round of imrpvements.
        - For each Step give it a short name in code format next to its name in brackets, example [`3-xxx xxx` `xxx`] - 2-3 (short, easy to understand step puprose from reading it).
        - For Steps that are to be done on Builder add a tag `🟡-onBuilder`, for steps to be done on local Machine, add a tag `🟢-onLocalMachine`, for steps to be done on Server, add a tag `🔴-onServer`.
        - to help understand what steps that can be changed, edited ..et c- for the Phases A, B, C = AKA as: `pre-release`, `mid-release`, and `post-release` SSH (user) hooks, consider to add to the step another tag [`🟣 1️⃣  User-Commands: Pre-release Hook`],  [`🟣` 2️⃣  `User-Commands: Mid-release Hook`],[`🟣` 3️⃣  `User-Commands: Post-release Hook`].
        - Consider including the concept of `deployignore` which shouldnt duplicate `gitignore` rather on top of any thing to ignore from github.
    
    3. third round
    
    - can we consider scenario of we want to do build on Local Machine.
    
    3. 4th round
    
    - can we consider the scenario if we want to do build on server
    
    ### **Comments2**
    
    - experianced issues:
        - Some devdependencies (Composer and or NPM) are needed for production
            - 1 example for clarifition but we need to cover all not just this:
                - Transfer Process: ⚠️ Partial corruption (illuminate/support missing)
                - corrupted optimized autoloader
                - build included faker but **Seeders use Faker and it's available as it was defined as devdepenncies.**
                - handling when dev dependencies needed for production. Laravel and npm
                    - should be added to production list in composer and package.json or should install on runner VM and or Server rather install production and development dependcies.
        - Server keeps defaulting to Composer 1 while app needs composer 2. even tho the server does have both. should we try to install composer per domain , or use default server. should we match exact version of composer, npm etc like v1.2.3 or main version is enough like v1 vs v2.
        - some edge cases faced:
            - an app laravel needs compoer 2.
    
    ### **Comments3**
    
    indetify and capture all Pitfalls to Avoid including but not limited to
    Running DB migrations that require downtime — use zero-downtime migration patterns (add columns, backfill, then drop old columns).
    Failing to clear application/opcode caches between releases.
    Not symlinking persistent folders (risking loss of uploads).
    Letting old releases pile up (disk bloat — always clean up).
    Forgetting to restart background queue/workers/webserver processes (they may keep using old code).
    Some devdependencies (Composer and or NPM) are needed for production
    Server keeps defaulting to Composer 1
    Versions Mismatch of dependcies from composer, npm, on local, build VM, Server and value of using lock files with exact versions like not^
    Config Version mismatch of composer version, npm , etc on local, builder vm, server.
    when no builder vm avaiable (builder down) to build locally and zip build artifact.
    when on shared hosting limitation and how to handle
    when root is public_html, to handle to create besides app in domain to convert public_html to symlik.
    ensuring no user data loss, uploads, etc between deployments.
    
    i assume all flow steps will be exactly in first and subsequent deployments with the use of conditional logic flows or (ex: -p when creating dir).
    And Regive me full master Zero-Downtime Deployment Flow for laravel with and without js
    
    ### **Comments4**
    
    - i assume we coverd but itll be helpful ensure its clear the steps include
        - **To Ensure avaiability and  Compatability**
            - **PHP Extensions**: checks what the app needs  critical PHP extensions like `pdo`, `openssl`, `mbstring`, and `curl` on localMachine and esnure these are setup on server to avoid failed deployments.
                - or if user needs to enable some on hosting UI like things requred by laravel apps like `exec()` function (like to be removed from execluded_Functions)
            - **Versions:** check what php, composer, npm version needed maybe on local and ensure its used right at the start of server steps or builder.
            - Tools: like`curl`, `git`, and `mysql , else`
        - **Setup shared directory** :
            - and ensure no user data or uploads loss for various shared folders given standard laravel fodlers, and any custom or differernces between apps from different developers.
            - ensure no user uploads or data is lost during subsequent deployments given flow is to be run as is exactly in first deployment and subsequent deployments.
            - maybe to an extensive set of shared directories under `shared/public/` to handle various types of user-generated content, ensuring files like uploads, avatars, and documents are not lost during a deployment
        - **pre-flight checks:**
            - **for exmaple ensuring composer2 is used if app needs it,**
        - dataset Backups:
            - application's database using secure way to use env file.
        - maintenance mode on and off
            - Enter and Exit Maintenance Modes at right times.
            - Full correct setup of symlinks not sure what but maybe including not limited, but maybe storage, bootstrap/cache, shared/.env, modules, some hosting have public_html (to use this strategy for security, be public symlinked) if a folder exist to backup fodler and use symlink, ensure works for first and subseqeuent even if some hosting may not have public_html by default.
        - Creating`.htaccess` files
            - - all needed to ensure secure, redirecs, like to `/public` directory, security, etc..
        - domain root structure:
            - maybe to use standard 3 folder setup as if for example domain root is
                - deploy - for zero down : releases, shared, current - also secured so no public access as needed for such apps..
                - public_html - symliked and only publicly acccessable.
                - secured: a folder for like backups,  reports, logs, etc..
        - Clean Local Machine Setup for deployment
            - maybe have 1 folder on local machine Admin-Local/Deployment where we have files related to deployment organized.
        - handling env file:
            - create an isolated Admin-Local/Deployment/EnvFiles/`.env.production`,`.env.staging`, etc  for different env per deployment env
            - deployignore ensure in case of leaks to ignore .env, keep .env.example.
            - On the first deployment or subseqent deployments
                - it moves the Admin-Local/Deployment/EnvFiles/`.` uploaded `.env` to the `shared` directory and adds the shared
                - check if git uploaded a .env (shouldnt but as a 2ndary measure), removes it and instantanesuly use the shared/.env - and that should be as part of the symlink to current.
        - public_html (Public Access)
            - to handle if a public_html folder exist, delete or backup and symlink as a file to point the public web directory (`public_html`) to the new release public, and  handle
            - creates or updates the `public_html` symlink to point to the `deploy/current/public` directory
        - accurate Symlink setup
            - for first deployment and subsequent deployemts.
            - ensure same steps can be run
        - Health checks
            - during the deployment to ensure no breakage that would corrupt the setup.
            - at start , verifications and at end health check to ensure the website is online and responding with a correct status code, and if not rollback to ensure the latest deployment not just doesnt ruin app, but also as sometimes a deployment may cause issues in folder structure, symlinks, etc - to roll back these these changes of the last deployment (as a security measure in case a deployemnt steps was missing an edge case substep , dependciesy etc) to fix .
        - overall
            - ensure the same steps can be run for first deployment or subsequent deployments using things like -p when creating folders
            - ensure no user uploads loss while considering diff apps might have the standard laravel folders and additional diff custom folders
            - Rollback (issues with app code files, database): in case we need to rollback for previous deployment if the app code
            - Last-Deployment-Discard (for issues with build and deploy pipeline):
                - if not rollback to ensure the latest deployment not just doesnt ruin app, but also as sometimes a deployment may cause issues in folder structure, symlinks, etc - to roll back these these changes of the last deployment (as a security measure in case a deployemnt steps was missing an edge case substep , dependciesy etc) to fix .
    
    ### **Comments5**
    
    - also as i am learning, i really need to have checks after major steps to ensure what did worked, verify and not be blind or maybe i did wrong and it propagates also keeps me motivated.
    - Steps to do
        - Get 1-19 steps as short list.
        - Example Info
            - local Path: `%path-localMachine%`
                - Example: `/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root/vite.config.js`
            - Server Path: `/home/u227177893/domains/dev.societypal.com`
                - deploy folder maybe thats the `%path-server%`
                    - `/home/u227177893/domains/dev.societypal.com/deploy`
            - `%path-Builder-VM%`
                - not sure if its a builder VM
                - but if we building on local machine we can define it as part local Path: `%path-localMachine%` or local Path: `%path-localMachine%/folder`
            - however keep in mind we need the full steps and details to cover and be able to use with any project, maybe using json project, deployments, etc..
            - variables,
    
    ### **Comments6**
    
    Also ensure to cover including steps to cover all  Pitfalls to Avoid as my goal is to understand the best zero down deployment pipleline for laravel apps with and without js.
    
    maybe also try to indetify and capture all Pitfalls to Avoid including but not limited to
    Running DB migrations that require downtime — use zero-downtime migration patterns (add columns, backfill, then drop old columns).
    Failing to clear application/opcode caches between releases.
    Not symlinking persistent folders (risking loss of uploads).
    Letting old releases pile up (disk bloat — always clean up).
    Forgetting to restart background queue/workers/webserver processes (they may keep using old code).
    Some devdependencies (Composer and or NPM) are needed for production
    Server keeps defaulting to Composer 1
    Versions Mismatch of dependcies from composer, npm, on local, build VM, Server and value of using lock files with exact versions like not^
    Config Version mismatch of composer version, npm , etc on local, builder vm, server.
    when no builder vm avaiable (builder down) to build locally and zip build artifact.
    when on shared hosting limitation and how to handle
    when root is public_html, to handle to create besides app in domain to convert public_html to symlik.
    ensuring no user data loss, uploads, etc between deployments.
    
    i assume all flow steps will be exactly in first and subsequent deployments with the use of conditional logic flows or (ex: -p when creating dir).
    And Regive me full master Zero-Downtime Deployment Flow for laravel with and without js
    
    - Steps to do
        - Get 1-19 steps as short list.
        - Example Info
            - local Path: `%path-localMachine%`
                - Example: `/Users/malekokour/Zaj_Master/MyApps/MyLaravel_Apps/2_Apps/SocietyPal-Project/SocietyPalApp-Master/SocietyPalApp-Root/vite.config.js`
            - Server Path: `/home/u227177893/domains/dev.societypal.com`
                - deploy folder maybe thats the `%path-server%`
                    - `/home/u227177893/domains/dev.societypal.com/deploy`
            - `%path-Builder-VM%`
                - not sure if its a builder VM
                - but if we building on local machine we can define it as part local Path: `%path-localMachine%` or local Path: `%path-localMachine%/folder`
    
    <aside>
    💡
    
    Notes:
    
    - Phases A, B, C = AKA as: `pre-release`, `mid-release`, and `post-release` SSH (user) hooks
    - **Build and Deploy Options we want to cover**:  all include Github for Codebase. below are for build and deploy:
        1. **Only Local Machine and Server**: no deployer (only local machine and server ssh remote exteions, build on local and ssh zip and github repo codebase for version control), 
        2. **DeployHQ** (has builder VM and deployer ssh setup with ability to add `pre-release`, `mid-release`, and `post-release` SSH (user) hooks
        3. Github Actions.
    - Local Testing: `HERD PRO`.
    - we must have things as variables so we can use same steps for other projects other websites, different github repos, different commits, etc..we can maybe collect and store project, deployment variables in 1 json file and use or add for next updates, new file for new app, project etc.
    - For Zero Down
        - The *use of symlinks* for swapping releases instantly is industry standard
        - Make sure shared folders (storage, .env, cache, etc.) are correctly symlinked to each new release to avoid config leaks or loss of uploads
        - Retaining a history of old releases for fast rollback is mentioned — best practice.
        - Restoring cached directories (**node_modules/**, **vendor/**, etc.) for faster installs is correct
        - **Separation of Build/Deploy Steps:**
            - Pipeline has a clear distinction between building (on runner VM) and deploying (to live server); this is both accurate and best practice
        - edge-case controls (e.g., custom health checks, transactional schema migrations) require manual scripting/hooks
    </aside>
    
    - experianced issues:
        - Some devdependencies (Composer and or NPM) are needed for production
            - 1 example for clarifition but we need to cover all not just this:
                - Transfer Process: ⚠️ Partial corruption (illuminate/support missing)
                - corrupted optimized autoloader
                - build included faker but **Seeders use Faker and it's available as it was defined as devdepenncies.**
                - handling when dev dependencies needed for production. Laravel and npm
                    - should be added to production list in composer and package.json or should install on runner VM and or Server rather install production and development dependcies.
        - Server keeps defaulting to Composer 1 while app needs composer 2. even tho the server does have both. should we try to install composer per domain , or use default server. should we match exact version of composer, npm etc like v1.2.3 or main version is enough like v1 vs v2.
        - some edge cases faced:
            - an app laravel needs compoer 2.

---
## Organized & Cleaned Requests

### 1. **Deployment Flow Structure and Clarity**
- **Objective**: Make the deployment guide beginner-friendly, easy to follow, and highly structured.
- **Numbered Bullets**:
    1.  **Start Point**: Begin the detailed flow from the step of pushing code to GitHub, assuming local setup is complete.
    2.  **Path Variables**:
        -   Define and use standardized path variables throughout the guide:
            -   `%path-localMachine%` (e.g., `/Users/malekokour/.../SocietyPalApp-Root`)
            -   `%path-server%` (e.g., `/home/u227177893/domains/dev.societypal.com/deploy`)
            -   `%path-Builder-VM%` (or a local build directory).
        -   Include an initial step to define these variables, possibly in a `deployment-config.json` file.
    3.  **Dynamic Variables**: Use variables for project-specific details like GitHub URL, commit hashes, etc., to make the template reusable.
    4.  **Visual Tags**:
        -   `🟢-onLocalMachine`: For steps performed on the local development machine.
        -   `🟡-onBuilder`: For steps performed on a CI/CD builder VM.
        -   `🔴-onServer`: For steps performed on the live production server.
        -   `🟣 1️⃣, 2️⃣, 3️⃣`: To mark locations for user-configurable hooks (pre-release, mid-release, post-release).
    5.  **Verification Steps**: After each major step, include a verification command or check with expected output to confirm success and prevent errors from propagating.

### 2. **Dependency and Environment Management**
- **Objective**: Proactively identify and resolve dependency and environment mismatches before they cause deployment failures.
- **Numbered Bullets**:
    1.  **Dev Dependencies in Production**:
        -   Create a definitive process to identify `require-dev` packages (like Faker, Telescope, Debugbar) that are needed for production tasks (e.g., seeding, migrations).
        -   This process should run *before* committing to GitHub.
        -   Provide a clear strategy: either move them to `require` in `composer.json` or ensure the build process installs them conditionally.
    2.  **Version Consistency**:
        -   Implement checks to ensure PHP, Composer, and Node.js versions are consistent across local, builder, and server environments.
        -   Force Composer v2 if the server defaults to v1.
        -   Recommend using lock files (`composer.lock`, `package-lock.json`) with exact versions (no `^` or `~`) to ensure deterministic builds.
    3.  **Environment Compatibility**:
        -   Add pre-flight checks for required PHP extensions (`pdo`, `openssl`, `mbstring`) and server functions (`exec`).
        -   Ensure necessary tools (`git`, `curl`, `mysql`) are installed on the server/builder.

### 3. **Zero-Downtime Deployment and Safety**
- **Objective**: Ensure the deployment process is seamless, with no downtime, no data loss, and robust safety nets.
- **Numbered Bullets**:
    1.  **Build Scenarios**: The guide must cover three primary build and deployment strategies:
        -   **Local Build**: Build on the local machine, then transfer the artifact via SSH.
        -   **DeployHQ**: Utilize a builder VM with user-configurable SSH hooks.
        -   **GitHub Actions**: Automate the build and deploy process via CI/CD pipelines.
    2.  **Shared Resources**:
        -   Implement a robust symlinking strategy for shared directories (`storage`, `.env`) and user-generated content folders (`public/uploads`, `public/avatars`) to prevent data loss.
        -   Handle the `public_html` directory structure common on shared hosting.
    3.  **Safety and Rollbacks**:
        -   Integrate health checks after deployment to verify the application is running correctly.
        -   Provide a clear, actionable rollback plan to revert to the previous release if a health check fails.
        -   Include steps for entering and exiting maintenance mode at the appropriate times.
    4.  **Database Migrations**: Emphasize zero-downtime migration patterns (e.g., add new columns before removing old ones).
    5.  **Cache and Queues**: Include mandatory steps to clear application/OPcache and restart queue workers to ensure the new code is active.

### 4. **Security and Configuration**
- **Objective**: Secure the application and manage environment-specific configurations properly.
- **Numbered Bullets**:
    1.  **Environment File (`.env`)**:
        -   Establish a secure workflow for managing production `.env` files.
        -   The `.env` file should never be in Git. It should be placed in the `shared` directory on the server and symlinked into each new release.
    2.  **`.htaccess` Configuration**: Provide template `.htaccess` files for security headers, HTTPS redirection, and routing requests to Laravel's `public` directory.
    3.  **File Permissions**: Specify the correct file and directory permissions needed for Laravel to run without issues.
    4.  **`deployignore`**: Introduce the concept of a `.deployignore` file to exclude files from the deployment artifact that are not covered by `.gitignore`.