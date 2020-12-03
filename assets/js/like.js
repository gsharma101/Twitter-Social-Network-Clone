$(document).ready(function () {
  $(document).on("click", ".like-btn", function () {
    var tweet_id = $(this).data("tweet");
    var user_id = $(this).data("user");
    var counter = $(this).find(".likesCounter");
    var button = $(this);
    var count = counter.text();

    var data = {
      like: tweet_id,
      user_id: user_id,
    };

    $.post("http://127.0.0.1/twitter/core/ajax/like.php", data, function () {
      counter.show();
      button.addClass("unlike-btn");
      button.removeClass("like-btn");
      count++;
      counter.text(count);
      button.find(".fa-heart-o").addClass("fa-heart");
      button.find(".fa-heart").removeClass("fa-heart-o");
    });
  });

  $(document).on("click", ".unlike-btn", function () {
    var tweet_id = $(this).data("tweet");
    var user_id = $(this).data("user");
    var counter = $(this).find(".likesCounter");
    var btn = $(this);
    var count = counter.text();

    var data = {
      unlike: tweet_id,
      user_id: user_id,
    };

    $.post("http://127.0.0.1/twitter/core/ajax/like.php", data, function () {
      counter.show();
      btn.addClass("like-btn");
      btn.removeClass("unlike-btn");
      count--;
      if (count === 0) {
        counter.hide();
      } else {
        counter.text(count);
      }
      btn.find(".fa-heart").addClass("fa-heart-o");
      btn.find(".fa-heart-o").removeClass("fa-heart");
    });
  });
});
