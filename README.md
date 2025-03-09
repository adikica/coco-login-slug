# Coco Login Slug

## Description

**Coco Login Slug** is a secure and lightweight WordPress plugin that allows you to change the default login URL (`/wp-login.php`) to a custom secret slug of your choice. This helps protect your site against brute-force attacks and automated login attempts by hiding the standard login endpoint.

In addition, the plugin ensures that after logout, users are redirected to the custom login slug instead of the default `/wp-login.php?loggedout=true`.

## Features

- **Custom Login URL:** Replace the default `/wp-login.php` with a custom slug (e.g., `/my-secret-login`).
- **Secure Logout Redirection:** Users are redirected to your custom login URL upon logout.
- **Access Protection:** Blocks unauthorized access to `/wp-login.php` by returning a 404 error for non-login requests.
- **Admin Settings Page:** Easily configure the custom login slug via the WordPress admin dashboard.
- **Modern PHP Practices:** Utilizes strict typing, an object-oriented structure, and adheres to modern WordPress coding standards.
- **Automatic Rewrite Rules:** Flushes and updates rewrite rules upon activation and when the login slug is changed.

## Installation

1. **Download and Upload:**
   - Download or clone the repository.
   - Upload the `coco-login-slug` folder to your `/wp-content/plugins/` directory.

2. **Activate the Plugin:**
   - Log in to your WordPress admin dashboard.
   - Navigate to **Plugins** > **Installed Plugins**.
   - Locate **Coco Login Slug** and click **Activate**.

3. **Configure the Plugin:**
   - Go to **Settings** > **Login Slug**.
   - Enter your desired custom login slug (e.g., `my-secret-login`).
   - Click **Save Changes**.
   - The plugin will automatically flush the rewrite rules to apply your changes.

## Usage

- **Logging In:**
  - Access your new login URL (e.g., `https://yourdomain.com/my-secret-login/`).

- **Logging Out:**
  - After logout, users are redirected to the custom login URL rather than the default `/wp-login.php?loggedout=true`.

- **Security:**
  - Direct requests to `/wp-login.php` (unless a valid login attempt) will return a 404 error, thereby protecting your site.

## FAQ

**Q: How do I revert to the default login URL?**  
A: Update the login slug to `wp-login` (or another preferred value) on the settings page and save your changes. The plugin will flush the rewrite rules automatically.

**Q: Will changing the login slug affect user credentials or roles?**  
A: No, the plugin only alters the login and logout URLs. User credentials and roles remain unaffected.

**Q: Is the plugin compatible with all WordPress themes?**  
A: The plugin is designed to work with most themes. However, if your theme has a custom 404 template, it will be used when blocking unauthorized access to `/wp-login.php`.

## Changelog

### Version 1.0.1
- Implemented strict typing and modern PHP practices.
- Improved logout redirection to always use the custom login slug.
- Refactored code into an object-oriented structure for better maintainability.
- Enhanced security measures and improved the admin settings interface.

## Contributing

Contributions are welcome! To contribute:

1. Fork the repository.
2. Create a new branch for your feature or bugfix.
3. Submit a pull request with a detailed description of your changes.

For major changes, please open an issue first to discuss what you would like to change.

## License

This project is licensed under the [MIT License](LICENSE).

## Author

**Adi Kica**  

