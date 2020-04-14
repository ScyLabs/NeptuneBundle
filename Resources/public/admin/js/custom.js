/*
Plugins jquery (utilisables depuis $(selector).function() )
ons raccourcies utiles :
$.edc.send() // RequÃªte AJAX avec paramettres
(url,type="GET",data="",function(){})
Ou function = success

$.fancy() avec en paramettres
(data,type="inline",close = false,functio(){})
OÃ¹ , data peut Ãªtre un lien vers (image , video ) ou du HTML.
Pour type -> Se rÃ©fÃ©rer a doc fancy3.
La fonction charge fancybox s'il n'est pas chargÃ©.
Function() est le callback , une fois que la fancy est affichÃ©e.

//////////////////////////////////

Chargement de scripts de faÃ§on automatique :

-Font-awesome PRO si les classes suivantes sont trouvÃ©es :
.fa,.fab,.far,.fal

-Fancybox si la classe suivante est trouvÃ©e : .fancy

- Masonry si .masonry es trouvÃ©e

- Superfish si .superfish est trouvÃ©

- Google recaptcha si #g-recaptcha est trouvÃ©
*/
(function ($){
    var url_asset_admin = url_asset+'/admin';

    $.fn.extend({
        setCustomValidity : function(message){
            this[0].setCustomValidity(message);
            return this;
        },
        checkValidity : function(){
            return this[0].checkValidity();
        }
    });
    /* FIN DEfinition des plugins Jquery*/


    /* DÃ©finition du namespace*/
    $.edc ={
        /* Variables */
        fichiers_fancy:[url_asset_admin+"/css/lib/fancybox/fancybox.min.css",
            url_asset_admin+"/js/lib/fancybox/fancybox.min.js"],
        /* FIN VARIABLES */
        /* Functions */
        send:function(url,type='GET',data='',fn = function(e){}){

            var content = !(typeof(data) == 'object');

            if(typeof(data) == 'function')
            {
                fn = data;
                data = '';
            }
            if(typeof(type) == 'function'){
                fn = type;
                type = 'GET';
            }
            $.ajax(
                {
                    type: type,
                    url: url,
                    data: data,
                    contentType: content,
                    processData: content,
                    success:function(e){
                        fn(e);
                    }
                });
        },
        fancy: function(data,type='inline',close = false,fn = function(e){}){
            var $this = $.edc;
            /* Variable fonction*/
            if(typeof(close) == 'function'){
                fn = close;
                close = false;
            }
            if(typeof(type) == 'function'){
                fn = type;
                type = 'inline';
            }
            var fancy = function(){
                if(close == true)
                    $.fancybox.close();
                $.fancybox.open([{
                    src: data,
                    type: type,
                    opts: {
                        touch: false,
                        afterShow: function(instance , current){
                            fn(instance,current);
                        }
                    }
                }]);
            };

            if($.fn.fancybox)
                fancy();
            else
                $this.loadScript($this.fichiers_fancy,fancy);

        },
        flashAlert: function(text = '',type ='success'){
            var id = 'i'+Math.round(Math.random() * 1000);
            var message = '<div id="'+id+'" class="flash dialog alert alert-'+type+' alert-dismissible fade show">';
            message += '<button type="button" class="close" data-dismiss="alert" aria-la0bel="Close">';
            message += '<span aria-hidden="true"><i class="fa fa-times"></i></span>';
            message += '</button>';
            message += '<p>'+text+'</p>'
            message += '</div>';

            $('#flashes').append(message);
            setTimeout(function () {
                $('#'+id).fadeOut(500);
            },5000)

        }
    };

    if(typeof(loadScript) === typeof('function'))
        $.edc.loadScript = loadScript;
    else
        $.edc.loadScript = function (e,t,n){if("function"==typeof t?(n=t,t=0):void 0===t&&(t=0),"string"==typeof e&&(e=new Array(e)),"object"==typeof e[t]&&($.edc.loadScript(e[t][0],0,e[t][1]),t++),"function"==typeof e[t]&&(e[t](),t++),void 0!==e[t]){var o=new RegExp(/\.js/),i=new RegExp(/\.css/),a=new RegExp(/player_api/);if(t<e.length)if(o.test(e[t])||a.test(e[t])){var c=document.createElement("script");c.src=e[t],c.type="text/javascript",c.defer="defer",document.body.appendChild(c),c.onload=function(){$.edc.loadScript(e,t+1,n)}}else if(i.test(e[t])){var d=document.createElement("link");d.href=e[t],d.rel="stylesheet",d.media="all",document.head.appendChild(d),d.onload=function(){$.edc.loadScript(e,t+1,n)}}else $.edc.loadScript(e,t+1,n)}else"function"==typeof n&&n()};


    $.edc.loadScript($.edc.fichiers_fancy,function(){
        $('.fancy').fancybox();
    });




    $('#tabs').tabs();
    $('#tabs2').tabs();
    $('.tabs').tabs();

    if($('#tabs-produit-1').length){
        var url = window.location.href;
        var exp = url.split('#');
        if(exp.length > 1){
            var anchor = exp[1];
            var type = anchor.split('-');
            if(type.length > 1){
                $('.ui-tabs-anchor[data-type="'+type[1]+'"]').click();
            }

        }
    }

    if($.fn.nestable){
        $('.dd').on('change',function () {
            let ser = JSON.stringify($(this).nestable('serialize'));
            let url = $(this).attr('data-action');
            let data = new FormData();
            data.append('prio',ser);

            if(typeof(url) != typeof(undefined)){
                $.edc.send(url,'POST',data);
            }

        });
    }

    if($.fn.masonry && $('.masonry').length){
        $('.masonry').masonry();
    }
    $('.sortable').sortable({
        stop: function(e,ui){

            let lis = $(this).find('> li');
            let table = new Array();

            for(let i = 0;i < lis.length ;i++){
                if(typeof(lis.eq(i).attr('data-id')) != 'undefined'){
                    table.push(lis.eq(i).attr('data-id'));
                }
                if(i == lis.length -1){
                    let data = new FormData();
                    let type = $(this).data('type');
                    data.append('prio',JSON.stringify(table));
                    data.append('type',type);
                    $.edc.send($(this).attr('data-action'),'POST',data);

                }
            }

        }
    });
    $(".sortable").disableSelection();

    $('#filter_type').on('change',function () {
        let val = $(this).val();
        if(val == 'all'){
            $('#cartouches').find('>li').show();
        }
        else{
            $('#cartouches').find('>li').show()
            $('#cartouches').find('>li:not(.'+val+')').hide();
        }
    })

})(jQuery);





