<?php
/**
 * @package corrupted_bytes
 */
/*
Plugin Name: CorruptedBytes addons
Description: [github_embed] [toggler]
Version: 0.1b
 */
namespace corruptedbytes;

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*** plugin meta funcs ***/
function enqueue_scripts() {
	$query_rand = is_user_logged_in() ? 'rand=' . rand() : '';
	//wp_enqueue_style('highlightjs-style', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/default.min.css');
	//wp_enqueue_style('highlightjs-monokai-style', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/monokai-sublime.min.css');
	//wp_enqueue_script('highlightjs-script', '//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js');

	wp_enqueue_style('corruptedbytes-style', plugin_dir_url(__FILE__).'assets/corrupted_bytes.css?'.$query_rand);
	wp_enqueue_script('corruptedbytes-script', plugin_dir_url(__FILE__).'assets/corrupted_bytes.js?'.$query_rand);

	wp_enqueue_style('prism-style', plugin_dir_url(__FILE__).'assets/prism.css?'.$query_rand);
	wp_enqueue_script('prism-script', plugin_dir_url(__FILE__).'assets/prism.js?'.$query_rand);
}
add_action('wp_enqueue_scripts', __NAMESPACE__.'\enqueue_scripts');

/*** shortcode handlers ***/
function github_embed( $atts, $content, $tag ) {
	$atts = shortcode_atts([
		'language' => '',
		'autodetect' => TRUE,
		'src' => '',
	], $atts);
	
	if ($atts['autodetect'])
		$language = pathinfo($atts['src'], PATHINFO_EXTENSION);
	else
		$langauge = $atts['language'];

	return "
		<pre class='line-numbers'>
			<code class='highlight github-embed language-{$language}' src='{$atts['src']}'>
				<noscript>{$atts['code']}</noscript>
			</code>
		</pre>
		<div class='code-source'>src: <a href='{$atts['src']}'>{$atts['src']}</a></div>";
}
add_shortcode('github_embed', __NAMESPACE__.'\github_embed');

function toggler_shortcode( $atts, $content, $tag ) {
	$atts = shortcode_atts([
		'type'  => 'toggler',
		'title' => 'toggle',
		'state' => 'closed',
	], $atts);

	$type_class = $atts['type'] == 'revealer' ? 'revealer' : 'toggler';

	return "<div class='$type_class {$atts['state']}'>
				<div class='banner'>{$atts['title']}</div>
				<div class='content'>{$content}</div>
			</div>";
}
add_shortcode('toggler', __NAMESPACE__.'\toggler_shortcode');
?>
