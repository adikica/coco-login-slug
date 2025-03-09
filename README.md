Coco Login Slug
Description
Coco Login Slug is a secure and lightweight WordPress plugin that lets you change the default login URL (/wp-login.php) to a custom secret slug of your choice. It enhances security by blocking direct access to the default login endpoint and redirects users to the custom slug after logout.

Features
Custom Login URL: Replace the default /wp-login.php with a custom slug (e.g., /my-secret-login).
Secure Logout Redirection: Users are automatically redirected to your custom login URL after logout.
Access Protection: Blocks unauthorized access to /wp-login.php, reducing potential brute-force attack vectors.
Easy Administration: Configure the custom slug directly from the WordPress admin dashboard.
Modern PHP & Best Practices: Uses strict typing, an object-oriented structure, and adheres to modern PHP and WordPress coding standards.
Automatic Rewrite Rules: Flushes and updates rewrite rules on activation and whenever the login slug is changed.
Installation
Download and Upload:

Download the plugin files.
Upload the coco-login-slug folder to your /wp-content/plugins/ directory.
Activate the Plugin:

Log in to your WordPress admin dashboard.
Navigate to Plugins > Installed Plugins.
Locate Coco Login Slug and click Activate.
Configure the Plugin:

Go to Settings > Login Slug.
Enter your desired custom login slug (e.g., my-secret-login).
Click Save Changes.
The plugin will automatically flush the rewrite rules to apply your changes.
Usage
Logging In:

Access your new login URL, e.g., https://yourdomain.com/my-secret-login/.
Logging Out:

After logout, users are redirected to the custom login URL instead of /wp-login.php?loggedout=true.
Security:

Direct requests to /wp-login.php (that are not valid login attempts) return a 404 error.
Frequently Asked Questions (FAQ)
Q: How do I revert to the default login URL?
A: Update the login slug to wp-login (or your preferred value) in the settings page and save your changes. The rewrite rules will be flushed automatically.

Q: Does this plugin affect user credentials or roles?
A: No, it only changes the URL used for login and logout processes. User data remains unchanged.

Q: Will all users be affected by this change?
A: Yes, the new login URL is applied site-wide for all user roles.

Changelog
Version 1.0.1
Implemented strict typing and modern PHP practices.
Improved logout redirection to enforce the custom login slug.
Refactored code using an object-oriented approach for better maintainability.
Enhanced security measures and admin settings interface.
Contributing
Contributions are welcome! If you'd like to contribute:

Fork the repository.
Create a new branch for your feature or bugfix.
Submit a pull request with a detailed description of your changes.
For major changes, please open an issue first to discuss what you would like to change.

License
This plugin is licensed under the MIT License.

Author
Adi Kica
