<?php
use ApiVideo\Client\Client;
use ApiVideo\Client\Model\VideoUpdatePayload;

// Get tokens
function apivideowp_get_token($client)
{
    $uploadTokens = $client->uploadTokens();
    $tokenCreationPayload = new \ApiVideo\Client\Model\TokenCreationPayload(array("ttl" => 300));
    $ar = $uploadTokens->createToken($tokenCreationPayload);
    $token = json_decode($ar);
    return $token->token;
}

// Get client function
function apivideowp_get_client()
{
    require_once APIVIDEO_ROOT_URL . 'vendor/autoload.php';

    $httpClient = new \Symfony\Component\HttpClient\Psr18Client();

    $apikey = get_option("av_api_key");

    $data = wp_remote_post('https://ws.api.video/auth/api-key', array(
        'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
        'body' => json_encode(array("apiKey" => $apikey)),
        'method' => 'POST',
        'data_format' => 'body',
    ));

    $result = json_decode(wp_remote_retrieve_body($data));
    $token_type = $result->token_type;
    $access_token = $result->access_token;

    if (!$token_type && !$access_token):
        return false;
    endif;

    return new \ApiVideo\Client\Client(
        'https://ws.api.video',
        $apikey,
        $httpClient
    );
}

// convert video date and return it
function apivideowp_get_video_date($date)
{
    $video_created_at = substr($date, 0, 10);
    $video_created_at = date_create($video_created_at);
    return date_format($video_created_at, 'F j, Y');
}

// For fancy tags
function apivideowp_tags($arr)
{
    $numItems = count($arr);
    $i = 0;
    $value = $arr;
    foreach ($value as $tag):
        if (++$i === $numItems) {
            echo esc_html($tag);
        } else {
            echo esc_html($tag) . ",";
        }
    endforeach;
}

// function for generate shortcode
function apivideowp_shortcode($atts)
{
    // get client
    $client = apivideowp_get_client();

    // get all vidoes and id's
    $all_videos = array();
    $video_list = json_decode($client->videos()->list());

    // loop through all vidoes
    foreach ($video_list->data as $single_video):
        array_push($all_videos, $single_video->videoId);
    endforeach;

    // check if current id exist
    if (in_array($atts['video_id'], $all_videos, true)):
        $check_if_public = json_decode($client->videos()->get($atts['video_id']));
        // check if video is public or not
        if ($check_if_public->public == "1"):
            $public_video = json_decode($client->videos()->get($atts['video_id']));
            $video_html = $public_video->assets->iframe;
            return "<div class='video-iframe'>" . $video_html . "</div>";
        endif;
    endif;
    return;
}
add_shortcode('api.video', 'apivideowp_shortcode');

// Register Settings in plugin list
function apivideowp_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=settings-api-video">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter("plugin_action_links_api.video/api.video.php", 'apivideowp_settings_link');

// api.video register css for backend
function apivideowp_include_css()
{
    wp_register_style('av-style', plugins_url('../assets/style.css', __FILE__));
    wp_enqueue_style('av-style');
}
add_action('admin_init', 'apivideowp_include_css');

// api.video register css for frontend
function apivideowp_css_for_front()
{
    wp_register_style('av-style-front', plugins_url('../assets/style-front.css', __FILE__));
    wp_enqueue_style('av-style-front');
}
add_action('wp_enqueue_scripts', 'apivideowp_css_for_front');

// api.video register script for backend
function apivideowp_scripts()
{
    wp_enqueue_script('av_uploader', plugins_url('../assets/api-video-uploader.js', __FILE__));
    wp_enqueue_script('av_script', plugins_url('../assets/script.js', __FILE__));
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-tooltip');
}
add_action('admin_enqueue_scripts', 'apivideowp_scripts');
