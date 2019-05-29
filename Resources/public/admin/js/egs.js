var egg = '';
var encours = false;
$(window).on('keyup',function(e){

    let key = e.originalEvent.key;
    egg += key;

    if (key == 'ArrowUp') {
        $('#keyup').append('<i class="fa fa-arrow-up"></i>');
    }
    else if (key == 'ArrowDown'){
        $('#keyup').append('<i class="fa fa-arrow-down"></i>');
    }
    else if(key == 'ArrowRight'){
        $('#keyup').append('<i class="fa fa-arrow-right"></i>');
    }
    else if (key == 'ArrowLeft'){
        $('#keyup').append('<i class="fa fa-arrow-left"></i>');
    }
    else {
        $('#keyup').append(key);
    }

    if(egg == 'ArrowUpArrowUpArrowDownArrowDownArrowLeftArrowRight' || egg == 'soulpaprika'){
        encours = false;

        window.location.href = url_asset+'/eggs/runner.html';
        $.edc.send(url_asset+'/eggs/runner.html','GET','',function (e) {
            $('html').empty().append(e);
        })

    }
    else if (egg == 'nyan cat'){
        audio('nyan.ogg');
        let img = document.createElement('img');
        img.src = url_asset+'/eggs/nyan.gif';
        img.id = 'image_suit_souris';
        img.style = "position:fixed;z-index:99999;display:block;";
        document.body.appendChild(img);
        setTimeout(function () {
            document.onmousemove = suitsouris;
        },200);

    }
    else if (egg == 'ninochess'){
        video('https://lichess.org/');
    }
    else if (egg == 'meuporg'){
        audio('meuporg.ogg');
    }
    else if (egg == 'piumaria'){
        audio('shark.ogg');
    }
    else if (egg == 'zerg rush'){
        video('https://www.google.fr/search?source=hp&ei=ct_nXOjgOqmcjLsP1pGw2AQ&q=zerg+rush&oq=zerg+rush&gs_l=psy-ab.12..35i39j0i67j0l8.1077.3178..3426...1.0..0.128.772.10j1......0....1..gws-wiz.....0..0i131j0i131i67j0i10j0i20i263.PNMuT7OVc7Q');
    }
    else if (egg == 'resident evil'){
        audio('mr-x.ogg');

        video(url_asset+'/eggs/concentr.gif','image');



        setTimeout(function () {
            audio('cri.ogg')
            video(url_asset+'/eggs/x.png','image');
        },10000);
    }
    else if(egg == 'quentin pierotti'){
        audio('barbie.mp3');
    }
    if(encours === false){

        encours = true;
        setTimeout(function(){
            $('#keyup').empty();
            encours = false;
            egg = '';
        },5000);
    }
});
function audio(src= '',type ='ogg'){
    let audio = document.createElement('audio');
    audio.controls = 'controls';
    let source = document.createElement('source');
    source.src = url_asset+'/eggs/'+src;
    console.log(source.src)
    source.type = "audio/"+type;
    audio.appendChild(source);
    document.body.appendChild(audio);
    audio.play();
}
function video(src = '',type ='iframe'){
    $.edc.fancy(src,type);
}


function suitsouris(event)
{
    var x = event.x+document.body.scrollLeft;
    var y = event.y+document.body.scrollTop;

    document.getElementById("image_suit_souris").style.left = (x+1)+'px';
    document.getElementById("image_suit_souris").style.top  = (y+1)+'px';
}