$(document).ready(function(){
    $(document).on('click','#postComment',function(){
        var comment = $('#commentField').val();
        var tweet_id = $('#commentField').data('tweet');

        var data = {
            comment:comment,
            tweet_id:tweet_id
        }

        if(comment != ''){
            $.post('http://127.0.0.1/twitter/core/ajax/comment.php',data,function(data){
                $('#comments').html(data);
                $('#commentField').val("");
            })
        }
    });
});