var toucheActive = false;
var prevCartouche = false;
var actualSelect = new Array();

var tabs = $('#tabs').tabs({'disabled':[1,2]});



/* Enregistrement de la touche enfoncée (Pour shift + Click) */
$(window).on('keydown',function(e){
    toucheActive = e.originalEvent.key;
});
$(window).on('keyup',function(e){
    toucheActive = false;
});
/* FIN de l'enregistrement de la touche*/


/*Affichage des pages , zones , et element , pour liaison */
$('#lier').on('click',function () {
    $('#container_cartouches').toggleClass('col-md-6').toggleClass('col-md-10');
    $('#gallery_actions').toggleClass('col-md-2').toggleClass('col-md-6');
    $('#selection_elements').slideToggle(500);
});
/* FIN AFFICHAGE ...*/

/*Click sur une TR dans le tableu de listing des pages ...*/
$('#selection_elements table tbody tr').on('click',function(){

    $('#selection_elements table tr').removeClass('active');
    $(this).toggleClass('active');

    /* On supprime la classe Active sur toutes les cartouches photos*/
    $('#cartouches').find('>li').removeClass('active');

    if($(this).attr('data-files') != ''){
        /*Est-ce qu'il y a des fichiers liés à la l'élément selectionné ? */
        let data = $(this).attr('data-files');
        let files = JSON.parse(data);

        /*On parcours le tableau (JSON) récupéré , et on ajoute la classe active aux fichiers concernés*/
        for(let i = 0;i < files.length;i++){
            $('#cartouches').find('>li[data-id="'+files[i]+'"]').addClass('active');
            if(i == (files.length -1)){
                selection();
            }
        }
    }
    zonesAndElements($(this));
    /*On parcours le tableau enregistré plus haut (Selection de la session ) . Et on réafecte (pour enregistrer la selection)*/
    for(let i = 0; i< actualSelect.length;i++){
        $('#cartouches').find('>li[data-id="'+actualSelect[i]+'"]').addClass('active');
        if(i == (actualSelect.length -1)){
            /* Fonction qui récupère les cartouches actives et rentre les ids dans un JSON data-selection=""*/
            selection();
        }
    }

});


function zonesAndElements(elem){

    let type = elem.attr('data-type');
    let zones = elem.attr('data-zones');
    let elements = elem.attr('data-elements');
    if(type == 'page'){
        if(tabs.find('.ui-tabs-tab').hasClass('ui-state-disabled')){
            tabs.tabs('enable')
        }
        $('#selection_elements table tbody tr[data-type="zone"]').addClass('none');
        $('#selection_elements table tbody tr[data-type="element"]').addClass('none');
        for(let i = 0; i < zones.length;i++){
            let id = zones[i];
            $('#selection_elements table tbody tr[data-id="'+id+'"][data-type="zone"]').removeClass('none');
        }
        for(let i = 0; i < elements.length;i++){
            let id = elements[i];
            $('#selection_elements table tbody tr[data-id="'+id+'"][data-type="element"]').removeClass('none');
        }


    }


}
/* Extension jquery pour affecté le on('click) d'action de cartouche. sur un element selectionné */
$.fn.gallery = function(e){
    $(this).on('click',function (e) {
        let parent = $(this).parent('#cartouches');
        let tab = new Array();
        let nb = parent.find('> li.active').length;

        /*On affecte le tableau tab (remplis par selection() . Dans data-select de #container_cartouches
        * */
        var callback = function(){
            if(nb == 0){
                $('#container_cartouches').attr('data-select',JSON.stringify(tab));
            }
            else{
                selection();
            }
        };

        /*Si la touche active (remplie plus haut ) est "Shift" . Alors , on enclenche la selection de toutes les cartouches entre les deux*/
        if(toucheActive === 'Shift'){
            if(prevCartouche !== false){
                /* Premier .eq() */
                let start = (prevCartouche.index() < $(this).index()) ? prevCartouche.index() : $(this).index();
                /* Denrier eq() de la selection*/
                let end = (prevCartouche.index() < $(this).index()) ? $(this).index() : prevCartouche.index();

                /*S'il y a  >=  de cartouches que le selection (pour eviter certains bugs) */
                if(parent.find('> li').length >= end){
                    /*On parcours les cartouches */
                    for(let j = start  ; j <= end   ;j++){
                        let li = parent.find('>li').eq(j);
                        if(prevCartouche.hasClass('active')){
                            /*Si la dernière cartouche selectionnée est active (on actives toutes les cartouches entre */
                            li.addClass('active');
                            let index = actualSelect.indexOf(li.attr('data-id'));
                            if(index < 0){
                                actualSelect.push(li.attr('data-id'));
                            }
                        }
                        else{
                            /*Si la dernière cartouche sélectionnée est désactivée , on desactive toutes les cartouches entre*/
                            li.removeClass('active');
                            let index = actualSelect.indexOf(li.attr('data-id'));
                            if(index > -1){
                                actualSelect.splice(index,1);
                            }
                        }

                        if(j == end){
                            /*Quand on est a la dernière cartouche . On récupère le nombre , et on lance le callback*/
                            nb  = parent.find('> li.active').length;
                            callback();
                        }
                    }
                }
            }
        }
        else{
            /*Si la touche SHIFT n'est pas pressée*/
            $(this).toggleClass('active');
            nb = parent.find('>li.active').length;
            if($(this).hasClass('active')){
                /*On ajoute l'ID au tableau de selection*/
                actualSelect.push($(this).attr('data-id'));
            }
            else{
                /*On supprime lID du tableau de election*/
                let index = actualSelect.indexOf($(this).attr('data-id'));
                if(index > -1){
                    actualSelect.splice(index,1);
                }
            }
            callback();
        }
        /*On affecte la cartouche précedente*/
        prevCartouche = $(this);
    })

};

$('#cartouches > li').gallery();

/*Si on clique sur le bouton d'envoi de formulaire .*/
$('#valider_liaison').on('click',function (e) {
    if(!$('#selection_elements').find('tr.active').length){
        /* Si il n'y a pas de TR active dans le tableau*/
        $.edc.flashAlert('Choisissez au moins un element sur lequel lier votre Selection','danger');
    }
    else{
        let sel = $('#container_cartouches').attr('data-select');
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
                form.submit();

                /* ON GENERE LE FORMULAIRE ET ON LE SUBMIT*/
            }
        }
    }

});


/*On met a jour le JSON correspondant a la selection*/
function selection(){
    let tab = new Array();
    let i = 0;
    let nb = $('#cartouches').find('>li.active').length;
    $('#cartouches').find('> li.active').each(function (e) {
        /* On parcours toutes les cartouches actives et on affecte au tableau*/
        i++;
        let data = $(this).attr('data-id');
        if(typeof(data) != 'undefined'){
            tab.push(data);
        }
        if(i == nb){
            $('#container_cartouches').attr('data-select',JSON.stringify(tab));
        }
    });
}


/*Configuration de DROPZONE*/
Dropzone.options.customdropzone = {
    dictDefaultMessage: 'Cliquez ou déposez des fichiers ici',
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
        /*Callback appelé quand le fichier est bien envoyé .. (pOur ajouter l'image a la liste ) */
        $.edc.flashAlert('Fichier bien envoyé');

        let file = done;

        let element = $('<li data-id="'+file.id+'" class="relative image col-lg-2 photo" data-type="'+file.type+'"></li>');

        let span = $('<span></span>');
        element.append(span);

        let spanspan = $('<span class="boxing"></span>');
        span.append(spanspan);

        spanspan.append(file.actions.remove.content);

        spanspan.append('<i class="fa fa-check centerXY"></i>');


        let img = spanspan.append($('<img/>'));
        spanspan.append($('<span class="date">'+file.date+'</span>'));
        let path = file.file;
        let exp  = path.split('.');

        if(exp[exp.length -1] == 'pdf'){

            path = 'thumbnails/';
            for(let i = 0; i < exp.length -1;i++ ){
                path += exp[i];
                if(i == exp.length -2 ){
                    path += ".jpg";
                    img.src = url_site+'/uploads/'+path;
                }
            }
        }
        spanspan.append($('<img src="'+url_site+'/uploads/'+path+'" />'));
        if($('#cartouches').length)
        {
            $('#cartouches').prepend(element);

            element.gallery();

        }


    }
};

/*FIN Configuration de DROPZONE*/

/* Sur page de gestion des prios . On ajoute la classe active aux fichiers actifs sur la page */
if($('#modification_prio').length){
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