<?php
function av_add_new_video() { ?>
  <div class="wrap">
    <h2><?= esc_html(get_admin_page_title()) ?></h2>
  </div>

  <div id="dropContainer" class="dropContainerWrapper">
    <div id="custom-bg-grey-containter"></div>
    <h2 class="upload-instructions drop-instructions">Drop files to upload</h2>
    <p class="upload-instructions drop-instructions">or</p>
    <label for="fileInput" id="video-file-label">
		  Select files
    </label>
  </div>
  <input type="file" id="fileInput" />

  <?php 
  $client = av_get_client();
  $real_token = av_get_token($client); ?>
  <div data-realtoken="<?=$real_token?>" id="real-token-data" style="display: none;"></div> 

  <div id="action-upload">
	<input type="file" id="video-file">
  </div>
  <div id="chunk-information"></div>
  <div id="av-progress-bar">
    <progress id="progress-tracker" value="0" max="100"></progress>
  </div>
  <div id="video-information"></div>

  <script>
  dropContainer.ondragover = dropContainer.ondragenter = function(evt) {
    document.getElementById("dropContainer").style.borderColor = "#2271b1";
    evt.preventDefault();
  };
  dropContainer.ondragleave = dropContainer.ondragleave = function(evt) {
    document.getElementById("dropContainer").style.borderColor = "#c3c4c7";
    evt.preventDefault();
  };

  dropContainer.ondrop = function(evt) {
    fileInput.files = evt.dataTransfer.files;
    const dT = new DataTransfer();
    dT.items.add(evt.dataTransfer.files[0]);
    fileInput.files = dT.files;
    evt.preventDefault();
  };
  </script>
<?php }
