# Configuring the Build Environment in DeployHQ

To mirror the language and tool versions, caching, and SSH restrictions shown in your example, configure your project’s **Build Configuration** in DeployHQ as follows.

***

## 1. Open Build Configuration

1. In your project sidebar, click **Build Pipeline**.  
2. At the top of the Build Pipeline page, click the **Build Configuration** tab.

***

## 2. Select Language Versions

Under **Language Versions**, DeployHQ lists all supported runtimes with their current versions. To override any of them:

1. Locate the language you wish to pin (.NET, Go, Java, NodeJS, PHP, Python, Ruby).  
2. Click its **Change Version** link.  
3. In the modal, choose the desired version from the dropdown.  
4. Save your selection.  

Repeat for each runtime your build pipeline requires.

***

## 3. Pin Package Manager Versions

Below the runtimes, find **Package Managers** (e.g., Composer):

1. Click **Change Version** next to Composer (or another manager).  
2. Select the specific version needed.  
3. Confirm to lock in that version for all subsequent builds.

***

## 4. Override Other Dependencies

If your build uses additional tools—such as PhantomJS—DeployHQ surfaces them under **Other Dependencies**:

1. Click **Change Version** next to the dependency name.  
2. Pick the required version.  
3. Save to ensure consistent behavior across build runs.

***

## 5. Configure Cached Files

Caching avoids repeatedly downloading large dependency sets:

1. Click the **Build Cache Files** link or follow **create your first cached file**.  
2. Click **New Cached File**.  
3. Enter a name and the folder path to cache (e.g., `~/.npm` or `vendor`).  
4. Save.  

DeployHQ will persist those paths between builds.

***

## 6. Define Known Hosts for SSH

To restrict SSH connections during your build:

1. Click the **Build Known Hosts** link or **add your first known host key**.  
2. Click **New Known Host**.  
3. Enter the host’s DNS name (e.g., `github.com`).  
4. Paste its public SSH key fingerprint.  
5. Save.  

Subsequent SSH calls during the build will only trust these hosts.

***

Once you’ve completed these steps, your DeployHQ builds will run with the exact language/runtime versions, package managers, and security constraints you require—just like your example configuration.
