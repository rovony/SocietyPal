# AI Assistant Usage, Context, and Communication Guidelines

**Purpose:**

Provide a clear standardized reference for using an AI assistant throughout the deployment process, covering set-up, context, communication rules, and troubleshooting.

## **When to Use this Document**

- At the beginning of your deployment process,
- When returning to the project after a break,
- Each time you need to request help from the AI assistant during any step.

## **Startup Template (to paste into every new AI assistant conversation):**

```text
I am following the Universal Laravel Deployment Guide.

- I am at Step [X] ([Step Name]).
- My project is [Laravel version], using [frontend framework or 'none'].
- I need help with: [short description of challenge, question, or error].
- Full context: [copy error output or logs here if relevant].

Please answer in the step-by-step, beginner-friendly format used by the official guide.
```

Example — for mid-process support:

```text
I'm following the Universal Laravel Deployment Guide.  
I'm at Step 15: Dependencies Installation.  
My project is Laravel 10 with Vite.  
The error is:
```

[Insert your error message or describe your roadblock here.]

```text
Please provide a step-by-step solution in your official step format.
```

## **Rules for Communicating with the AI Assistant**

1. **Be Specific:** Always mention the current guide section and the precise step you are on.
2. **Share Context:** Clearly state your Laravel version, any frontend tools, and the hosting/server type.
3. **Copy Logs/Errors:** Paste exact error messages or symptoms; avoid paraphrasing technical errors.
4. **Request Beginner Format:** Specify that you want a clear, beginner-friendly, step-by-step or checklist response.
5. **Iterate:** If a step doesn't resolve the issue, reply with the new error or state of the system, and request a next action.

---

## **Expected Result**

- `ai-context-and-rules.md` appears at the project root
- The file contains the clear template and communication rules above
- All users and AI assistants refer to this file at project start and when troubleshooting
- All AI conversations are structured, reproducible, and provide the needed context for the Universal Laravel Deployment workflow

---

**Summary:**

By performing Step 00, you ensure every user and AI conversation is consistently structured, making help and troubleshooting efficient, documented, and beginner-friendly right from the root of your project.