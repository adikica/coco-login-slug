<?php
declare(strict_types=1);
/**
 * Plugin Name: Coco Login Slug
 * Description: Securely change the WordPress login URL to a custom slug and redirect logout to the custom slug.
 * Version: 1.0.1
 * Author: Your Name
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined( 'COCO_LOGIN_SLUG_DEFAULT' ) ) {
    define( 'COCO_LOGIN_SLUG_DEFAULT', 'coco-login' );
}

/**
 * Class Coco_Login_Slug_Plugin
 *
 * Provides functionality for a custom login slug and proper logout redirection.
 */
class Coco_Login_Slug_Plugin {

    /**
     * Constructor.
     */
    public function __construct() {
        // Admin settings and form handling.
        add_action( 'admin_menu', [ $this, 'add_settings_menu' ] );
        add_action( 'admin_init', [ $this, 'handle_settings_submission' ] );

        // Rewrite rules and security measures.
        add_action( 'init', [ $this, 'add_rewrite_rules' ] );
        add_action( 'init', [ $this, 'block_wp_login' ] );

        // Login and logout handling.
        add_filter( 'login_redirect', [ $this, 'redirect_after_login' ], 10, 3 );
        add_action( 'login_form', [ $this, 'force_admin_redirect' ] );
        add_filter( 'logout_url', [ $this, 'filter_logout_url' ], 10, 2 );
    }

    /**
     * Adds a settings page under "Settings" in the WordPress admin.
     */
    public function add_settings_menu(): void {
        add_options_page(
            __( 'Coco Login Slug', 'coco-login-slug' ),
            __( 'Login Slug', 'coco-login-slug' ),
            'manage_options',
            'coco-login-slug',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Processes the settings form submission.
     */
    public function handle_settings_submission(): void {
        if ( isset( $_POST['cls_update_slug'] ) && check_admin_referer( 'cls_update_slug' ) ) {
            if ( ! empty( $_POST['cls_login_slug'] ) ) {
                $new_slug = sanitize_title( wp_unslash( $_POST['cls_login_slug'] ) );
                update_option( 'cls_login_slug', $new_slug );
                flush_rewrite_rules();
                add_settings_error( 'cls_messages', 'cls_message', __( 'Login slug updated successfully.', 'coco-login-slug' ), 'updated' );
            }
        }
    }

    /**
     * Renders the plugin settings page.
     */
    public function render_settings_page(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'You do not have sufficient permissions to access this page.', 'coco-login-slug' ) );
        }

        $current_slug = get_option( 'cls_login_slug', COCO_LOGIN_SLUG_DEFAULT );
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Coco Login Slug', 'coco-login-slug' ); ?></h1>
            <?php settings_errors( 'cls_messages' ); ?>
            <form method="post">
                <?php wp_nonce_field( 'cls_update_slug' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="cls_login_slug"><?php esc_html_e( 'New Login Slug:', 'coco-login-slug' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="cls_login_slug" name="cls_login_slug" value="<?php echo esc_attr( $current_slug ); ?>" class="regular-text" required>
                            <p class="description">
                                <?php
                                printf(
                                    __( 'Example: if you enter "my-login", the new login URL will be %s', 'coco-login-slug' ),
                                    '<code>' . esc_url( home_url( '/' . $current_slug . '/' ) ) . '</code>'
                                );
                                ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <input type="hidden" name="cls_update_slug" value="1">
                <?php submit_button( __( 'Save Changes', 'coco-login-slug' ) ); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Adds a custom rewrite rule for the login slug.
     */
    public function add_rewrite_rules(): void {
        $login_slug = get_option( 'cls_login_slug', COCO_LOGIN_SLUG_DEFAULT );
        if ( ! empty( $login_slug ) ) {
            add_rewrite_rule( '^' . preg_quote( $login_slug, '/' ) . '/?$', 'wp-login.php', 'top' );
        }
    }

    /**
     * Blocks direct access to wp-login.php unless a valid login attempt is detected.
     */
    public function block_wp_login(): void {
        if ( strpos( $_SERVER['REQUEST_URI'], 'wp-login.php' ) !== false && ! is_admin() && ! isset( $_POST['log'] ) ) {
            status_header( 404 );
            nocache_headers();

            $theme_404 = get_template_directory() . '/404.php';
            if ( file_exists( $theme_404 ) ) {
                include $theme_404;
            } else {
                echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>404 Not Found</title></head><body><h1>404 - Page Not Found</h1><p>The page you are looking for does not exist.</p></body></html>';
            }
            exit;
        }
    }

    /**
     * Redirects users to the admin dashboard after successful login.
     *
     * @param string           $redirect_to The original redirect URL.
     * @param string           $request     The requested redirect URL.
     * @param WP_User|WP_Error $user        The logged-in user object.
     *
     * @return string The URL to redirect to.
     */
    public function redirect_after_login( string $redirect_to, string $request, $user ): string {
        if ( isset( $user->roles ) && is_array( $user->roles ) ) {
            return admin_url();
        }
        return $redirect_to;
    }

    /**
     * Forces a redirect to the admin dashboard if login is attempted via wp-login.php.
     */
    public function force_admin_redirect(): void {
        if ( basename( $_SERVER['REQUEST_URI'] ) === 'wp-login.php' && isset( $_POST['log'] ) ) {
            wp_redirect( admin_url() );
            exit;
        }
    }

    /**
     * Filters the logout URL to enforce a redirect to the custom login slug.
     *
     * @param string $logout_url The original logout URL.
     * @param string $redirect   The provided redirect parameter (if any).
     *
     * @return string The modified logout URL.
     */
    public function filter_logout_url( string $logout_url, string $redirect ): string {
        $login_slug      = get_option( 'cls_login_slug', COCO_LOGIN_SLUG_DEFAULT );
        $custom_login_url = home_url( '/' . $login_slug . '/' );
        // Build a logout URL using the custom slug.
        $new_logout_url  = wp_nonce_url( home_url( '/' . $login_slug . '/?action=logout' ), 'log-out' );
        // Force the redirect_to parameter to always point to the custom login URL.
        $new_logout_url  = add_query_arg( 'redirect_to', urlencode( $custom_login_url ), $new_logout_url );
        return $new_logout_url;
    }

    /**
     * Plugin activation hook.
     */
    public static function activate(): void {
        if ( false === get_option( 'cls_login_slug' ) ) {
            update_option( 'cls_login_slug', COCO_LOGIN_SLUG_DEFAULT );
        }
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation hook.
     */
    public static function deactivate(): void {
        flush_rewrite_rules();
    }
}

/**
 * Initialize the Coco Login Slug plugin.
 */
function coco_login_slug_plugin_init(): void {
    new Coco_Login_Slug_Plugin();
}
add_action( 'plugins_loaded', 'coco_login_slug_plugin_init' );

register_activation_hook( __FILE__, [ 'Coco_Login_Slug_Plugin', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'Coco_Login_Slug_Plugin', 'deactivate' ] );
