/* Albumart JS handlers */
var album_tiles = [];

function changeAlbum(album, new_art, type){
    var image = $(".image", album);

    // Fadeout animation
    if(type == 'fadeout' || !type){
        image.animate({
            opacity:0
        }, 500, function(){
            image.css("background-image", "url('albums/" + new_art + "')");
            image.animate({
                opacity: 1
            }, 500, function(){
                // end fadeout
            });
        });
    }else if(type == 'zoomout'){
        image.transition({
            scale:0
        }, function(){
            image.css("background-image", "url('albums/" + new_art + "')");
            image.transition({
                scale:1
            });
        });
    }else if(type == 'rotateX'){
        image.transition({
            rotateX: '90deg'
        }, function(){
            image.css("background-image", "url('albums/" + new_art + "')");
            image.transition({
                rotateX: '0deg'
            });
        });
    }else if(type == 'rotateY'){
        image.transition({
            rotateY: '90deg'
        }, function(){
            image.css("background-image", "url('albums/" + new_art + "')");
            image.transition({
                rotateY: '0deg'
            });
        });
    }
}

var types = ['fadeout', 'zoomout', 'rotateX', 'rotateY'];

$(document).ready(function()
{
});

/*
var cols = 2 + $(document).width() / 160, rows =  2 + $(document).height() / 160;

for(var i=0; i<cols * rows; i++){
    var elem = $('<div class="album" id="album<?php echo $i; ?>">\
          <div class="image" style="background-image:url(\'albums/greatesthits.jpg\');">\
            <a class="darken"></a>\
          </div>\
        </div>');
    album_tiles.push(elem);
    elem.appendTo($('#albumart .albums'));
}

*/