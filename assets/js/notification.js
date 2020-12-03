notification = function () {
    $.get('http://127.0.0.1/twitter/core/ajax/notification.php', { showNotification: true }, function (data) {
        if (data) {
            if (data.notification > 0) {
                $('#notification').addClass('span-i');
                $('#notification').html(data.notification);
            }
            if (data.messages > 0) {
                $('#messages').addClass('span-i');
                $('#messages').html(data.messages);
            }
        }
    }, 'json');
}

setInterval(notification, 10000);