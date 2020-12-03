$(document).ready(function(){
    $(document).on('click','.deleteTweet',function(){
        var tweet_id = $(this).data('tweet');
        
        $.post('http://127.0.0.1/twitter/core/ajax/deleteTweet.php',{showPopup:tweet_id},function(data){
       $('.popupTweet').html(data); 
         $('.close-retweet-popup,.cancel-it').click(function(){
           $('.retweet-popup').hide(); 
        });
            
        $(document).on('click', '.delete-it',function(){
           $.post('http://127.0.0.1/twitter/core/ajax/deleteTweet.php',{deleteTweet:tweet_id},function(){
              $('.retweet-popup').hide();
              location.reload();
           }); 
        });
            
    });
        
    });
                   
    $(document).on('click','.deleteComment',function(){
       var commentID = $(this).data('comment');
       var tweet_id =  $(this).data('tweet');    
        var data = {
          deleteComment:commentID    
        }
        $.post('http://127.0.0.1/twitter/core/ajax/deleteComment.php',data,function(){
          $.post('http://127.0.0.1/twitter/core/ajax/popuptweets.php',{showpopup:tweet_id},function(data){
            $('.popupTweet').html(data);
            $('.tweet-show-popup-box-cut').click(function(){
                $('.tweet-show-popup-wrap').hide();
            })
        });  
        });
    });
});