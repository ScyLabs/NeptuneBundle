var Codex = (function(){
    var listingZones = $('.listing_zones_codex');
    var boxCodex = $('.box_codex');
    listingZones.on('click','.box_photo',function () {
        var zone = $(this).parents('.zone');
        $.edc.fancy(zone.data('codexurl')+'/show/'+zone.data('id')+'?token='+zone.data('token'),'iframe');
        $('.fancybox-content').append('<a href="'+zone.data('downloadlink')+'" class="download_zone ajax">Télécharger</a>')
        setTimeout(function () {
            $('.download_zone').neptuneAjaxEvent();
        },500);

    });
    boxCodex.find('.filter').on('change','select',function(){
        var cssSelect = "";
        var tabVal = $(this).val();
        for(var i = 0; i < tabVal.length ; i++){
            if(i > 0)
                cssSelect += ',';
            cssSelect += tabVal[i];
        }
        if(cssSelect === '')
            cssSelect = '.zone';
        listingZones.find('.zone').hide();
        listingZones.find(cssSelect).show();

    })
})();