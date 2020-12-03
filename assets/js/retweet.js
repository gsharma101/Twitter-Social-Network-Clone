$(document).ready(function(){
    $(document).on('click','.retweet',function(){
        $tweet_id = $(this).data('tweet');
        $user_id = $(this).data('user');
        $counter = $(this).find('.retweetCounter');
        $count = $counter.text();
        $button = $(this);
        var data = {
          showPopup:$tweet_id,
          user_id:$user_id
        }
        $.post('http://127.0.0.1/twitter/ajax/retweet.php',data,function(data){
            $('.popupTweet').html(data);
            $('.close-retweet-popup').click(function(){
                $('.retweet-popup').hide();
            })
        });
    });

    $(document).on('click','.retweet-it',function(){
        var comment = $('.retweetMsg').val();
        var retweetdData = {
            retweet:$tweet_id,
            user_id:$user_id,
            comment:comment
          }
        $.post('http://127.0.0.1/twitter/retweet.php',retweetdData,function(){
            $('.retweet-popup').hide();
            $count++;
            $counter.text($count);
            $button.removeClass('retweet').addClass('retweeted');
        });
    });
});