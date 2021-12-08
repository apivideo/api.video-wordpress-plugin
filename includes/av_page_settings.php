<?php
function api_settings_page() {
  if(isset($_POST['api_video_options']['api_key'])):
    update_option("av_api_key", $_POST['api_video_options']['api_key']);
  endif; ?>
  <h2>api.video API key</h2>
  <p><a target="_blank" href="https://api.video">api.video</a> provides video infrastructure for product builders.</p>
  <p>Use <a target="_blank" href="https://api.video">api.video</a>'s lightning-fast video APIs for integrating, scaling, and managing on-demand & low latency live streaming features in your WordPress site.</p>
  <p>If you don't already have an API key you can sign up for a free sandbox account at <a target="_blank" href="https://api.video">api.video</a>.</p>
  <form action="" method="post">
  <input id='api-video-settings-key' name='api.video_options[api_key]' type='text' value='<?= get_option("av_api_key"); ?>' />
      <input name="submit" class="button button-primary" type="submit" value="<?php esc_attr_e( 'Save' ); ?>" />
  </form>
<?php }
