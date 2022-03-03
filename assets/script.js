const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const product = urlParams.get('page');

(function ($) {
  $(document).ready(function () {

    if (product == "add-new-video") {
      const realTokenData = document.querySelector('#real-token-data');
      const realtoken = realTokenData.dataset.realtoken;
      const dropArea = document.querySelector('#dropContainer');
      const input = document.querySelector('#fileInput');
      const chunkSize = 5242880;

      input.addEventListener('change', videoUploadAction);
      dropArea.addEventListener('drop', videoUploadAction);

      function videoUploadAction() {
        document.getElementById("custom-bg-grey-containter").style.display = "block";
        document.getElementById("av-progress-bar").style.display = "flex";
        if (dropArea.classList.contains("currentUpload")) {
          return;
        }
        document.getElementById("dropContainer").classList.add("currentUpload");
        document.getElementById("fileInput").disabled = true;

        const uploader = new VideoUploader({
          file: input.files[0],
          chunkSize: chunkSize,
          uploadToken: realtoken,
          retries: 1
        });

        uploader.onProgress(function (event) {
          var totalPercentComplete = Math.round(event.uploadedBytes / event.totalBytes * 100);
          document.getElementById("progress-tracker").value = totalPercentComplete;
          document.getElementById("chunk-information").innerHTML = "Total uploaded: " + totalPercentComplete + "%";
        })

        uploader.upload()
          .then(function (video) {
            document.getElementById("video-information").innerHTML = "Watch the video <a href='admin.php?page=api.video-library&videoid=" + video.videoId + "'>here</a>";
            document.getElementById("fileInput").value = null;
            document.getElementById("fileInput").disabled = false;
            dropArea.classList.remove("currentUpload");
            document.getElementById("custom-bg-grey-containter").style.display = "none";
          })
          .catch(function (error) {
            document.getElementById("chunk-information").innerHTML = "";
            document.getElementById("fileInput").value = null;
            document.getElementById("av-progress-bar").style.display = "none";
            if (error.status == 401) {
              document.getElementById("video-information").innerHTML = "You took too long to select your video. Please refresh the page and try again.";
            } else {
              document.getElementById("video-information").innerHTML = "An error occured. Plsead refresh the page and try again.";
            }
          });
      }
    }

    if (product == "api.video-library") {
      $("#tooltip-questionmark-image").tooltip();
      $(".clickable-item").tooltip();
    }

    $(".button-form-trigger").on("click", function () {
      $(this).parent().parent().find('#head-form').find('#form-button-trigger').trigger('click');
    });

    if ($(".video-info-frame").hasClass("active")) {
      $("#bg-for-videos").css("display", "block");
    } else {
      $("#bg-for-videos").css("display", "none");
    }

    $(".clickable-item").hover(
      function () {
      }, function () {
        $(".ui-tooltip").css("display", "none");
        $(".clickable-item").tooltip("disable");
        $(".clickable-item").tooltip("option", "content", "Copy");
        $(".clickable-item").tooltip("enable");
        $(".ui-tooltip").css("display", "block");
      }
    );

    $(".clickable-item").on("click", function () {
      $(this).tooltip("option", "content", "Copied!");

      idForItemItem = $(this).prev();

      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(idForItemItem.val()).select();
      document.execCommand("copy");
      $temp.remove();

      $(this).prev().focus();
      $(this).prev().select();
    });

    function loadCurrentVideoPlayer() {
      var url = $(".video-info-frame.active .custom-iframe input").val();

      if (!url) {
        return;
      }

      var iframe = document.createElement("iframe");
      iframe.src = url;
      iframe.width = "100%";
      iframe.height = "100%";
      iframe.frameborder = "0";
      iframe.scrolling = "no";
      iframe.allowfullscreen = "";

      $(".video-info-frame.active .custom-iframe").append(iframe);
    }

    loadCurrentVideoPlayer();

    $(".apivideo-trig").click(function () {
      $(this).prev().addClass("active");
      $("#bg-for-videos").css("display", "block");
      loadCurrentVideoPlayer();
    });

    $(".close-video-trig").on("click", function () {
      $("#bg-for-videos").css("display", "none");
      $(".video-info-frame").removeClass("active");
      $(this).parent().parent().next().find("iframe").attr('src', $(this).parent().parent().next().find("iframe").attr('src'));
    });

    $("#bg-for-videos").on("click", function (e) {
      $(this).next().next().find(".video-info-frame.active").find("iframe").attr('src', $(this).next().next().find(".video-info-frame.active").find("iframe").attr('src'));
      if (e.target !== this) { }
      else {
        $("#bg-for-videos").css("display", "none");
        $(".video-info-frame").removeClass("active");
      }
    });
  });
})(jQuery);


function customfunction() {
  if (confirm("You are about to permanently delete this item from your site. This action cannot be undone. 'Cancel' to stop, 'OK' to delete.")) {
  } else {
    return false;
  }
}

function av_change_url() {
  window.history.pushState("object or string", "Title", "admin.php?page=api.video-library");
}

/*
var __nspid = "isrtzw";
var __nsptags = [];
(function (w, d) {
  var x = function () {
    var j = d.createElement("script");
    j.type = "text/javascript";
    j.async = true;
    j.src = "http" + ("https:" === d.location.protocol ? "s://cs" : "://c") + ".ns1p.net/p.js?a=" + __nspid;
    d.body.appendChild(j);
  }
  if (w.addEventListener) {
    w.addEventListener("load", x, false);
  } else if (w.attachEvent) {
    w.attachEvent("onload", x);
  } else {
    w.onload = x;
  }
}(window, document));
*/