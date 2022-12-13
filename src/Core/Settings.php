<?php

namespace DisableLazyLoad\Core;

class Settings {

	public static $instance = null;

	public static function get_instance() {
		return ( is_null( self::$instance ) ) ? new self() : self::$instance;
	}

	public function __construct() {
        // Register settings on admin_init hook
		add_action( 'admin_init', array( $this, 'register_settings' ) );

        add_filter( 'attachment_fields_to_edit', array( $this, 'add_attachment_meta' ), 10, 2 );
        
        add_filter( 'attachment_fields_to_save', array( $this, 'save_attachment_meta' ), 10, 2);        
	}

    function add_attachment_meta( $form_fields, $post ) {
        $field_value = (bool)get_post_meta( $post->ID, 'dll_on_attachment', true );
        $form_fields['dll_on_attachment'] = array(
            'value' => $field_value ? $field_value : '',
            'input' => 'html',
            'html' => '<label><input style="margin-top:0" ' . ( $field_value ? ' checked="checked"' : '' ) . ' type="checkbox" name="attachments[' . $post->ID . '][dll_on_attachment]" id="attachments-' . $post->ID . '-dll_on_attachment" /> <strong>' . __( "Disable lazy loading", 'disable-lazy-load' ) .'</strong></label>',
            'label' => '',
            'required' => false,
        );
        
        return $form_fields;
    }

    // Save custom checkbox attachment field
    function save_attachment_meta( $post, $attachment ) {

        if( isset( $attachment['dll_on_attachment'] ) ){  
            update_post_meta( $post['ID'], 'dll_on_attachment', sanitize_text_field( $attachment['dll_on_attachment'] ) );  
        } else {
            delete_post_meta( $post['ID'], 'dll_on_attachment' );
        }
        
        return $post;
    }


  	public function register_settings(){

        // Register a new setting for "media" page
        register_setting( 'media', 'dll_settings' );

        // Register a new section in the "media" page
        add_settings_section(
            'dll_settings_section',
            __( 'Lazy loading Settings', 'disable-lazy-load' ),
            array( $this, 'dll_settings_section_cb'),
            'media'
        );

        // Register a new field in the "dll_settings_section" section
        add_settings_field(
            'dll_settings',
            __( 'Lazy loading on images', 'disable-lazy-load' ),
            array( $this, 'dll_settings_cb'),
            'media',
            'dll_settings_section',
            [
                'label_for' => 'dll_disable_all',
                'class' => 'dll_disable_all'
            ]
        );

	}

    public function dll_settings_section_cb() {
        echo '<p>' . __( 'Disable the default lazy loading feature in differnt images of your site.', 'disable-lazy-load' ) . '</p>';
    }
     
    public function dll_settings_cb() {
        // Get the value of the setting we've registered with register_setting()
        $dll_settings = get_option('dll_settings');

        $all_images = ( isset( $dll_settings['all_images'] ) ) ? $dll_settings['all_images'] : '';

        $post_thumbnails = ( isset( $dll_settings['post_thumbnails'] ) ) ? $dll_settings['post_thumbnails'] : '';
        
        // Output setting for all site disable field
        ?>
        <fieldset>     
            <label>
              <input name="dll_settings[all_images]" class="dll_settings" type="checkbox" value="1" <?php checked( 1, $all_images ); ?>>

              <?php _e( 'Disable on all images ( except post thumbnails ).', 'disable-lazy-load' ); ?>
            </label>
        </fieldset>

        <br />

        <fieldset>     
            <label>
                <input name="dll_settings[post_thumbnails]" class="dll_post_thumbnails_settings" type="checkbox" value="1" <?php checked( 1, $post_thumbnails ); ?>>
                <?php _e( 'Disable only on post thumbnails.', 'disable-lazy-load' ); ?>
            </label>
        </fieldset>

<?php

    }

}
