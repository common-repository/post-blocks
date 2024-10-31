<?php
  $pb_options = get_option('widget_post_blocks');
  $pb_options_to_update = array('post_blocks_css','post_blocks_future_posts','post_blocks_remove_css','post_blocks_date_one','post_blocks_date_two','post_blocks_date_one_inactive','post_blocks_date_two_inactive');
 ?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div>
  <h2>Post Blocks</h2>
  <form method="post" action="options.php"><input type="hidden" name="action" value="update" />
  <input type="hidden" name="page_options" value="<?php echo esc_attr( implode( ',', $pb_options_to_update ) ); ?>" />
  <?php wp_nonce_field('update-options'); ?>
  <table class="form-table">
   <tr valign="top">
    <th scope="row" rowspan="2">CSS</th>
    <td><span class="regular-text code">&lt;style&gt;<br>
    #post_blocks .post_blocks_post { width: <?php echo absint($pb_options['pwidth']); ?>px; }<br>
    #post_blocks .datetime { width: <?php echo absint($pb_options['dwidth']); ?>px;}<br>
    </span>
    <textarea rows="12" style="width:100%;" name="post_blocks_css"><?php echo (get_option("post_blocks_css")) ? get_option("post_blocks_css") : "#post_blocks, #post_blocks ul { margin: 0; padding: 1px; }
#post_blocks ul li { display: table;  border: 1px solid #c0c0c0; border-radius: 3px 3px 3px 3px; float:relative; float: left; margin: 5px; }
#post_blocks .post_blocks_post { display: table-cell; color:#000;font:1em 'Georgia','Myriad Pro',sans-serif;height:40px;line-height:100%;overflow:hidden;padding:5px;text-align:left; vertical-align: top; }
#post_blocks .post_blocks_post h3 { padding-bottom: 3px; margin: 0px; font-size: 1rem; }
#post_blocks .post_blocks_post a { color:#000; text-decoration: none; font-weight: bold; }
#post_blocks .post_blocks_post a:hover { text-decoration: underline; }
#post_blocks .datetime { display: table-cell; background: #c0c0c0; color: #919191; padding: 5px; margin: 0 !important; font:2em 'Georgia','Myriad Pro',sans-serif; text-align:center; text-shadow: 1px 1px #D3D3D3, -1px -1px #6E6E6E;}
#post_blocks .monthday, #post_blocks .year{ display: block; }"; ?></textarea><span class="regular-text code">&lt;/style&gt;</span></td>
   </tr>
   <tr valign="top">
    <td>
    <input type="checkbox" value="y" id="post_blocks_remove_css" name="post_blocks_remove_css"<?php echo get_option("post_blocks_remove_css") ? " checked='checked' " : " " ?>/><label for="post_blocks_remove_css">Disable CSS?</label>
    </td>
   </tr>
   <tr valign="top">
    <th scope="row">Future Posts</th>
    <td>
    <input type="checkbox" value="y" id="post_blocks_future_posts" name="post_blocks_future_posts"<?php echo get_option("post_blocks_future_posts") ? " checked='checked' " : " " ?>/><label for="post_blocks_future_posts">Enable future posts?</label>
    </td>
   </tr>
   <tr valign="top">
    <th scope="row">Date Format</th>
    <td>
    <label for="post_blocks_date_one">Date Line One:</label> <input type="text" value="<?php echo get_option("post_blocks_date_one") ? get_option("post_blocks_date_one") : "n/j" ?>" id="post_blocks_date_one" name="post_blocks_date_one" /> <span class="description">(ex: <?php echo date((get_option("post_blocks_date_one") ? get_option("post_blocks_date_one") : "n/j"));?>)</span> <input type="checkbox" value="y" id="post_blocks_date_one_inactive" name="post_blocks_date_one_inactive"<?php echo get_option("post_blocks_date_one_inactive") ? " checked='checked' " : " " ?>/><label for="post_blocks_date_one_inactive">Disable?</label><br />
    <label for="post_blocks_date_two">Date Line Two:</label> <input type="text" value="<?php echo get_option("post_blocks_date_two") ? get_option("post_blocks_date_two") : "Y" ?>" id="post_blocks_date_two" name="post_blocks_date_two" /> <span class="description" >(ex: <?php echo date((get_option("post_blocks_date_two") ? get_option("post_blocks_date_two") : "Y"));?>)</span> <input type="checkbox" value="y" id="post_blocks_date_two_inactive" name="post_blocks_date_two_inactive"<?php echo get_option("post_blocks_date_two_inactive") ? " checked='checked' " : " " ?>/><label for="post_blocks_date_two_inactive">Disable?</label><br />
    <span class='tip'>Date format uses PHPs <a href="http://www.php.net/date" target="_blank">date()</a> format options</span>
    </td>
   </tr>
  </table>
  <p class="submit">
  <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
  </p></form>
</div>