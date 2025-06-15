StudentHub is a platform designed to connect students and professors around academic projects. The idea is to:

Let students create projects they are working on or planning,

Allow them to assign professors to supervise or support these projects,

And give professors the ability to add tasks and track progress within the assigned projects.

🧱 Main Features
User Registration & Login System for both students and professors.

Role-Based Access: Professors and students see different interfaces and have different permissions.

Project Management:

Students can create new projects,

They assign professors to projects during creation.

Task Management:

Professors can only add tasks to projects they’re assigned to,

Each task includes a name, description, priority, due date, and auto-assignment to the student who owns the project.

Email Notifications using PHPMailer when tasks are assigned.

Validation and Access Protection to ensure project ownership and data integrity.

🖥️ Technical Stack
PHP as the backend scripting language,

MySQL for database operations,

HTML, CSS, Bootstrap for the frontend UI,

PHPMailer for sending task assignment emails.

🔐 Security
Passwords are hashed using password_hash() and verified securely,

Session and cookie handling ensures proper user authentication and redirection.

🏠 Homepage Flow
Users are greeted with a homepage where they are encouraged to Login or Sign Up.

Once logged in, they are redirected to a personalized dashboard where they can manage tasks and projects based on their role.

📁 Project Structure
The project is organized into:

MVC-like folders for controllers, models, and views,

A vendor directory with external libraries like PHPMailer,

A send_email.php utility for email integration,

Centralized routing via index.php.

✅ Conclusion
StudentHub is designed with scalability and role clarity in mind. It simplifies the process of managing academic work between students and professors, while maintaining proper access control and automation.