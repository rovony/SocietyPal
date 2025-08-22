Here’s a clean grouping of your variables into bullets (groups) and sub-bullets (variables).

---

* **Revision Variables**

  * `%startrev%` → Start revision ref
  * `%endrev%` → End revision ref
  * `%startrevmsg%` → Start revision commit message (available on deployment finish or failure)
  * `%endrevmsg%` → End revision commit message (available on deployment finish or failure)
  * `%tag%` → Tag related to end revision (if present)
  * `%commitrange%` → The start and end commit, separated with a hyphen

* **Project Variables**

  * `%project%` → The name of this project in DeployHQ
  * `%projecturl%` → The address of this project in DeployHQ
  * `%projectperma%` → The permalink of this project in DeployHQ

* **Deployment Variables**

  * `%branch%` → The branch of the deployment
  * `%count%` → This number of deployments in the project
  * `%servers%` → Names of the servers in this deployment
  * `%deployer%` → User who started the deployment (if manually deployed)
  * `%deploymenturl%` → The address of this deployment in DeployHQ
  * `%status%` → The current status of this deployment
  * `%started_at%` → The ISO8601 timestamp representing the start time of the deployment
  * `%completed_at%` → The ISO8601 timestamp representing the completion time of the deployment
  * `%deployment_overview%` → The parsed and stripped deployment overview (if provided)

* **Server / Environment Variables**

  * `%environment%` → Server environment (development, production etc.)
  * `%path%` → Base server path we’re deploying to (server-only)

---

Do you want me to also **speculate practical usage examples** for each group (e.g., how they’d show up in logs, scripts, or notifications), or just keep it as a reference list?
