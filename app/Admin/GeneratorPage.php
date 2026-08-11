<?php
namespace Maker\MakerMaker\Admin;

use Maker\MakerMaker\Generator\GeneratorException;
use Maker\MakerMaker\Generator\GeneratorFactory;
use Maker\MakerMaker\Generator\PluginDefinition;
use Throwable;

final class GeneratorPage
{
    private const PAGE = 'makermaker';
    private const ACTION = 'makermaker_create_plugin';

    public function __construct( private readonly GeneratorFactory $factory )
    {
    }

    public function register(): void
    {
        add_action( 'admin_menu', [ $this, 'menu' ] );
        add_action( 'admin_post_' . self::ACTION, [ $this, 'create' ] );
    }

    public function menu(): void
    {
        add_management_page( 'MakerMaker', 'MakerMaker', 'install_plugins', self::PAGE, [ $this, 'render' ] );
    }

    public function render(): void
    {
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_die( esc_html__( 'You are not allowed to generate plugins.', 'makermaker' ) );
        }

        $notice = get_transient( $this->noticeKey() );
        delete_transient( $this->noticeKey() );
        $dependency = null;
        try {
            $this->factory->create();
        } catch ( Throwable $error ) {
            $dependency = $error->getMessage();
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__( 'MakerMaker', 'makermaker' ); ?></h1>
            <p><?php echo esc_html__( 'Generate a structured plugin from the official template in your installed TypeRocket Pro v6 package.', 'makermaker' ); ?></p>
            <?php if ( is_array( $notice ) ) : ?>
                <div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible"><p><?php echo esc_html( $notice['message'] ); ?></p></div>
            <?php endif; ?>
            <?php if ( $dependency !== null ) : ?>
                <div class="notice notice-error"><p><?php echo esc_html( $dependency ); ?></p></div>
            <?php else : ?>
                <div class="notice notice-success inline"><p><?php echo esc_html__( 'TypeRocket Professional template detected.', 'makermaker' ); ?></p></div>
            <?php endif; ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="<?php echo esc_attr( self::ACTION ); ?>">
                <?php wp_nonce_field( self::ACTION ); ?>
                <table class="form-table" role="presentation">
                    <tr><th scope="row"><label for="makermaker-name">Plugin name</label></th><td><input required class="regular-text" id="makermaker-name" name="name" value="Example Plugin"></td></tr>
                    <tr><th scope="row"><label for="makermaker-slug">Plugin slug</label></th><td><input required class="regular-text" id="makermaker-slug" name="slug" value="example-plugin" pattern="[a-z][a-z0-9-]*"></td></tr>
                    <tr><th scope="row"><label for="makermaker-namespace">PHP namespace</label></th><td><input required class="regular-text" id="makermaker-namespace" name="namespace" value="Maker\\ExamplePlugin"></td></tr>
                    <tr><th scope="row"><label for="makermaker-vendor">Composer vendor</label></th><td><input required class="regular-text" id="makermaker-vendor" name="vendor" value="maker"></td></tr>
                    <tr><th scope="row">Activation</th><td><label><input type="checkbox" name="activate" value="1"> Activate after successful generation</label></td></tr>
                </table>
                <?php submit_button( 'Generate plugin', 'primary', 'submit', true, [ 'disabled' => $dependency !== null ] ); ?>
            </form>
        </div>
        <?php
    }

    public function create(): void
    {
        if ( ! current_user_can( 'install_plugins' ) ) {
            wp_die( esc_html__( 'You are not allowed to generate plugins.', 'makermaker' ), 403 );
        }
        check_admin_referer( self::ACTION );

        try {
            $definition = new PluginDefinition(
                sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ),
                sanitize_text_field( wp_unslash( $_POST['slug'] ?? '' ) ),
                sanitize_text_field( wp_unslash( $_POST['namespace'] ?? '' ) ),
                sanitize_text_field( wp_unslash( $_POST['vendor'] ?? 'maker' ) )
            );
            $result = $this->factory->create()->generate( $definition );

            if ( isset( $_POST['activate'] ) ) {
                if ( ! function_exists( 'activate_plugin' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/plugin.php';
                }
                $activation = activate_plugin( $definition->slug . '/' . basename( $result->entryFile ) );
                if ( is_wp_error( $activation ) ) {
                    throw new GeneratorException( 'Plugin generated but activation failed: ' . $activation->get_error_message() );
                }
            }
            $this->notice( 'success', 'Plugin “' . $definition->name . '” generated successfully.' );
        } catch ( Throwable $error ) {
            $this->notice( 'error', $error->getMessage() );
        }

        wp_safe_redirect( admin_url( 'tools.php?page=' . self::PAGE ) );
        exit;
    }

    private function notice( string $type, string $message ): void
    {
        set_transient( $this->noticeKey(), [ 'type' => $type, 'message' => $message ], 60 );
    }

    private function noticeKey(): string
    {
        return 'makermaker_notice_' . get_current_user_id();
    }
}
