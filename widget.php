<?php
/**
 * @package Post Blocks
 */
// Widget stuff
function widget_post_blocks_register() {
  if ( function_exists('register_sidebar_widget') ) :
  function widget_post_blocks($args) {
    extract($args);
    $pb_options   = get_option('widget_post_blocks');
    $pb_category  = ($pb_options['category']) ? array('cat'=>$pb_options['category']) : array();
    $pb_status    = (bool_from_yn(get_option("post_blocks_future_posts"))) ? array('future','publish') : array('publish');
    $pb_post_args = array_merge(array('posts_per_page' => $pb_options['number'] , 'nopaging' => 0, 'post_status' => $pb_status, 'ignore_sticky_posts' => true),$pb_category);
        $pb_r = new WP_Query($pb_post_args);
    if ($pb_r->have_posts()) :
?>
    <?php echo $before_widget; ?>
    <?php if (  $pb_options['title'] ) echo $before_title .  $pb_options['title'] . $after_title; ?>
    <ul id="posts">
    <?php  while ($pb_r->have_posts()) : $pb_r->the_post(); $pb_date = get_the_date(); global $post;
    if($pb_options['titlemax'] > 0 && ( get_the_title() )){ $pb_title = str_split(html_entity_decode($post->post_title),$pb_options['titlemax']); }
    ?>
    <li id="post-<?php the_ID(); ?>">
    <div class='datetime'>
    <?php if(!bool_from_yn(get_option("post_blocks_date_one_inactive"))){ echo date(get_option("post_blocks_date_one"), strtotime($pb_date)); ?><br /><?php } ?>
    <?php if(!bool_from_yn(get_option("post_blocks_date_two_inactive"))){ echo date(get_option("post_blocks_date_two"), strtotime($pb_date)); } ?>
    </div>
    <div class="post_blocks_post">
<?php if($post->post_status == 'future'){    ?>
        <h3><?php if ( get_the_title() ) ($pb_options['titlemax'] > 0 && count($pb_title) > 1) ? print esc_attr($pb_title[0])."...": the_title(); else the_ID(); ?></h3>
<?php }else{    ?>
        <h3><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) ($pb_options['titlemax'] > 0 && count($pb_title) > 1) ? print esc_attr($pb_title[0])."...": the_title(); else the_ID(); ?></a></h3>
<?php }    ?>
    <?php $pb_content = ($pb_options['contentmax'] > 0) ? str_split(strip_tags(str_replace(']]>', ']]&gt;', apply_filters('the_content', get_the_content()))), $pb_options['contentmax']) : strip_tags(str_replace(']]>', ']]&gt;', apply_filters('the_content', get_the_content())));
    echo (($pb_options['contentmax'] > 0) && (count($pb_content) > 1)) ? "<span title='". htmlspecialchars(implode($pb_content), ENT_QUOTES)."'>".$pb_content[0]."...</span>" : (($pb_options['contentmax'] > 0) ? $pb_content[0] : $pb_content); ?>
    </div>
    </li>
    <?php endwhile; ?>
    </ul>
    <?php echo $after_widget; ?>
    <div style="clear: both; height: 1px;"></div>
  <?php
    endif;
  }

  function widget_post_blocks_style() {
    $pb_plugin_dir = '/wp-content/plugins';
    $pb_options = get_option('widget_post_blocks');
    if ( defined( 'PLUGINDIR' ) ) $pb_plugin_dir = '/' . PLUGINDIR;
    if(!bool_from_yn(get_option("post_blocks_remove_css"))){
    ?>
<style type="text/css">
#post_blocks .post_blocks_post { width: <?php echo absint($pb_options['pwidth']); ?>px; }
#post_blocks .datetime { width: <?php echo absint($pb_options['dwidth']); ?>px;}
<?php echo (get_option("post_blocks_css")) ? get_option("post_blocks_css") : "#post_blocks, #post_blocks ul { margin: 0; padding: 1px; }
#post_blocks ul li { display: table;  border: 1px solid #c0c0c0; border-radius: 3px 3px 3px 3px; float:relative; float: left; margin: 5px; }
#post_blocks .post_blocks_post { display: table-cell; color:#000;font:1em 'Georgia','Myriad Pro',sans-serif;height:40px;line-height:100%;overflow:hidden;padding:5px;text-align:left; vertical-align: top; }
#post_blocks .post_blocks_post h3 { padding-bottom: 3px; margin: 0px; }
#post_blocks .post_blocks_post a { color:#000; text-decoration: none; font-weight: bold; }
#post_blocks .post_blocks_post a:hover { text-decoration: underline; }
#post_blocks .datetime { display: table-cell; background: #c0c0c0; color: #919191; padding: 5px; margin: 0 !important; font:2em 'Georgia','Myriad Pro',sans-serif; text-align:center; text-shadow: 1px 1px #D3D3D3, -1px -1px #6E6E6E;}
#post_blocks .monthday, #post_blocks .year{ display: block; }"; ?>
</style>
    <?php
    }
  }

  function widget_post_blocks_control() {
    $pb_options = $pb_newoptions = get_option('widget_post_blocks');
    if ( isset( $_POST['post_blocks-submit'] ) && $_POST["post_blocks-submit"] ) {
      $pb_newoptions['title'] = strip_tags(stripslashes($_POST["post_blocks-title"]));
      $pb_newoptions['number'] = absint(strip_tags(stripslashes($_POST["post_blocks-number"])));
      $pb_newoptions['dwidth'] = absint(strip_tags(stripslashes($_POST["post_blocks-dwidth"])));
      $pb_newoptions['pwidth'] = absint(strip_tags(stripslashes($_POST["post_blocks-pwidth"])));
      $pb_newoptions['titlemax'] = absint(strip_tags(stripslashes($_POST["post_blocks-titlemax"])));
      $pb_newoptions['contentmax'] = absint(strip_tags(stripslashes($_POST["post_blocks-contentmax"])));
      $pb_newoptions['category'] = absint(strip_tags(stripslashes($_POST["post_blocks-category"])));
      if ( empty($pb_newoptions['title']) ) $pb_newoptions['title'] = __('Posts');
      if ( empty($pb_newoptions['number']) ) $pb_newoptions['number'] = 4;
      if ( empty($pb_newoptions['dwidth']) ) $pb_newoptions['dwidth'] = 60;
      if ( empty($pb_newoptions['pwidth']) ) $pb_newoptions['pwidth'] = 230;
      if ( empty($pb_newoptions['titlemax']) && $pb_newoptions['titlemax'] !== 0 ) $pb_newoptions['titlemax'] = 20;
      if ( empty($pb_newoptions['contentmax']) && $pb_newoptions['contentmax'] !== 0 ) $pb_newoptions['contentmax'] = 120;
    }
    if ( $pb_options != $pb_newoptions ) {
      $pb_options = $pb_newoptions;
      update_option('widget_post_blocks', $pb_options);
    }
    $pb_title = htmlspecialchars($pb_options['title'], ENT_QUOTES);
    $pb_number = htmlspecialchars($pb_options['number'], ENT_QUOTES);
    $pb_dwidth = htmlspecialchars($pb_options['dwidth'], ENT_QUOTES);
    $pb_pwidth = htmlspecialchars($pb_options['pwidth'], ENT_QUOTES);
    $pb_titlemax = htmlspecialchars($pb_options['titlemax'], ENT_QUOTES);
    $pb_contentmax = htmlspecialchars($pb_options['contentmax'], ENT_QUOTES);
    $pb_category = htmlspecialchars($pb_options['category'], ENT_QUOTES);
    ?>
        <p><label for="post_blocks-title"><?php _e('Title:'); ?></label>
        <input class="widefat" id="post_blocks-title" name="post_blocks-title" type="text" value="<?php echo $pb_title; ?>" /></p>
        <p><label for="post_blocks-number"><?php _e('Number of posts to show:'); ?></label>
        <input class="widefat" id="post_blocks-number" name="post_blocks-number" type="text" value="<?php echo $pb_number; ?>"  size="3" /></p>
        <p><label for="post_blocks-dwidth"><?php _e('Date Block Width(pixels):'); ?></label>
        <input class="widefat" id="post_blocks-dwidth" name="post_blocks-dwidth" type="text" value="<?php echo $pb_dwidth; ?>"  size="3" /></p>
        <p><label for="post_blocks-pwidth"><?php _e('Post Block Width(pixels):'); ?></label>
        <input class="widefat" id="post_blocks-pwidth" name="post_blocks-pwidth" type="text" value="<?php echo $pb_pwidth; ?>"  size="3" /></p>
        <p><label for="post_blocks-titlemax"><?php _e('Post Title Max(# Characters)(0 for all):'); ?></label>
        <input class="widefat" id="post_blocks-titlemax" name="post_blocks-titlemax" type="text" value="<?php echo $pb_titlemax; ?>"  size="3" /></p>
        <p><label for="post_blocks-contentmax"><?php _e('Post Content Max(# Characters)(0 for all):'); ?></label>
        <input class="widefat" id="post_blocks-contentmax" name="post_blocks-contentmax" type="text" value="<?php echo $pb_contentmax; ?>"  size="3" /></p>
        <p><label for="post_blocks-category"><?php _e('Post Category:'); ?></label>
        <?php wp_dropdown_categories(array('hide_empty' => 1, 'name' => 'post_blocks-category', 'orderby' => 'name', 'selected' => $pb_category, 'hierarchical' => true, 'class' => 'widefat', 'show_option_all' => __('All Categories'))); ?>
        <input type="hidden" id="post_blocks-submit" name="post_blocks-submit" value="1" />
  <?php
  }

  if ( function_exists( 'wp_register_sidebar_widget' ) ) {
    wp_register_sidebar_widget( 'post_blocks', 'Post Blocks', 'widget_post_blocks', null, 'post_blocks');
    wp_register_widget_control( 'post_blocks', 'Post Blocks', 'widget_post_blocks_control', null, 75, 'post_blocks');
  } else {
    register_sidebar_widget('Post Blocks', 'widget_post_blocks', null, 'post_blocks');
    register_widget_control('Post Blocks', 'widget_post_blocks_control', null, 75, 'post_blocks');
  }
  if ( is_active_widget('widget_post_blocks') )
    add_action('wp_head', 'widget_post_blocks_style');
  endif;
}

add_action('init', 'widget_post_blocks_register');
