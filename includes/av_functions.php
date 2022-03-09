<?php
use ApiVideo\Client\Client;

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

    $client = new \ApiVideo\Client\Client(
        'https://ws.api.video',
        $apikey,
        $httpClient
    );

    $client->setApplicationName("wordpress-plugin", "1.0.5");

    return $client;
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
    $videoId = $atts['video_id']; 
    if(preg_match_all('/^[a-zA-Z0-9]+$/', $videoId) == 0) {
        return "";
    } 
    return '<div class="video-iframe"><iframe loading="lazy" src="https://embed.api.video/vod/'.$videoId.'" width="100%" height="100%" frameborder="0" scrolling="no" allowfullscreen="true"></iframe></div>';
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
