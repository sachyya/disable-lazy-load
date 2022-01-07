<?php
use DisableLazyLoad\Core\Settings;

final class DisableLazyLoad {

	private $version = DLL_VERSION;

	public static $instance = null;

	public static function get_instance() {
		return ( is_null( self::$instance ) ) ? new self() : self::$instance;
	}

	public function __construct() {
		$this->autoload();

		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	public function autoload() {
		require_once( DLL_PATH . '/vendor/autoload.php' );
	}

	public function init() {
        // Get instance of Settings class
        Settings::get_instance();

        $disable_all = get_option('dll_settings');

        // Disable on all images if the setting for all images is checked
        if( ! empty( $disable_all['all_images'] ) ) {
            add_filter( 'wp_lazy_loading_enabled', '__return_false' );
        }

        // Disable on post thumbnails or images called by wp_get_attachment_image if the respective setting is checked.

        if( ! empty( $disable_all['post_thumbnails'] ) ) {
            add_filter( 'wp_lazy_loading_enabled', array( $this, 'disable_on_post_thumbnails' ), 10, 3 );
        }

        // Disable on specific images on the basis of meta box added
        add_filter( 'wp_img_tag_add_loading_attr', array( $this, 'disable_on_specific_img' ), 10, 3 );
        add_filter( 'wp_get_attachment_image_attributes', array( $this, 'disable_on_specific_post_thumbnails' ), 10, 3 );
 
	}

    public function disable_on_specific_post_thumbnails( $attr, $attachment, $size ) {
        $attachment_id = $attachment->ID;
        
        $image_url = wp_get_attachment_image_url( $attachment->ID );
        $dll_disable = get_post_meta( $attachment_id, 'dll_on_attachment', true );

        if( 'on' === $dll_disable ) {
            unset( $attr['loading'] );
        }
        
        return $attr;
        
    }
    
    public function disable_on_specific_img( $value, $image, $context ) {
        $args = array(
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'meta_query'  => array(
                array(
                    'key'     => 'dll_on_attachment',
                    'value'   => 'on',
                )
            )
        );

        $query = new WP_Query( $args );

        if( $query->have_posts() ) {
            
            while( $query->have_posts() ) {
                $query->the_post();

                $image_url = wp_get_attachment_url( get_the_ID() );

                if ( false !== strpos( $image, $image_url ) ) {
                    return false;
                }
            }
        }
            
        return $value;
    }
    
    public function disable_on_post_thumbnails( $default, $tag_name, $context ) {

        if ( 'img' === $tag_name && 'wp_get_attachment_image' === $context ) {
            return false;
        }

        return $default;
    }

}

DisableLazyLoad::get_instance();
