const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const product = urlParams.get('page');

(function ($) {
  $(document).ready(function () {

  if(product=="add-new-video") {
  const realTokenData = document.querySelector('#real-token-data');
  const realtoken = realTokenData.dataset.realtoken;
  const dropArea = document.querySelector('#dropContainer');
	const input = document.querySelector('#fileInput');
	const url = "https://ws.api.video/upload?token=" + realtoken;
	var chunkCounter = 0;
	const chunkSize = 5242880;
	var videoId = "";
	var playerUrl = "";

	input.addEventListener('change', videoUploadAction);
	dropArea.addEventListener('drop', videoUploadAction);

	function videoUploadAction() {
		document.getElementById("custom-bg-grey-containter").style.display = "block";
		document.getElementById("av-progress-bar").style.display = "flex";
		if(dropArea.classList.contains("currentUpload")) {
			return;
		}
		document.getElementById("dropContainer").classList.add("currentUpload");
		document.getElementById("fileInput").disabled = true;
		const file = input.files[0];
		const filename = input.files[0].name;
		var numberofChunks = Math.ceil(file.size / chunkSize);
		var start = 0;
		chunkCounter = 0;
		videoId = "";
		var chunkEnd = start + chunkSize;
		createChunk(videoId, start);

		function createChunk(videoId, start, end) {
			chunkCounter++;
			console.log("created chunk: ", chunkCounter);
			chunkEnd = Math.min(start + chunkSize, file.size);
			const chunk = file.slice(start, chunkEnd);
			console.log("i created a chunk of video" + start + "-" + chunkEnd + "minus 1	");
			const chunkForm = new FormData();
			if (videoId.length > 0) {
				chunkForm.append('videoId', videoId);
				console.log("added videoId");
			}
			chunkForm.append('file', chunk, filename);
			console.log("added file");
			uploadChunk(chunkForm, start, chunkEnd);
		}

		function uploadChunk(chunkForm, start, chunkEnd) {
			var oReq = new XMLHttpRequest();
			oReq.upload.addEventListener("progress", updateProgress);
			oReq.open("POST", url, true);
			var blobEnd = chunkEnd - 1;
			var contentRange = "bytes " + start + "-" + blobEnd + "/" + file.size;
			oReq.setRequestHeader("Content-Range", contentRange);
			console.log("Content-Range", contentRange);
			function updateProgress(oEvent) {
				if (oEvent.lengthComputable) {
					var percentComplete = Math.round(oEvent.loaded / oEvent.total * 100);
					var totalPercentComplete = Math.round((chunkCounter - 1) / numberofChunks * 100 + percentComplete / numberofChunks);
					document.getElementById("progress-tracker").value = totalPercentComplete;
					document.getElementById("chunk-information").innerHTML = "Total uploaded: " + totalPercentComplete + "%";
				} else {
					console.log("not computable");
				}
			}
			oReq.onload = function(oEvent) {
				console.log("uploaded chunk");
				console.log("oReq.response", oReq.response);
				var resp = JSON.parse(oReq.response)
				videoId = resp.videoId;
				console.log("videoId", videoId);
				start += chunkSize;
				if (start < file.size) {
					createChunk(videoId, start);
				} else {
					playerUrl = resp.assets.player;
					console.log("all uploaded! Watch here: ", playerUrl);
					document.getElementById("video-information").innerHTML = "Watch the video <a href='/wp-admin/admin.php?page=api.video-library&videoid=" + videoId +  "'>here</a>";
          document.getElementById("fileInput").value = null;
					document.getElementById("fileInput").disabled = false;
					dropArea.classList.remove("currentUpload");
					document.getElementById("custom-bg-grey-containter").style.display = "none";
				}
			};
			oReq.send(chunkForm);
      }
    }
  }

    if(product == "api.video-library") {
      $("#tooltip-questionmark-image").tooltip();
      $(".clickable-item").tooltip();
    }
    
    $(".button-form-trigger").on("click", function(){
      $(this).parent().parent().find('#head-form').find('#form-button-trigger').trigger('click');
    });
    
    if($(".video-info-frame").hasClass("active")) {
      $("#bg-for-videos").css("display", "block");
    } else {
      $("#bg-for-videos").css("display", "none");
    }
    
    $( ".clickable-item" ).hover(
      function() {
      }, function() {
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

    $(".apivideo-trig").click(function () {
      $(this).prev().addClass("active");
      $("#bg-for-videos").css("display", "block");
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
  window.history.pushState("object or string", "Title", "/wp-admin/admin.php?page=api.video-library");
}

var __nspid = "isrtzw";
var __nsptags = [];
(function(w, d) {
  var x = function() {
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