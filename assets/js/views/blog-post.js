import "rrssb/js/rrssb.min.js";
import Glide from '@glidejs/glide'


$('.rrssb-buttons').rrssb({
    title: $('.rrssb-buttons').data('title'),
    url: $('.rrssb-buttons').data('url'),
});

const slider = document.querySelectorAll(".glide");
if (slider.length > 0) {
    new Glide('.glide').mount();
}

const vplayer = document.querySelectorAll(".vplayer");
if (vplayer.length > 0) {
    for (var i = 0; i < vplayer.length; i++) {

        const poster = vplayer[i].dataset.i;
        if (!!poster) {
            const $poster = new Image();
            $poster.src = poster;
            $poster.addEventListener("load", function () {
                const $img = vplayer[i].querySelector('img');
                vplayer[i].removeChild($img);
                vplayer[i].appendChild($poster);
            }(i));
        }

        vplayer[i].addEventListener("click", function () {
            const source = this.dataset.src;
            const url = this.dataset.v;

            switch (this.dataset.src) {
                case "youtube":
                    const regExp = /^.*(youtu\.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                    const match = url.match(regExp);

                    if (match && match[2].length == 11) {
                        const iframe = document.createElement("iframe");
                        iframe.setAttribute("allowfullscreen", "");
                        iframe.setAttribute("frameborder", "0");
                        iframe.setAttribute("src", "https://www.youtube.com/embed/" + match[2] + "?rel=0&showinfo=1&autoplay=1");

                        this.innerHTML = "";
                        this.appendChild(iframe);
                    }
                    break;
                case "pornhub":
                    const iframe = document.createElement("iframe");
                    iframe.setAttribute("allowfullscreen", "");
                    iframe.setAttribute("scrolling", "no");
                    iframe.setAttribute("frameborder", "0");
                    iframe.setAttribute("src", url);

                    this.innerHTML = "";
                    this.appendChild(iframe);
            }
        });
    };
};