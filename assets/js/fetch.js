$(document).ready(function(){
    var win = $(window);
    var offset = 3;

    win.scroll(function(){
        if($(document).height() <= (win.height() + win.scrollTop())){
            offset +=3;
            $('#loader').show();
            $.post('http://127.0.0.1/twitter/core/ajax/fetchPost.php',{fetchPost:offset},function(data){
                $('.tweets').html(data);
                $('#loader').hide();
            });
        }
    });
});