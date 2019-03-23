import Glide from '@glidejs/glide'


$(".navbar-burger").click(function () {
    $(".navbar-burger").toggleClass("is-active");
    $(".navbar-menu").toggleClass("is-active");
});

const slider = document.querySelectorAll(".glide");
if (slider.length > 0) {
    new Glide('.glide').mount()
}

