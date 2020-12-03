$(document).ready(function () {
  $(".search").keyup(function () {
    var search = $(this).val();
    $.post("http://127.0.0.1/twitter/core/ajax/search.php", { search: search }, function (
      data
    ) {
      $(".search-result").html(data);
    });
  });
  $(document).on("keyup", ".search-user", function () {
    $(".message-resent").hide();
    var search = $(this).val();
    $.post(
      "http://127.0.0.1/twitter/core/ajax/searchUserInMsg.php",
      { search: search },
      function (data) {
        $(".message-body").html(data);
      }
    );
  });
});
