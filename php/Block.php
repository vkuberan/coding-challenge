<?php
/**
 * Block class.
 *
 * @package SiteCounts
 */

namespace XWP\SiteCounts;

use WP_Block;

/**
 * The Site Counts dynamic block.
 *
 * Registers and renders the dynamic block.
 */
class Block {

	/**
	 * The Plugin instance.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Instantiates the class.
	 *
	 * @param Plugin $plugin The plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Adds the action to register the block.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'init', [ $this, 'register_block' ] );
	}

	/**
	 * Registers the block.
	 */
	public function register_block() {
		register_block_type_from_metadata(
			$this->plugin->dir(),
			[
				'render_callback' => [ $this, 'render_callback' ],
			]
		);
	}

	/**
	 * Renders the block.
	 *
	 * @param array    $attributes The attributes for the block.
	 * @param string   $content    The block content, if any.
	 * @param WP_Block $block      The instance of this block.
	 * @return string The markup of the block.
	 */
	public function render_callback( $attributes, $content, $block ) {
		global $post;
		$post_types = get_post_types( [ 'public' => true ] );
		$class_name = isset( $attributes['className'] ) ? $attributes['className'] : '';
		ob_start();

		?>
		<div class="<?php echo esc_attr( $class_name ); ?>">
			<h2><?php esc_html__( 'Post Counts', 'site-counts' ); ?></h2>
			<?php
			foreach ( $post_types as $post_type_slug ) :
				$post_type_object = get_post_type_object( $post_type_slug );
				$post_count       = count(
					get_posts(
						[
							'post_type'      => $post_type_slug,
							'posts_per_page' => -1,
						]
					)
				);

				if ( '' === $post_count ) {
					$post_count = 0;
				}

				?>
				<p>
					<?php
						echo sprintf(
							/* translators: %2$s is replaced with "int", %2$s is replaced with "string" */
							esc_html__( 'There are %1$d %2$s.', 'site-counts' ),
							esc_attr( $post_count ),
							esc_attr( $post_type_object->labels->name )
						);
					?>
				</p>
			<?php endforeach; ?>
			<p>
				<?php
				if ( isset( $post->ID ) ) {
					echo sprintf(
						/* translators: %1$s is replaced with "int"" */
						esc_html__( 'The current post ID is %1$d.', 'site-counts' ),
						esc_attr( $post->ID )
					);
				}
				?>
			</p>
		</div>
		<?php

		return ob_get_clean();
	}
}
