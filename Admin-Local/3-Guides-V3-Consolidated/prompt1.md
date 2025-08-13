
here is my initial request again

- i just wrote Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/
we will use this as example project. we did some of the initial steps, we want to setup first time. i need you to do 3 things
1- help me understand what the step will do, is it corrrect, right, etc before doing it, in short.
2- do it
3- if any imporvements to fix erorrs, avoid future errors when we try the same steps on future apps , to complie in tracker detailed so once we done to update the guides. (i want the step report (ex: ✅ STEP 01 SUCCESSFULLY COMPLETED
) u gave above and next steps all in 1 file so we can later review all. u write and update the file every after every step. and why did u stop we need to do all steps in 
)
do all steps in folders 
Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-1-Project-Setup
Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-2-Pre-Deployment-Preparation
Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-3-Deployment-Execution
Admin-Local/3-Guides-V3-Consolidated/1-Setup-New-Project/Phase-4-Post-Deployment-Maintenance


i want the step report (ex: ✅ STEP 01 SUCCESSFULLY COMPLETED
) u gave above and next steps all in 1 file so we can later review all. u write and update the file every after every step. and why did u stop we need to do all steps in 
- 🎯 Task Summary
- 1. ✅ UNDERSTANDING - What the step does:
- 2. ✅ EXECUTION - Completed successfully:
- 3. ✅ IMPROVEMENTS & ERROR PREVENTION:
-- 📋 Improvements Identified:
-- 🚨 Error Prevention Measures:
-- 🔧 Recommendations for Future Apps:


i am not sure if someone tried to do the steps on thhis project or not but if yes maybe before initail setsp , need to assess the current state since we discovered this might be a pre-existing setup.



note: for next steps enhance details for Improvements Identified and Error Prevention Measures and be specific so who reads later understand

if a step we identify is wrong and must be fixed given its execution results- how about u see what the step has, and we try to imrpve it, dont destroy , we edit, we ad, we remove, not destroy all overwerite.- we upgrade, we ensure fix works, update step, update tracker.. then we proceed to next


## RULES
1- When u strart a new project, setup a tracker to capture current app info and 
- Current App info
- Setup status: first time, update, deployment, card info etc.
- 🎯 Task Summary
- 1. ✅ UNDERSTANDING - What the step does:
- 2. ✅ EXECUTION - Completed successfully:
- 3. ✅ IMPROVEMENTS & ERROR PREVENTION:
-- 📋 Improvements Identified:
-- 🚨 Error Prevention Measures:
-- 🔧 Recommendations for Future Apps:


2- CodeCanyon Vendor Management Strategy:
Minimize vendor file modifications to avoid diff conflicts on updates
Use customization layer rules instead of direct vendor edits.
2-  if errors were found , ensure step are updated if had error found when executed step but then fixed, so next time step execuion doesnt cause errors.
3- when a script is used in a step md file, also add to step md below the script block, a bullet EXPLANATION, and subbullets and numbered subbullets,   explanation of whta the script do in summariezed yet easy to undersand way, steps maybe with example(s). aslo ensure to captuer that in tracker so we know what was done and track in case of issues.

4- dont write current project related outputs in steps files as we will use in future proejcts, u can include examples, but the fucking racker is for current setup project trakcers..steps should include steps and substeps, numberd. examples commands, outputs etc for the example project SocietyPro we are using.
5- ensure step files have steps and substeps as numbered headings and or bullets - not just headings and bullets.
6- in steps md files - include Tag Instruct-User  when you want user to open a file and follow instructions. the tag is with emoji,.

7- maybe partially or fully implenmented- well for steps that has parts that includes human tasks or you shouldnt proceed before u confirm i did actually do it. while for some human instrcutions or steps it might be difficult to test if user did things right but when possible you need to either confirm or use ask question tool to ensure user did step before moving to next step- this applies to steps that involves human tasks (maybe by ensuring the tag Instruct - user) maybe also ensure to update step 0 so these rules are considerd by the ai u or any future ai.
- note; we need to enusre we have the  involving human tasks marked with "Tag Instruct-User".  for all the steps files that actually have human tasks so ai (u or any future ai ) never miss that and proceed without confirming. confirmation can be mainly by asking user question using ask question tool, but also if the task can be confirmed by inspecting codebase, or using non editing non destructive commands that would be helpful. note when usinng ask question tool the question should be using a comprehensive summary template below:
step xx: xxx
HUMAN TASK REQUEIRED - alert emoijs. 
Short Desc
Steps human to follow

Question with options (please update that in step 0)
so in summary:
- we need to ensure ALL steps containing human tasks with the "🏷️ Tag Instruct-User 👤" tag using emojis
- - For any step with human tasks, the AI must confirm completion before proceeding to the next step
- - Use the ask_questions tool with this mandatory template format:
  ```
  Step XX: [Step Title]
  🚨 HUMAN TASK REQUIRED 🚨
  Short Description: [Brief task summary]
  Steps for human to follow:
  [Numbered list of human actions]
  
  Confirmation Question: [Specific question with multiple choice options when possible]
- When possible, supplement confirmation by inspecting the codebase or using non-destructive commands
- Never proceed to subsequent steps without explicit human confirmation of task completion
**Step 0 Enhancement:**
- Update step 0 to include these human task confirmation rules as core requirements
- Ensure any future AI following these guides will automatically recognize and respect the "🏷️ Tag Instruct-User 👤" tags
- Document the mandatory confirmation process and template usage
IMPOERTANT:  human instrucion tag is for things outside this coebase or commands, like hoing to herd and starting it, like going to github.com creating repo, like loggin into hosting and creating dataset, etc- things yu cant do.
make user tag as heading at top of a file that has a human task explnaning this step includew a human task and what to do any other steps are not done by human . and at the exact specific substeps that are human tasks.
