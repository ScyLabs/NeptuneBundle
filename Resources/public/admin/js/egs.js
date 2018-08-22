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

        window.location.href = url_eggs+'/eggs/runner.html';
        $.edc.send(url_eggs+'/eggs/runner.html','GET','',function (e) {
            $('html').empty().append(e);
        })

    }
    else if (egg == 'nyan cat'){
        audio('nyan.ogg');
        let img = document.createElement('img');
        img.src = url_eggs+'/eggs/nyan.gif';
        img.id = 'image_suit_souris';
        img.style = "position:fixed;z-index:99999;display:block;";
        document.body.appendChild(img);
        setTimeout(function () {
            document.onmousemove = suitsouris;
        },200);

    }
    else if (egg == 'meuporg'){
        audio('meuporg.ogg');
    }
    else if (egg == 'caramel song'){
        video('https://www.youtube.com/watch?v=A67ZkAd1wmI');
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
    source.src = url_eggs+'/eggs/'+src;
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