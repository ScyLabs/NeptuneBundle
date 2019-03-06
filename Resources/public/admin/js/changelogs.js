$.ajax({
    url: url_changelog,
    type: 'GET',
    success: function (result) {

        if(result.active == true){
            var popup = $('<div class="popup"></div>');
            popup.append('<h1>'+result.title+'</h1>');
            var changes = result.changes;
            $.each(changes,function (key,change) {
                popup.append('<p>'+change+'</p>')
            });
            popup.append('<a href="'+result.cookieAction+'" class="btn" style="max-width:200px;">J\'ai compris</a>')
            $.edc.fancy(popup,'inline',false,function(result){
            });


        }
    }
});
