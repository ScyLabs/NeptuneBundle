$.ajax(url_changelog,{
    type: 'GET',
    success: function (result) {
        console.log(result);
        if(result.active === true){
            var popup = $('<div class="popup"></div>');
            popup.append('<h1>'+result.title+'</h1>');
            var changes = result.changes;

            $.each(changes,function (key,change) {
                popup.append('<p>'+change+'</p>')
            });
            var fancy = function(){

                $.edc.fancy(popup,'inline',false,function(result){

                });
            }
            if($.fn.fancybox){
                fancy();
            }else{

            }
        }
    }
});