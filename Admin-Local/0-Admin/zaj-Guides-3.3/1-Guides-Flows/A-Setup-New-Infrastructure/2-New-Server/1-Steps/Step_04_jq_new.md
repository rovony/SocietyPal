## Final Steps to Install `jq` for Turbo Deployment (DeployHQ)


# run below on server:

We installed `jq` manually because the server did not have the `jq` command available, which is required for DeployHQ’s Turbo Deployments ([DeployHQ jq requirement](https://www.deployhq.com/support/deployments/turbo-deployments)).

### Steps Followed

1. **Download the static binary:**
   ```bash
   mkdir -p ~/bin
   wget -O ~/bin/jq https://github.com/stedolan/jq/releases/download/jq-1.6/jq-linux64
   chmod +x ~/bin/jq
   ```

2. **Add `jq` to your PATH permanently:**
   ```bash
   echo 'export PATH=$HOME/bin:$PATH' >> ~/.bashrc
   source ~/.bashrc
   ```

3. **Verify installation:**
   ```bash
   jq --version
   # Output: jq-1.6
   ```

### Why We Did This

DeployHQ’s Turbo Deployment feature requires `jq` to parse JSON responses during deployment. Since the server did not have `jq` installed, we manually downloaded the static binary and updated the PATH so DeployHQ can detect and use
