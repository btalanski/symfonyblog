import JVideoBackground from "./libs/video-bg";


$.fn.J_video_background = JVideoBackground;

const videoBackground = new $.fn.J_video_background({
    $el: $('.video-bg'),
    src: 'assets/videos/bg.mp4'
});

$(document).ready(function() {

    // Check for click events on the navbar burger icon
    $(".navbar-burger").click(function() {
  
        // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
        $(".navbar-burger").toggleClass("is-active");
        $(".navbar-menu").toggleClass("is-active");
  
    });
  });