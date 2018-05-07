$(document).foundation()
$(function() {
        var demo1 = $("#slider").slippry({

          // options
          adaptiveHeight: false, // height of the sliders adapts to current slide
          captions: false, // Position: overlay, below, custom, false

          // pager
          pager: false,

          // controls
          controls: false,
          autoHover: false,

          // transitions
          transition: 'kenburns', // fade, horizontal, kenburns, false
          kenZoom: 100,
          speed: 2000 // time the transition takes (ms)

        });
    });