import Glide from '@glidejs/glide'

const slider = document.querySelectorAll(".glide");
if (slider.length > 0) {
    new Glide('.glide').mount()
}
