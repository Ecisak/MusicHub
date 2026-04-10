# MusicHub

MusicHub is a web application for sharing, reviewing, and discovering user-uploaded music. Built with PHP on an MVC architecture, this project demonstrates a complete workflow from user registration and content submission to multi-level administrative approval and real-time data visualization.

## Key Features

### User Roles & Permissions
The application features a robust, hierarchical role-based access control system:
*   **Guest:** Can browse accepted songs, read approved reviews, and view the leaderboard.
*   **Author:** Can upload music tracks and cover art. They can view the status of their submissions (pending, accepted, rejected) on their profile page.
*   **Reviewer:**
    *   Validates newly uploaded tracks for technical correctness.
    *   Writes detailed reviews (with scoring) for accepted songs.
    *   Can edit or delete their own reviews before they are approved by an admin.
*   **Admin:**
    *   Approves or rejects reviews written by reviewers.
    *   Manages user roles (promote, demote, ban), but cannot affect other Admins or Superadmins.
    *   Can permanently delete any song from the platform.
*   **Superadmin:** Has all Admin privileges and is the only role that can assign `admin` or `superadmin` roles.

### Music & Review Workflow
MusicHub uses a multi-stage approval process to ensure content quality:
1.  **Upload:** An `Author` uploads a track and cover art. The song is marked as `pending`.
2.  **Validation:** A `Reviewer` is assigned the a pending track and either accepts it, making it public, or rejects it.
3.  **Review Submission:** `Reviewers` can write detailed, formatted reviews for any accepted song (except their own). These reviews are submitted with a `pending` status.
4.  **Review Approval:** An `Admin` reviews the submitted text and ratings, and either `approves` or `rejects` them. Only approved reviews are visible on the song's page.

### Dynamic Content & APIs
*   **Explore Page:** Displays all accepted songs with dynamic sorting and filtering based on upload date, overall rating, quality, originality, and lyrics scores.
*   **Live Leaderboard:** A top-10 leaderboard page that updates in real-time using **Server-Sent Events (SSE)**. The frontend maintains a connection to a PHP API endpoint that pushes updates whenever the top songs change.

## Technology Stack & Security

### Architecture
*   **MVC (Model-View-Controller):** The codebase is organized into `models` for database interaction, `views` (Twig templates) for presentation, and `controllers` for handling application logic.
*   **Front Controller:** All web requests are routed through `public/index.php`, which directs traffic to the appropriate controller.

### Backend
*   **PHP 8.x**
*   **MySQL / MariaDB** with `PDO` for database abstraction.
*   **Composer** for managing server-side dependencies.
    *   `twig/twig`: A modern template engine for PHP.
    *   `ezyang/htmlpurifier`: For sanitizing user-generated HTML and preventing XSS attacks.

### Frontend
*   **Twig:** Used for all HTML templating, providing a clear separation of logic and presentation.
*   **Bootstrap 5:** Powers the entire UI, ensuring a responsive and modern design.
*   **JavaScript:**
    *   Used for real-time form validation on the registration page (checking username/email availability via AJAX).
    *   Handles the **Server-Sent Events (SSE)** connection for the live leaderboard.
*   **CKEditor 5:** A WYSIWYG editor for writing formatted reviews.

### Security
*   **SQL Injection Prevention:** All database queries use `PDO` prepared statements.
*   **Cross-Site Scripting (XSS) Prevention:** User input for reviews is sanitized with **HTML Purifier**, and Twig provides context-aware auto-escaping.
*   **Cross-Site Request Forgery (CSRF) Prevention:** Forms are protected using CSRF tokens managed in the user's session.
*   **Password Hashing:** User passwords are securely hashed using the `bcrypt` algorithm.
*   **File Upload Security:** Validates file types and sizes on both the client and server sides. Uploaded files are given unique names to prevent collisions and path traversal issues.
