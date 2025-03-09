<?php
/**
 * Plugin Name: Coco Login Slug
 * Description: Securely change the WordPress login URL to a custom slug.
 * Version: 1.6
 * Author: Your Name
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Add settings submenu
function cls_add_settings_menu(): void {
    add_options_page(
        __('Coco Login Slug', 'coco-login-slug'),
        __('Login Slug', 'coco-login-slug'),
        'manage_options',
        'coco-login-slug',
        'cls_settings_page'
    );
}
add_action('admin_menu', 'cls_add_settings_menu');

// Settings page
function cls_settings_page(): void {
    if (!current_user_can('manage_options')) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('cls_update_slug')) {
        if (!empty($_POST['cls_login_slug'])) {
            $new_slug = sanitize_title($_POST['cls_login_slug']);
            update_option('cls_login_slug', $new_slug);
            flush_rewrite_rules(); // Flush rewrite rules after updating slug
            echo '<div class="updated notice is-dismissible"><p>' . __('Login slug updated successfully.', 'coco-login-slug') . '</p></div>';
        }
    }

    $current_slug = get_option('cls_login_slug', 'coco-login');
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Coco Login Slug', 'coco-login-slug'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('cls_update_slug'); ?>
            <label for="cls_login_slug"><?php esc_html_e('New Login Slug:', 'coco-login-slug'); ?></label>
            <input type="text" id="cls_login_slug" name="cls_login_slug" value="<?php echo esc_attr($current_slug); ?>" required>
            <p class="description">
                <?php printf(
                    __('Example: if you enter "my-login", the new login URL will be <strong>%s</strong>', 'coco-login-slug'),
                    esc_url(home_url('/' . $current_slug . '/'))
                ); ?>
            </p>
            <br>
            <input type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes', 'coco-login-slug'); ?>">
        </form>
    </div>
    <?php
}

// Rewrite rules for custom login slug
function cls_rewrite_rules(): void {
    $login_slug = get_option('cls_login_slug', 'coco-login');
    if (!empty($login_slug)) {
        add_rewrite_rule('^' . preg_quote($login_slug, '/') . '/?$', 'wp-login.php', 'top');
    }
}
add_action('init', 'cls_rewrite_rules');

// Block direct access to wp-login.php and return 404 error
function cls_block_wp_login_404(): void {
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false && !is_admin() && !isset($_POST['log'])) {
        status_header(404);
        nocache_headers();
        
        // Check if 404.php exists, otherwise create a default one
        $theme_404 = get_template_directory() . '/404.php';
        if (!file_exists($theme_404)) {
            echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">';
            echo '<title>404 Not Found</title></head><body><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p></body></html>';
            exit;
        }
        
        include $theme_404; // Load the theme's 404 template if available
        exit;
    }
}
add_action('init', 'cls_block_wp_login_404');

// Redirect after successful login to /wp-admin/
function cls_redirect_after_login($redirect_to, $request, $user): string {
    if (isset($user->roles) && is_array($user->roles)) {
        return admin_url(); // Redirect all users to the dashboard
    }
    return $redirect_to;
}
add_filter('login_redirect', 'cls_redirect_after_login', 10, 3);

// Ensure users are redirected to /wp-admin/ after logging in through the custom slug
function cls_force_admin_redirect(): void {
    if (basename($_SERVER['REQUEST_URI']) === 'wp-login.php' && isset($_POST['log'])) {
        wp_redirect(admin_url());
        exit;
    }
}
add_action('login_form', 'cls_force_admin_redirect');
