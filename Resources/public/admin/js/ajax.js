var toucheActive = false;
var prevCartouche = false;
var actualSelect = new Array();


$.fn.neptuneAjaxEvent = function(parentObject,parentAction){

    this.on('click',function(e){

        parentObject = (typeof(parentObject) == 'undefined') ? null : parentObject;
        parentAction = (typeof(parentObject) == 'undefined') ? null : parentAction;

        e.preventDefault();
        let action = $(this).attr('href');
        let button = $(this);


        if(action !== null){
            var success = function (result) {
                if($.fn.fancybox){
                    $.fancybox.open([{
                        src: result,
                        type: 'inline',

                        opts: {
                            touch: false,
                            afterShow: function(instance , current){

                                let container =  current.$content;
                                let forms = container.find('form');
                                initialisations(container,instance,current,action);

                                container.find('#valider_liaison').on('click',function (e) {

                                    let sel = container.find('#container_cartouches').attr('data-select');
                                    if(typeof(sel) != 'undefined'){

                                        let element = $('#selection_elements').find('tr.active').eq(0);
                                        let id_elem = element.attr('data-id');
                                        let type_elem = element.attr('data-type');
                                        if(typeof(id_elem) != 'undefined' && typeof(type_elem) != 'udefined'){

                                            /* ON GENERE LE FORMULAIRE ET ON LE SUBMIT*/

                                            let form = $('<form method="post"></form>').attr('action',$(this).attr('data-action'));
                                            $('body').append(form);
                                            form.append($('<input type="hidden" name="selection"/>').attr('value',sel));
                                            form.append($('<input type="hidden" name="id"/>').attr('value',id_elem));
                                            form.append($('<input type="hidden" name="type"/>').attr('value',type_elem));
                                            form.on('submit',function (e) {
                                                e.preventDefault();
                                                let form = $(this);
                                                let data = new FormData(this);

                                                $.edc.send($(this).attr('action'),$(this).attr('method'),data,function (result) {
                                                    $.edc.flashAlert(result.message);
                                                    instance.close();
                                                    $.ajax({
                                                        type:'GET',
                                                        url:action,
                                                        success:success
                                                    });
                                                });
                                            });
                                            form.submit();

                                            /* ON GENERE LE FORMULAIRE ET ON LE SUBMIT*/
                                        }
                                    }

                                });

                                if(forms.length && !button.hasClass('export')){

                                    forms.on('submit',function (e) {
                                        e.preventDefault();
                                        let form = $(this);
                                        let data = new FormData(this);
                                        let nameForm = $(this).attr("name");
                                        $.edc.send($(this).attr('action'),$(this).attr('method'),data,function (result) {
                                            if(result.success === true){
                                                $.fancybox.close();
                                                if(button.hasClass('add') ){

                                                    if(parentObject !== null){
                                                        parentObject.close();
                                                        action = parentAction;
                                                        $.ajax({
                                                            type:'GET',
                                                            url:parentAction,
                                                            success:success
                                                        });
                                                    }else{
                                                        location.reload();
                                                    }
                                                }
                                                else if (forms.find('#form_form_remove').length){
                                                    instance.close();
                                                    action = parentAction;
                                                    $.ajax({
                                                        type:'GET',
                                                        url:action,
                                                        success:success
                                                    });
                                                }
                                                $.edc.flashAlert(result.message);


                                                return;
                                            }
                                            result.errors.forEach(function (key) {
                                                let keyName = Object.keys(key);
                                                let keyValue = Object.values(key)[0][0].message;
                                                let input = nameForm + "_" + keyName;
                                                if(form.find('#'+input).next('.error').length){
                                                    form.find('#'+input).next('.error').empty().append("<div class='error'>" + keyValue + "</div>");
                                                }
                                                else{
                                                    form.find("#" + input).after("<div class='error'>" + keyValue + "</div>");
                                                }
                                            })
                                        })
                                    })
                                }
                            },
                            'beforeClose': function() {
                                tinyMCE.remove('.div-ajax textarea.tiny');
                            }
                        }
                    }]);
                }
            };

            $.ajax({
                type:'GET',
                url:action,
                success: success
            });

        }
    });
};
$('.ajax').neptuneAjaxEvent();


function initialisations(container,instance,current,action){


    container.find('.ajax').neptuneAjaxEvent(instance,action);

    if(container.find('#modification_prio').length){
        let files = $('#selection_elements tr.active').attr('data-files');
        if(typeof(files) != 'undefined'){
            let tab = JSON.parse(files);
            for(let i = 0;i < tab.length  ;i++){
                $('#cartouches > li[data-id="'+tab[i]+'"]').addClass('active');
                if(i == tab.length -1){
                    selection();
                }
            }
        }
    }

    var init_select2 = function(){
        container.find('.select2').select2();
    };
    if($.fn.select2){
        init_select2();
    }
    else{
        $.edc.loadScript([
            url_site + '/bundles/scylabsneptune/admin/css/lib/select2/select2.min.css',
            url_site + '/bundles/scylabsneptune/admin/js/lib/select2/select2.full.min.js',
            url_site + '/bundles/scylabsneptune/admin/js/lib/select2/select2-init.js',
        ],init_select2);
    }
    container.find('#tabs').tabs();
    container.find('#tabs2').tabs();
    container.find('.tabs').tabs();
    container.find('.fancy').fancybox();

    container.find('.nestable:not(.onelevel)').nestable({
        group: 1
    });
    container.find('.nestable.onelevel').nestable({
        maxDepth:1
    });
    $('.dd').on('change',function () {
        let ser = JSON.stringify($(this).nestable('serialize'));
        let url = $(this).attr('data-action');
        let data = new FormData();
        data.append('prio',ser);

        if(typeof(url) != typeof(undefined)){
            $.edc.send(url,'POST',data);
        }

    });
    if(container.find('.dataTable').length){
        container.find('.dataTable').DataTable();
    }
    container.find('#cartouches > li > span').gallery();



    container.find('#filter_type').on('change',function () {
        let val = $(this).val();
        if(val == 'all'){
            container.find('#cartouches').find('>li').show();
        }
        else{
            container.find('#cartouches').find('>li').show()
            container.find('#cartouches').find('>li:not(.'+val+')').hide();
        }
    });

    container.find('.sortable').sortable({
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

    if(container.find('.dropzone').length){
        new Dropzone(container.find('#container_cartouches')[0], { // Make the whole body a dropzone
            dictDefaultMessage: 'Cliquez ou déposez des fichiers ici',
            url:container.find('#container_cartouches').data('action'),
            accept:function(file,done){

                let accepts = [
                    'application/pdf',
                    'image/jpeg',
                    'image/png',
                    'image/svg+xml',
                    'audio/*',
                    'image/gif',
                    'video/mp4',
                    'application/zip',
                    'application/x-7z-compressed',
                    'application/x-rar-compressed',
                ];
                if($.inArray(file.type,accepts) !== -1){
                    done();
                }else{
                    done('Fichier non autorisé');
                }
            },
            success: function(thisfile,done){
                success_dropzone(thisfile,done);

            }
        });
    }

    tinyMCE.init({
        language : "fr_FR",
        selector: '.div-ajax textarea.tiny',
        height: 300,
        menubar: false,
        plugins: [
            'advlist autolink lists link charmap print preview anchor code codesample textcolor',
            'searchreplace visualblocks code fullscreen',
            'insertdatetime table contextmenu paste code help wordcount'
        ],
        toolbar: 'insert | undo redo  | bold italic  | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | code | codesample | help',
        content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tinymce.com/css/codepen.min.css']
    });
}

function success_dropzone(thisfile,done){
    /*Callback appelé quand le fichier est bien envoyé .. (pOur ajouter l'image a la liste ) */
    $.edc.flashAlert('Fichier bien envoyé');

    let file = done;

    let element = $('<li data-id="'+file.id+'" class="relative image col-lg-4 photo" data-type="'+file.type+'"></li>');


    let span = $('<span></span>');
    element.append(span);

    let spanspan = $('<span class="boxing"></span>');
    span.append(spanspan);



    spanspan.append('<i class="fa fa-check centerXY"></i>');

    spanspan.append($('<span class="date">'+file.date+'</span>'));
    let path = file.file;
    let exp  = path.split('.');
    let actions = $('<ul class="actions"></ul>');
    actions.append($('<li><a href="'+url_site+'uploads/'+path+'" class="fancy"><i class="fa fa-search"></i></a></li>'));
    actions.append($('<li>'+file.actions.remove.content+'</li>'));

    element.append(actions);
    if(exp[exp.length -1] == 'pdf'){

        path = 'thumbnails/';
        for(let i = 0; i < exp.length -1;i++ ){
            path += exp[i];
            if(i == exp.length -2 ){
                path += ".jpg";
                img.src = url_site+'uploads/'+path;
            }
        }
    }
    spanspan.append($('<img src="'+url_site+'uploads/'+path+'" />'));
    if($('#cartouches').length)
    {
        $('#cartouches').prepend(element);

        element.find('>span').gallery();
        element.find('.fancy').fancybox();

    }
}
