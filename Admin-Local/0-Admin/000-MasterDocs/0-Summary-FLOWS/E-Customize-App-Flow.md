Of course. Here is the detailed workflow for "Flow E," the process for customizing the application.

This flow is the day-to-day process your development team will use to add value to the project. It is designed from the ground up to integrate perfectly with the protection and tracking systems you have in place, ensuring every customization is safe, documented, and its value is tracked.

---

### Flow E: Customize Application

**Objective**: To provide a structured, repeatable, and safe process for adding new features or modifying existing ones, ensuring every change is fully documented, tested, and seamlessly integrated with the investment protection and customization protection systems.

#### Phase 1: Requirement & Planning

**Objective**: To ensure every customization is well-defined, planned, and justified _before_ a single line of code is written.

1.  **Step 45: Define Business Requirement & Scope**

    -   **Description**: Clearly document the goal of the customization. What problem does it solve? Who is it for? What are the specific requirements and acceptance criteria?
    -   **Action**: Create a new entry or task in your project management tool (e.g., Jira, Trello) or directly within a planning document.
    -   **System Usage**:
        -   **Project Tracker**: Add a new entry for this customization, linking to the detailed requirement document. Mark Step 45 as complete.

2.  **Step 46: Log in Investment Tracker (Pre-Authorization)**

    -   **Description**: This is a critical step. Before starting work, create a new entry in the `Investment-Tracker.md`.
    -   **Action**: Add a new section to the tracker with the following details:
        -   **Feature Name**: A clear, descriptive name.
        -   **Business Case**: A brief explanation of _why_ this feature is being built (e.g., "To increase user retention by 10%").
        -   **Estimated Effort**: A rough estimate of the time required.
        -   **Status**: `Planned`
    -   **System Usage**:
        -   **Investment Tracker**: This is the primary action. It serves as the "green light" for the development work to begin and starts the ROI calculation process.
        -   **Project Tracker**: Mark Step 46 as complete.

3.  **Step 47: Create Technical Plan & Identify Impact**

    -   **Description**: Analyze the vendor code to determine the best way to implement the feature with minimal direct modifications. Identify which files will need to be overridden or extended.
    -   **Action**: Write a brief technical plan outlining the approach (e.g., "Create a new service class, override the `ProductController` `show` method, add a new Blade template").
    -   **System Usage**:
        -   **Customization Manifest**: This plan will directly inform which files will be added to the manifest later.
        -   **Project Tracker**: Mark Step 47 as complete, attaching the technical plan.

4.  **Step 48: Create New Git Feature Branch**
    -   **Description**: Isolate all work for this new feature in its own branch.
    -   **Action**: From the `develop` branch, create a new branch with a descriptive name (e.g., `feature/user-profile-avatars`).
    -   **System Usage**:
        -   **Project Tracker**: Mark Step 48 as complete, noting the branch name.

#### Phase 2: Protected Development

**Objective**: To write high-quality, isolated code that respects the customization protection layer.

5.  **Step 49: Write Code in Isolation**

    -   **Description**: Following the core principle of the "Zaj Style", all new or modified code must be placed within the `/1-Source-Code/c-custom-code/` directory. You should never directly edit files in `/b-vendor-modified/`.
    -   **Action**: Create the necessary files and folders within the `c-custom-code` directory to house your new logic, views, routes, etc.
    -   **System Usage**:
        -   **Customization Protection**: This is the physical implementation of the protection system.

6.  **Step 50: Update Customization Manifest**

    -   **Description**: As you create new custom files, you must immediately map them in the `customization-manifest.json` file. This tells the system which vendor files your new code is intended to override or extend.
    -   **Action**: For each custom file, add an entry to the manifest. For example: `{"vendor_path": "app/Http/Controllers/ProductController.php", "custom_path": "c-custom-code/app/Http/Controllers/CustomProductController.php"}`.
    -   **System Usage**:
        -   **Customization Manifest**: This is a critical step. An out-of-date manifest makes future vendor updates extremely difficult.
        -   **Project Tracker**: Mark Steps 49 & 50 as "In Progress" and update them with commits as you work.

7.  **Step 51: Write Automated Tests**
    -   **Description**: For every new piece of logic, write corresponding automated tests (unit, feature, etc.). This proves the feature works as expected and protects it from future regressions.
    -   **Action**: Create test files within your testing framework that target the new code in the `c-custom-code` directory.

#### Phase 3: Verification & Documentation

**Objective**: To rigorously test the new feature and finalize its documentation in the Investment Tracker.

8.  **Step 52: Local Verification & QA**

    -   **Description**: Run the full suite of automated tests. Manually test the new feature according to the acceptance criteria defined in Step 45.
    -   **Action**: Execute tests and perform manual Quality Assurance. Fix any bugs found on the feature branch.
    -   **System Usage**:
        -   **Project Tracker**: Mark Step 52 as complete after all tests pass and QA is signed off.

9.  **Step 53: Finalize Investment Tracker Entry**
    -   **Description**: With the feature complete and tested, update its entry in the `Investment-Tracker.md`.
    -   **Action**: Update the entry with:
        -   **Actual Effort**: The total time spent on the feature.
        -   **Status**: `Completed`
        -   **Deployment Date**: The date it was merged.
        -   **Outcome/Value**: A brief note on the final result.
    -   **System Usage**:
        -   **Investment Tracker**: This completes the ROI loop for the feature, providing a permanent record of the value delivered for the effort invested.
        -   **Project Tracker**: Mark Step 53 as complete.

#### Phase 4: Deployment

**Objective**: To safely deploy the new feature to production.

10. **Step 54: Merge & Deploy to Staging**

    -   **Description**: Create a pull request to merge the feature branch into `develop`. Once approved and merged, deploy the `develop` branch to the staging environment for final UAT.
    -   **Action**: Use Git to manage the pull request and merge. Run the staging deployment script.
    -   **System Usage**:
        -   **Project Tracker**: Mark Step 54 as complete after UAT is approved on staging.

11. **Step 55: Merge & Deploy to Production**

    -   **Description**: Merge the `develop` branch into `main`. Deploy the `main` branch to the production server.
    -   **Action**: Execute the final merge and run the production deployment script.
    -   **System Usage**:
        -   **Project Tracker**: Mark Step 55 as complete.

12. **Step 56: Post-Deployment Verification**
    -   **Description**: Perform a final smoke test of the new feature on the live production site to ensure it is working correctly.
    -   **Action**: Verify functionality on the live site.
    -   **System Usage**:
        -   **Project Tracker**: Mark Step 56 as complete, concluding the customization workflow.
