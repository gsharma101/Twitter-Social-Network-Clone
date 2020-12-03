$(document).ready(function () {
  $(document).on("click", ".t-show-popup", function () {
    var tweet_id = $(this).data("tweet");
    $.post(
      "http://127.0.0.1/twitter/core/ajax/popuptweets.php",
      { showpopup: tweet_id },
      function (data) {
        $(".popupTweet").html(data);
        $(".tweet-show-popup-box-cut").click(function () {
          $(".tweet-show-popup-wrap").hide();
        });
      }
    );
  });

  $(document).on("click", ".imagePopup", function (e) {
    e.stopPropagation();
    var tweet_id = $(this).data("tweet");
    $.post(
      "http://127.0.0.1/twitter/core/ajax/imagePopup.php",
      { showImage: tweet_id },
      function (data) {
        $(".popupTweet").html(data);
      }
    );
  });
});
