var dataTable = null;
/*Click sur une TR dans le tableu de listing ...*/
var tabs = $('#tabs_locked').tabs({'disabled':[1,2]});

$('#selection_elements table tbody tr').on('click',function(){

    if(!$(this).hasClass('active')){
        let action = $(this).attr('data-action');
        if(typeof(action) !== 'undefined'){
            $.edc.send(action,'GET','',function (e) {
                let response = JSON.parse(e);
                if(!$('#initial_message').hasClass('hide')){
                    $('#initial_message').addClass('hide').hide();
                }

                if(dataTable !== null){
                    dataTable.destroy();
                    dataTable = null;
                }
                $('#body_tab_objects').empty();
                $('#objects').empty();
                for(let i = 0;i < response.length;i++){
                    $('#objects').append(
                        '<li class="dd-item" data-id="'+response[i].object.id+'">\n' +
                        '<div class="dd-handle">'+
                        '<a href="'+response[i].actions.active+'">' +
                        ((response[i].object.active) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>') +
                        response[i].object.name+
                        '</div>\n' +
                        '</li>'
                    );
                    $('#body_tab_objects').append(
                        '<tr>' +
                        '<td>' +
                        '<a href="'+response[i].actions.active+'">' +
                        ((response[i].object.active) ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>') +
                        '</a>'+
                        '</td>'+
                        '<td>'+response[i].object.name+'</td>'+
                        '<td>' +
                        '<ul class="actions">' +
                        '<li><a href="'+response[i].actions.gallery+'" title="Gestion des fichiers"><i class="fa fa-file-alt"></i></a></li>' +
                        '<li><a href="'+response[i].actions.detail+'" title="Gestion des details"><i class="fa fa-newspaper"></i></a></li>' +
                        '<li><a href="'+response[i].actions.edit+'" title="Modifier"><i class="fa fa-pencil"></i></a></li>' +
                        '<li>'+response[i].actions.remove.content+'</li>'+
                        '</ul>' +
                        '</td>'+
                        '</tr>');


                    if(i == (response.length -1)){
                        dataTable = $('#objects_datatable').DataTable();
                    }
                }


            });
            zonesAndElements($(this));
        }
    }
    $('#selection_elements table tr').removeClass('active');
    $(this).toggleClass('active');
});
$('#button_add').on('click',function (e) {
    e.preventDefault();
    let action = $(this).data('action');
    let tr = $('#selection_elements table tbody tr.active');
    if(tr.length == 0){
        $.edc.flashAlert('Veuillez selectionner au moins un element auquel ajouter une zone','danger');
    }
    else{
        tr = tr.eq(0);
        if(typeof(tr.data('type')) != 'undefined' && typeof(tr.data('id')) !='undefined'){
            window.location.href = action+'/'+tr.data('type')+'/'+tr.data('id');
        }

    }


});


function zonesAndElements(elem){


    let type = elem.attr('data-type');
    let zones = JSON.parse(elem.attr('data-zones'));
    var elements = null;
    if(typeof(elem.attr('data-elements')) != 'undefined'){
        elements = JSON.parse(elem.attr('data-elements'));
    }


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
        if(elements != null){

            for(let i = 0; i < elements.length;i++){
                let id = elements[i];
                $('#selection_elements table tbody tr[data-id="'+id+'"][data-type="element"]').removeClass('none');
            }
        }


    }


}