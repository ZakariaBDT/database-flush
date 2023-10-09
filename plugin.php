<?php
/**
 * Plugin Name:       A Database Flush Plugin
 * Description:       Example block written with ESNext standard and JSX support – build step required.
 * Requires at least: 5.7
 * Requires PHP:      7.0
 * Version:           1.0.0
 * Author:            Zakaria Binsaifullah
 * Author URI:        https://makegutenblock.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       boilerplate
 *
 * @package           @wordpress/create-block 
 */

 /**
  * @package Zero Configuration with @wordpress/create-block
  *  [boilerplate] && [BOILERPLATE] ===> Prefix
  */

// Stop Direct Access 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blocks Final Class
 */

final class BOILERPLATE_BLOCKS_CLASS {
	public function __construct() {

		// define constants
		$this->define_constants();

		// block initialization
		add_action( 'init', [ $this, 'blocks_init' ] );

		// blocks category
		if( version_compare( $GLOBALS['wp_version'], '5.7', '<' ) ) {
			add_filter( 'block_categories', [ $this, 'register_block_category' ], 10, 2 );
		} else {
			add_filter( 'block_categories_all', [ $this, 'register_block_category' ], 10, 2 );
		}

		// register blocks style
		add_filter( 'render_block', [ $this, 'generate_inline_style_on_render_block' ], 10, 2 );

		// admin page
		add_action( 'admin_menu', [ $this, 'admin_page' ] );

		// enqueue admin scripts
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

	}

	/**
	 * Enqueue Admin Scripts
	 */
	public function enqueue_admin_scripts() {
		wp_enqueue_script( 'boilerplate-sweet-alert', 'https://cdn.jsdelivr.net/npm/sweetalert2@11', array(), BOILERPLATE_VERSION, false );
	}
	/**
	 * Admin Page
	 */
	public function admin_page() {
		add_menu_page(
			'Boilerplate Blocks',
			'Boilerplate Blocks',
			'manage_options',
			'boilerplate-blocks',
			[ $this, 'admin_page_callback' ],
			'dashicons-layout',
			20
		);
	} 

	/**
	 * Admin Page Callback
	 */
	function admin_page_callback() {
		?>
		<div class="wrap">
			<h2>Your Plugin Settings</h2>
			<form method="post" action="">
				<p>Click the button below to update all post contents:</p>
				<input type="submit" name="update_posts" class="button button-primary" value="Flush Database">
			</form>
		</div>
		<?php
			$total_updated_posts = 0; 
			$postTypes = get_post_types();
			$excludeTypes = array( 'attachment', 'revision', 'nav_menu_item', 'custom_css', 'customize_changeset', 'oembed_cache', 'user_request', 'wp_block', 'wp_template', 'wp_template_part', 'wp_global_styles', 'wp_navigation' );
			// exclude default post types except post and page
			$finalPostTypes = array_diff( $postTypes, $excludeTypes );
		
			// Handle the button click
			if (isset($_POST['update_posts'])) {
				foreach ($finalPostTypes as $postType) {
					$total_updated_posts += $this->update_posts_with_heading_block($postType);
					$this->update_posts_with_heading_block( $postType );
				}

							// Display the success message only once if any posts were updated
				if ($total_updated_posts > 0) {

					?>
					<script>
						Swal.fire({
							title: 'Success!',
							text: 'Total <?php echo $total_updated_posts; ?> posts were updated where our blocks were used.',
							icon: 'success',
							confirmButtonText: 'Okay, Got it!'
						});
					</script>
					<?php 
				}
			}
		?>
		<?php
	}

	function update_posts_with_heading_block($post_type) {
		$args = array(
			'post_type' => $post_type,
			'posts_per_page' => -1, // Get all posts of this post type
		);
	
		$query = new WP_Query($args);
	
		$updated_posts = 0; // Track the number of updated posts
	
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				
				// Get the post content
				$post_content = get_the_content();
	
				// Check if the post content contains a heading block (adjust the block name as needed)
				if (has_block( 'core/heading', $post_content )) {

					// Update the post content
					$post_data = array(
						'ID' => get_the_ID(),
						'post_content' => $post_content,
					);
	
					wp_update_post($post_data);
	
					$updated_posts++; // Increment the count of updated posts
				}
			}
			wp_reset_postdata();
		}
	
		return $updated_posts;
	}


	/**
	 * Initialize the plugin
	 */
	public static function init(){
		static $instance = false; 
		if( ! $instance ) {
			$instance = new self();
		}
		return $instance;
	}

	/**
	 * Define the plugin constants
	 */
	private function define_constants() {
		define( 'BOILERPLATE_VERSION', '1.0.0' );
		define( 'BOILERPLATE_URL', plugin_dir_url( __FILE__ ) );	
	}

	/**
	 * Register Block Category
	 */
	public function register_block_category( $categories, $post ) {
		return array_merge(
			array(
				array(
					'slug'  => 'boilerplate',
					'title' => __( 'Boilerplate', 'boilerplate' ),
				),
			),
			$categories,
		);
	}

	/**
	 * Blocks Registration 
	 */
	public function register_block( $name, $options = array() ) {
		register_block_type( __DIR__ . '/build/blocks/' . $name, $options );
	 }

	/**
	 * Blocks Initialization
	*/
	public function blocks_init() {
		$blocksList = [
			'test',
		];
		
		// register blocks
		if( ! empty( $blocksList ) ) {
			foreach( $blocksList as $block ) {
				$this->register_block( $block );
			}
		}
	}

	/**
     * Register Inline Style
     */
    function generate_inline_style_on_render_block($block_content, $block ) {

        if (isset($block['blockName']) && str_contains($block['blockName'], 'boilerplate/')) {
            if (isset($block['attrs']['blockStyle'])) {

                $style = $block['attrs']['blockStyle'];
                $handle = isset( $block['attrs']['uniqueId'] ) ? $block['attrs']['uniqueId'] : 'boilerplate-blocks';

                // convert style array to string
                if ( is_array($style) ) {
                    $style = implode(' ', $style);
                }

                // minify style to remove extra space
                $style = preg_replace( '/\s+/', ' ', $style );

                wp_register_style(
                    $handle,
                    false
                );
                wp_enqueue_style( $handle );
                wp_add_inline_style( $handle, $style );

            }
        }
        return $block_content;
    }

}

/**
 * Kickoff
*/

BOILERPLATE_BLOCKS_CLASS::init();
