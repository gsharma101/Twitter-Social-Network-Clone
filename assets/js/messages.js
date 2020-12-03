$(document).ready(function () {
  $(document).on("click", "#messagePopup", function () {
    var getMessages = 1;
    var data = {
      showMessage: getMessages,
    };
    $.post("http://127.0.0.1/twitter/core/ajax/messages.php", data, function (data) {
      $(".popupTweet").html(data);
    });
  });

  $(document).on("click", ".people-message", function () {
    var get_id = $(this).data("user");
    $.post(
      "http://127.0.0.1/twitter/core/ajax/messages.php",
      { showChatPopup: get_id },
      function (data) {
        $(".popupTweet").html(data);
        if (autoscroll) {
          scrolldown();
        }
        $("#chat").on("scroll", function () {
          if ($(this).scrollTop() < this.scrollHeight - $(this.height())) {
            autoscroll = false;
          } else {
            autoscroll = true;
          }
        });
        $(".close-msgPopup").click(function () {
          clearInterval(timer);
        });
      }
    );
    getMessages = function () {
      $.post(
        "http://127.0.0.1/twitter/core/ajax/messages.php",
        { showChatMessage: get_id },
        function (data) {
          $(".main-msg-inner").html(data);
          if (autoscroll) {
            scrolldown();
          }
          $("#chat").on("scroll", function () {
            if ($(this).scrollTop() < this.scrollHeight - $(this.height())) {
              autoscroll = false;
            } else {
              autoscroll = true;
            }
          });

          $(".close-msgPopup").click(function () {
            clearInterval(timer);
          });
        }
      );
    };
    var timer = setInterval(getMessages, 1000);
    getMessages();

    autoscroll = true;
    scrolldown = function () {
      $("#chat").scrollTop($("#chat")[0].scrollHeight);
    };
    $(document).on("click", ".back-messages", function () {
      var getMessages = 1;
      var data = {
        showMessage: getMessages,
      };
      $.post("http://127.0.0.1/twitter/core/ajax/messages.php", data, function (data) {
        $(".popupTweet").html(data);
        clearInterval(timer);
      });
    });
    $(document).on("click", ".deleteMsg", function () {
      var messageID = $(this).data('message');
      $('.message-del-inner').height('100px');
      $(document).on('click', '.cancel', function () {
        $('.message-del-inner').height('0px');
      });
      $(document).on('click', '.delete', function () {
        $.post("http://127.0.0.1/twitter/core/ajax/messages.php", { deleteMsg: messageID }, function (data) {
          $('.message-del-inner').height('0px');
          getMessages();
        });
      });
    });
  });
});
