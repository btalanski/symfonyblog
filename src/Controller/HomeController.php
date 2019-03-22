<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Post;
use App\Entity\Category;

class HomeController extends AbstractController
{
    /**
     * @Route("/"), name="site_index"
     */
    public function index()
    {   
        // $slides = array(
        //     array(
        //         "src" => "https://picsum.photos/1280/400",
        //         "caption" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
        //         "target" => "/blog/1-test-1"
        //     ),
        // );
        
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $excluded_categories = $this->getDoctrine()->getRepository(Category::class)->getExcludeFromHomeList();

        $posts = $this->getDoctrine()->getRepository(Post::class)->getRecentPostsArray(9, $excluded_categories);

        return $this->render('home.html.twig', [
            'slides' => null,
            'posts' => $posts,
            'categories' => $categories
        ]);
    }
}