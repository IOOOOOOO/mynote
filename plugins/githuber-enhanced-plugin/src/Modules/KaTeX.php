<?php
/**
 * Module Name: KaTex
 * Module Description: Use KaTex markup for complex equations and other geekery.
 *
 * @author Terry Lin
 * @link https://terryl.in/
 *
 * @package Githuber
 * @since 1.1.0
 * @version 1.1.0
 *
 * Some code snippets are from Jetpack Module: Beautiful Math, we simply modify it for our needs.
 * @link https://github.com/Automattic/jetpack/blob/master/modules/latex.php
 * @license GPL
 */

namespace Githuber\Module;

class KaTex extends ModuleAbstract {

	public $katex_version = '0.10.0';

	/**
	 * Constructer.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Initialize.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'front_enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_enqueue_scripts' ) );
		add_action( 'wp_print_footer_scripts', array( $this, 'front_print_footer_scripts' ) );

		add_filter( 'the_content', array( $this, 'katex_markup' ), 9 );
		add_filter( 'comment_text', array( $this, 'katex_markup' ), 9 );
	}
 
	/**
	 * Register CSS style files for frontend use.
	 * 
	 * @return void
	 */
	public function front_enqueue_styles() {
		$option = githuber_get_option( 'prism_src', 'githuber_modules' );

		switch ( $option ) {
			case 'cloudflare':
				$style_url = 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/' . $this->katex_version . '/katex.min.css';
				break;

			case 'jsdelivr':
				$style_url = 'https://cdn.jsdelivr.net/npm/katex@' . $this->katex_version . '/dist/katex.min.css';
				break;

			default:
				$style_url = $this->githuber_plugin_url . 'assets/vendor/katex/katex.min.css';
				break;
		} 
		wp_enqueue_style( 'katex', $style_url, array(), $this->katex_version, 'all' );
	}

	/**
	 * Register JS files for frontend use.
	 * 
	 * @return void
	 */
	public function front_enqueue_scripts() {
		$option = githuber_get_option( 'katex_src', 'githuber_modules' );

		switch ( $option ) {
			case 'cloudflare':
				$script_url = 'https://cdnjs.cloudflare.com/ajax/libs/KaTeX/' . $this->katex_version . '/katex.min.js';
				break;

			case 'jsdelivr':
				$script_url = 'https://cdn.jsdelivr.net/npm/katex@' . $this->katex_version . '/dist/katex.min.js';
				break;

			default:
				$script_url = $this->githuber_plugin_url . 'assets/vendor/katex/katex.min.js';
				break;
		} 
		wp_enqueue_script( 'katex', $script_url, array(), $this->katex_version, true );
	}

	/**
	 * Katex Markup
	 * 
	 * Ex.
	 * $$ x_{1,2} = {-b\pm\sqrt{b^2 - 4ac} \over 2a}.$$
	 *
	 * @param string $content
	 * @return void
	 */
	public function katex_markup( $content ) {
		$textarr = wp_html_split( $content );

		$regex = '%
			\$\$*
			((?:
				[^$]+ # Not a dollar
			|
				(?<=(?<!\\\\)\\\\)\$ # Dollar preceded by exactly one slash
			)+)
			(?<!\\\\)\$*\$ # Dollar preceded by zero slashes
		%ix';

		foreach ( $textarr as &$element ) {
			if ( '' == $element || '<' === $element[0] ) {
				continue;
			}

			if ( false === stripos( $element, '$$' ) ) {
				continue;
			}

			$element = preg_replace_callback( $regex, array( $this, 'katex_src' ), $element );
		}

		return implode( '', $textarr );
	}

	/**
	 * Undocumented function
	 *
	 * @param array $matches Matched Regex array.
	 * @return string 
	 */
	public function katex_src( $matches ) {
		$katex = $matches[1];
		$katex = $this->katex_entity_decode( $katex );
		return '<p class="katex-container">' . $katex . '</p>';
	}

	/**
	 * Decode HTML symbols.
	 *
	 * @param string $katex KaTeX string.
	 * @return void
	 */
	public function katex_entity_decode( $katex ) {
		return str_replace( array( '&lt;', '&gt;', '&quot;', '&#039;', '&#038;', '&amp;', "\n", "\r" ), array( '<', '>', '"', "'", '&', '&', ' ', ' ' ), $katex );
	}
	
	/**
	 * Print Javascript plaintext in page footer.
	 */
	public function front_print_footer_scripts() {
		$script = '
			<script>
				(function($) {
					$(function() {
						$(".katex-container").each(function() {
							var katexText = $(this).text();
							var el = $( this ).get( 0 );
							if ($(this).parent("code").length == 0) {
								try {
									katex.render(katexText, el)
								} catch (err) {
									$(this).html("<span class=\'err\'>" + err)
								}
							}
						});
					});
				})(jQuery);
			</script>
		';
		echo $script;
	}
}