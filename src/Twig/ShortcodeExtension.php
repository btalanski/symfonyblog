<?php 
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Symfony\Component\Asset\Packages;
use Maiorano\Shortcodes\Manager\ShortcodeManager;
use Maiorano\Shortcodes\Library\SimpleShortcode;

use App\Utils\Assets;

class ShortcodeExtension extends AbstractExtension
{   
    public function __construct(Packages $assetsManager)
    {
        $this->assets = $assetsManager;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('shortcodes', [$this, 'doShortcodes']),
        ];
    }

    public function doShortcodes($html = '')
    {
        $assets = $this->assets;
        
        $manager = new ShortcodeManager([
            'vplayer' => new SimpleShortcode('vplayer',[
                'placeholder'=>'https://via.placeholder.com/1280x720/570052/FFFFFF/?text=Carregando...',
                'poster' => '',
                'src' => 'youtube',
                'assets' => $assets
            ], function($content=null, array $atts=[]){
                $render = "";
                $poster = "";
                $template = '<div class="vplayer" data-v="##video_url##" data-i="##poster##" data-src="##source##"><img src="##place_holder##" /><a class="plybtn"><span class="is-sr-only">Clique para executar o vídeo</span></a></div>';

                if(!is_null($atts['url']) && $atts['url'] !== ""){
                    if(!is_null($atts['poster']) && $atts['poster'] !== ""){
                        $poster = $atts['assets']->getUrl($atts['poster'], 'uploads');
                    }
                    $render = str_replace('##video_url##', $atts['url'], $template);
                    $render = str_replace('##place_holder##', $atts['placeholder'], $render);
                    $render = str_replace('##poster##', $poster, $render);
                    $render = str_replace('##source##',$atts['src'], $render);
                }

                return $render;
            }),
            'i' => new SimpleShortcode('i',[ 'alt' => '', 'assets' => $assets], function($content=null, array $atts=[]){
                $render = "";

                if(!is_null($atts['src']) && $atts['src'] !== ""){
                    $src = $atts['assets']->getUrl($atts['src'], 'uploads');
                    $template = '<figure class="image is-16by9"><img src="##src##" alt="##alt##" /></figure>';
                    $render = str_replace('##src##', $src, $template);
                    $render = str_replace('##alt##', $atts['alt'], $render);
                }

                return $render;
            }),
            'galery' => new SimpleShortcode('galery',[ 'alt' => '', 'assets' => $assets], function($content=null, array $atts=[]){
                $render = '<div class="glide slides">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">'.$content.'</ul>
                </div>
                <div class="glide__arrows" data-glide-el="controls">
                    <button class="glide__arrow glide__arrow--left" data-glide-dir="<">Anterior</button>
                    <button class="glide__arrow glide__arrow--right" data-glide-dir=">">Próximo</button>
                </div>
                </div>';

                return $render;
            }),

            'galery_slide' => new SimpleShortcode('s',[ 'alt' => '', 'assets' => $assets], function($content=null, array $atts=[]){
                $render = "";
                if(!is_null($atts['src']) && $atts['src'] !== ""){
                    $src = $atts['assets']->getUrl($atts['src'], 'uploads');
                    $template = '<li class="glide__slide"><div class="slide-item"><img src="##src##" alt="##alt##" /><div class="caption"><a href="#">##alt##</a></div></div></li>';
                    $render = str_replace('##src##', $src, $template);
                    $render = str_replace('##alt##', $atts['alt'], $render);
                }

                return $render;
            }),
        ]);

        return $manager->doShortcode($html, null, true);
    }
}