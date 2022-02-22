<?php
function av_library() { ?>
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>

  <?php
    $url = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $query_str = parse_url($url, PHP_URL_QUERY);
    parse_str($query_str, $query_params);
  ?>

  <?php 
  // Get client
  $client = av_get_client();
  if(!$client): ?>
    <p class="api-error-message">Api key is not added/valid, please go to <a href="admin.php?page=settings-api-video">settings</a> page and update it with correct one</p>
    <?php return;
  endif;
  ?>

  <!-- Wrap section -->
  <div class="wrap">
    <h1 class="wp-heading-inline">Library</h1>
    <a href="admin.php?page=add-new-video" class="page-title-action aria-button-if-js">Add New</a>
    <hr class="wp-header-end">

    <?php

      // Handle if delete button is clicked
      if (isset($_POST["deleteavideo"])) {
        $client->videos()->delete($_POST['deletevideoid']);
      }

      // Handle if Save button is clicked!
      if (isset($_POST['submit-button'])) :
        $public = true;
        $panoramic = false;
        $mp4support = false;

        if ($_POST['input_public']) : $public = false;
        endif;
        if ($_POST['input_panoramic']) : $panoramic = true;
        endif;
        if ($_POST['input_mp4support']) : $mp4support = true;
        endif;

        $av_tags = explode(',', $_POST['input_tags']);
        $av_tags = str_replace(' ', '', $av_tags);

        $client->videos()->update($_POST['videoid'], (new \ApiVideo\Client\Model\VideoUpdatePayload())
          ->setTitle($_POST['input_title'])
          ->setDescription($_POST['input_description'])
          ->setPublic($public)
          ->setPanoramic($panoramic)
          ->setMp4Support($mp4support)
          ->setTags($av_tags));
      endif;

      // Check if videos exist
      $videos = [];
      $currentPageNum = 0; 
      do {
          $currentPage = $client->videos()->list(["sortBy" => "publishedAt", "sortOrder" => "desc", "currentPage" => $currentPageNum]);
          $currentPageNum++;
          $videos = array_merge($videos, $currentPage->getData());
      } while($currentPage->getPagination()->getCurrentPage() < $currentPage->getPagination()->getPagesTotal());

      if (!$videos) :
        echo "<h4>No videos.</h4>"; 
      endif; ?>
  </div>
  <!-- End of Wrap section -->

  <!-- Video section -->
  <div class="videos-wrapper">
    <?php
      // Get videos from newest to oldest
      $videos_decoded = $videos;
      // Loop for every video and check if there is extra class
      foreach ($videos_decoded as $video) :
        $video = json_decode($video);
        if ($query_params['videoid'] == $video->videoId) : $extra_class = " active";
        else : $extra_class = "";
        endif; 
      ?>

      <!-- Single video popup -->
      <div class="video-info-frame<?= $extra_class; ?>" data-videoid="<?= $video->videoId ?>">
        <!-- Single video header -->
        <div class="video-frame-top-section">
          <div class="video-frame-header-text">
            <h1>Video details</h1>
          </div>
          <div class="edit-media-header">
            <a onclick="av_change_url()" class="close-video-trig">X</a>
          </div>
        </div>
        <!-- End of Single video header -->

        <!-- Video popup main section -->
        <div class="video-frame-bottom-section">
          <!-- Video -->
          <div class="video-info-left">
            <div class="custom-iframe">
              <?= $video->assets->iframe ?>
            </div>
          </div>
          <!-- End of Video -->

          <!-- Video general wrapper -->
          <div class="video-info-right">
            <!-- Video info -->
            <div class="video-info-right-data">
              <div><span class="video-info-bold-text">Video id: </span><span><?= $video->videoId; ?></span></div>
              <div><span class="video-info-bold-text">Video created at: </span><span><?= get_video_date($video->createdAt); ?></span></div>
              <div><span class="video-info-bold-text">Video published at: </span><span><?= get_video_date($video->publishedAt); ?></span></div>
              <div><span class="video-info-bold-text">Video updated at: </span><span><?= get_video_date($video->updatedAt); ?></span></div>
            </div>
            <!-- End of Video info-->

            <!-- General form -->
            <form method="POST" id="head-form" action="admin.php?page=api.video-library&videoid=<?= $video->videoId ?>">
              <div class="av-settings">
                <!-- Text for video -->
                <div class="custom-private-text">
                  <?php if (!$video->public) : ?>
                    <p>This video is private, and will not be visible without implementing tokens. Learn more <a target="_blank" href="https://api.video/blog/tutorials/tutorial-private-video/">here</a>. </p>
                  <?php endif; ?>
                </div>

                <!-- Labels and inputs -->
                <div class="labels-and-inputs-wrapper">
                  <ul class="labels-and-inputs-ul">
                    <li class="labels-and-inputs-li">
                      <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Private: </label>
                      <input class="av-custom-input custom-copy-item" name="input_public" id="clickable-item-3" <?php if (!$video->public) { echo "checked"; } ?> type="checkbox" class="attachment-details-copy-link" value="yes">
                      <div id="tooltip-questionmark-image" title="Private videos will not be visible without implementing tokens.">?</div>
                    </li>
                    <li class="labels-and-inputs-li">
                      <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Panoramic: </label>
                      <input class="av-custom-input custom-copy-item" name="input_panoramic" id="clickable-item-4" <?php if ($video->panoramic) { echo "checked"; } ?> type="checkbox" class="attachment-details-copy-link" value="yes">
                    </li>
                    <li class="labels-and-inputs-li">
                      <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Enable downloads: </label>
                      <input class="av-custom-input custom-copy-item" name="input_mp4support" id="clickable-item-5" <?php if ($video->mp4Support) { echo "checked"; } ?> type="checkbox" class="attachment-details-copy-link" value="yes">
                    </li>
                  </ul>
                </div>

                <!-- Title -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-title" class="name custom-label-item">Title: </label>
                  <input class="custom-input-margin av-custom-input custom-copy-item" id="clickable-item-1" name="input_title" type="text" value="<?= $video->title ?>">
                </div>

                <!-- Description -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Description: </label>
                  <textarea class="custom-input-margin av-custom-input custom-copy-item" id="clickable-item-2" name="input_description" type="text" class="attachment-details-copy-link"><?= $video->description ?></textarea>
                </div>

                <!-- Tags -->
                <div class="custom-video-inputs tags">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Tags: </label>
                  <input class="custom-input-margin av-custom-input custom-copy-item" name="input_tags" id="clickable-item-6" type="text" class="attachment-details-copy-link" value="<?php av_tags($video->tags); ?>">
                </div>

                <!-- Separator -->
                <div class="custom-separator">
                  <span>Separate tags by comma</span>
                </div>

                <!-- Video URL -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Video URL: </label>
                  <input class="av-custom-input custom-copy-item" name="input_video_url" id="clickable-item-7" type="text" class="attachment-details-copy-link" readonly value="<?= $video->assets->player ?>">
                  <div class="clickable-item" title="Copy"></div>
                </div>

                <!-- Shortcode -->
                <div class="custom-video-inputs">
                  <label for="attachment-details-two-column-copy-link" class="name custom-label-item">Shortcode: </label>
                  <input class="custom-copy-item custom-input-width-75" id="clickable-item-8" type="text" readonly value="[api.video video_id=<?= $video->videoId; ?>]">
                  <div class="clickable-item" title="Copy"></div>
                </div>

                <!-- Save button for form -->
                <input type="submit" name="submit-button" id="form-button-trigger" class="button button-primary" value="Save">
                <input type="text" id="hidden-video-id" name="videoid" value="<?= $video->videoId ?>">
              </div>
            </form>
            <!-- End of General form -->

            <!-- Settings bar -->
            <div class="actions settings-bar">
              <form method="POST" class="action-delete-form" onsubmit="return customfunction()">
                <?php if ($video->assets->mp4) : ?>
                  <a target="_blank" href="<?= $video->assets->mp4 ?>">Download</a>
                  <span class="links-separator">|</span>
                <?php endif; ?>
                <a class="custom-edit-link" target="_blank" href="https://go.api.video/videos/<?= $video->videoId; ?>">Edit on api.video</a>
                <span class="links-separator">|</span>
                <input type="submit" class="button-link custom-delete-link wp-delete-permanently" name="deleteavideo" value="Delete permanently" />
                <input type="text" class="form-hidden-text-input" name="deletevideoid" value="<?= $video->videoId ?>" />
              </form>
              <a class="button-form-trigger button button-primary">Save</a>
            </div>
            <!-- End of Settings bar -->
          </div>
          <!-- End of Video general wrapper -->
        </div>
        <!-- Video popup right section -->
      </div>
      <!-- End of Single video popup -->

      <div class="single-video apivideo-trig" style="background-image: url('<?= $video->assets->thumbnail; ?>');">
        <div class="filenamee"><?= $video->title ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <!-- End of Video section -->
  <div onclick="av_change_url()" id="bg-for-videos"></div>
<?php } ?>