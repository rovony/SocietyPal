
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


what are the suggested changes to have to the build and deploy flow?
Q2: are there any changes we should implment in the 
Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/00-Previous-Version/Phase-2-Pre-Deployment-Preparation/1-Steps/master-checklist-Phase2.md

or Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-1-Project-Setup/1-Steps/master-checklist-Phase1.md
like any steps to change or add to ensure that we dont have such issues on server? or how can we handle devdependiencies needed for production, should we add step to idnetify and add them to production in composer and package or not or rather handle via build and deploy commands, im not sure or bioth