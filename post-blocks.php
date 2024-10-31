<?php
/*
Plugin Name: Post Blocks
Plugin URI: http://wordpress.org/extend/plugins/post-blocks/
Description: Extends the basic WordPress functionality to enable posts to be listed anywhere you can put a widget.
Version: 0.0.6
Author: Jeremy Tompkins
Author URI: http://www.exec-tools.com/
Copyright: 2017, Jeremy Tompkins, Exec Tools

GNU General Public License, Free Software Foundation <http://creativecommons.org/licenses/GPL/2.0/>
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if(!function_exists('str_split')) {
  function str_split($string, $split_length = 1) {
    $array = explode("\r\n", chunk_split($string, $split_length));
    array_pop($array);
    return $array;
  }
}

add_action('admin_menu', 'post_blocks_menu');
add_filter('the_content', 'post_blocks_check');

//Additional links on the plugin page
add_filter('plugin_row_meta', 'post_blocks_plugin_links',10,2);

function post_blocks_menu() {
  add_options_page('Post Blocks Options', 'Post Blocks', 8, __FILE__, 'post_blocks_options');
}

function post_blocks_check($content = ""){
  if(strpos($content,'[post_blocks]') !== false)
  {
    $pb_content = post_blocks_style();
    $pb_content .= post_blocks_output();
    $content = preg_replace('/\[post_blocks\]/',$pb_content,$content);
    return $content;
  }else{
    return $content;
  }
}

function post_blocks_options() {
 include_once 'post-blocks-settings.php';
}

function post_blocks_plugin_links($links, $file) {
  $base = plugin_basename(__FILE__);
  if ($file == $base) {
    $links[] = '<a href="options-general.php?page=' . $base .'">' . __('Settings','post_blocks') . '</a>';
    $links[] = '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=FSUVY53M35HTY" target="_blank">' . __('Donate','post_blocks') . '</a>';
  }
  return $links;
}
function post_blocks_output() {
  $pb_options   = get_option('widget_post_blocks');
  $pb_category  = ($pb_options['category']) ? array('cat'=>$pb_options['category']) : array();
  $pb_status    = (bool_from_yn(get_option("post_blocks_future_posts"))) ? array('future','publish') : array('publish');
  $pb_post_args = array_merge(array('posts_per_page' => $pb_options['number'] , 'nopaging' => 0, 'post_status' => $pb_status, 'ignore_sticky_posts' => true),$pb_category);
  $pb_r = new WP_Query($pb_post_args);
  if ($pb_r->have_posts()) :
    $return = '<div id="post_blocks"><ul id="posts">';
    while ($pb_r->have_posts()) : $pb_r->the_post(); $pb_date = get_the_date(); global $post;
      if($pb_options['titlemax'] > 0 && ( get_the_title() )){ $pb_title = str_split(html_entity_decode($post->post_title),$pb_options['titlemax']); }
        $return .= '
<li id="post-'.get_the_ID().'">
  <div class="datetime">
  ';
  if(!bool_from_yn(get_option("post_blocks_date_one_inactive"))){
    $return .= date(get_option("post_blocks_date_one"), strtotime($pb_date)).'<br />';
  }
  if(!bool_from_yn(get_option("post_blocks_date_two_inactive"))){
    $return .= date(get_option("post_blocks_date_two"), strtotime($pb_date));
  }
  $return .= '</div>
  <div class="post_blocks_post"><strong>';
  if($post->post_status == 'future'){
    if( get_the_title() ){
      $return .= ($pb_options['titlemax'] > 0 && count($pb_title) > 1) ? esc_attr($pb_title[0])."...": get_the_title();
    }else{
      $return .= get_the_ID();
    }
  }else{
    $return .= '<a href="'. get_the_permalink() .'" title="'. (get_the_title() ? esc_attr(get_the_title()) : get_the_ID() ) .'">';
    if ( get_the_title() ){
      $return .= ($pb_options['titlemax'] > 0 && count($pb_title) > 1) ? esc_attr($pb_title[0])."...": get_the_title();
    }else{
      $return .= get_the_ID();
    }
    $return .= '</a>';
  }
  $return .= '</strong><br>';
  $pb_content = ($pb_options['contentmax'] > 0) ? str_split(strip_tags(str_replace(']]>', ']]&gt;', apply_filters('the_content', get_the_content()))), $pb_options['contentmax']) : strip_tags(str_replace(']]>', ']]&gt;', apply_filters('the_content', get_the_content())));
  $return .= (($pb_options['contentmax'] > 0) && (count($pb_content) > 1)) ? "<span title='". htmlspecialchars(implode($pb_content), ENT_QUOTES)."'>".$pb_content[0]."...</span>" : (($pb_options['contentmax'] > 0) ? $pb_content[0] : $pb_content);
  $return .= '</div>
  </li>';
  endwhile;
  $return .= '</ul></div>';
  return $return;
  endif;
}

function post_blocks_style() {
  $pb_plugin_dir = '/wp-content/plugins';
  $pb_options = get_option('widget_post_blocks');
  if ( defined( 'PLUGINDIR' ) ) $pb_plugin_dir = '/' . PLUGINDIR;
  if(!bool_from_yn(get_option("post_blocks_remove_css"))){
    $return = '<style type="text/css">
#post_blocks .post_blocks_post { width: '. absint($pb_options['pwidth']) .'px; }
#post_blocks .datetime { width: '. absint($pb_options['dwidth']). 'px; }';
    if(get_option("post_blocks_css")){
      $return .= get_option("post_blocks_css");
    }else{
      $return .= '
#post_blocks, #post_blocks ul { margin: 0; padding: 1px; }
#post_blocks ul li { display: table;  border: 1px solid #c0c0c0; border-radius: 3px 3px 3px 3px; float:relative; float: left; margin: 5px; }
#post_blocks .post_blocks_post { display: table-cell; color:#000;font:1em "Georgia","Myriad Pro",sans-serif;height:40px;line-height:100%;overflow:hidden;padding:5px;text-align:left; vertical-align: top; }
#post_blocks .post_blocks_post h3 { padding-bottom: 3px; margin: 0px; }
#post_blocks .post_blocks_post a { color:#000; text-decoration: none !important; font-weight: bold; }
#post_blocks .post_blocks_post a:hover { text-decoration: underline; }
#post_blocks .datetime { display: table-cell; background: #c0c0c0; color: #919191; padding: 5px; margin: 0 !important; font:2em "Georgia","Myriad Pro",sans-serif; text-align:center; text-shadow: 1px 1px #D3D3D3, -1px -1px #6E6E6E;}
#post_blocks .monthday, #post_blocks .year{ display: block; }"; ?>';
    }
    $return .= '</style>';
    return $return;
  }
}

include_once dirname( __FILE__ ) . '/widget.php';

?>