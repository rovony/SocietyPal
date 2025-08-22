
# To ensure we catch all potential errors before they happen 
we need to do the below things

1- on local - try to go through the install process as sometimes when u install some errors maybe caught (by visiting website.com/install)
2- Ensure 100% accurecy of The Build-PipelineSame Packages (PHP and if used, NPM) are 



# Introdcution:
to ensure we have the build pipeline cover all potential sources of issues when building on the Build VM and deploying to the server (or Even if Building on the server). The sources of errors can come from the below reasons:
1. Variations in Build Configurations Versions: Composer version, NPM Version
2. the use of dev depencncy that may be not installed on production when using `composer install  --no dev` or `npm ci`.
3. what else ?




# Objectives:
1. use this step to document required build configruations versions, and provide commands + steps to check and confirm on server and buid VM.
1.1 Per Project identify versions needed for composer, npm , else? and instructions to ensure same or acceptable versions (if not exact) on build vm and server (even if no build on server as to use commands, packaes, etc u may need laravel and composer compatable for example, or npm)
2. Design a Build pipeline that will try to catch all and any of the potential dependcies.
2.1. Per Project 2ndary confirmation steps of the build pipeline: Also in addition to this Universal Build Pipeline- add few steps with commands to confirm if anything else needs to be adedd (not removed as Universal Build Pipeline should use conditional language in case some apps dont work)


so we need to have
1 - any work to do on local Mchine (step 19.1): any work to do while in local machine to be part of step 19.1 (maybe also rename ste) in Admin-Local/0-Admin/zaj-Guides/1-Guides-Flows/B-Setup-New-Project/Phase-2-Pre-Deployment-Preparation/1-Steps/Step-19.1-Ensure-NoBuild-Errors-and-Ensure-Full-Accurate-Build-Pipeline.md
to include steps, scripts, etc.. steps to investigate and any suggestion changes to either Build pipleline, ssh -pipleline, etc. 




Note current version of the 

- Universal  SSH Pipleine
 Build Pipeline 








Very important:
the above request we aim to have it work for any laravel app with and without js, not just societypal (current workspace, this is just an example first project we are using to help us setup the steps, scripts, pipelines etc.)



so yea we want steps to follow to do things
universal build pipeline , universal ssh pipeline - we currently have v1 of both. maybe no change to ssh unless we need we can add then.

also steps to follow to verify on local no other than universal and step 19.3 to setup and run and use the Dry github thing per Expert 3 if u agree, and if any elements from Expert 2 and 1.

--
