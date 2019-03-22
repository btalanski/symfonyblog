<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Post;
use App\Entity\Category;

class BlogCategoryController extends AbstractController
{
    /**
     * @Route("/blog/categoria/{slug}/{page}", name="blog_category", requirements={"slug"="[a-z0-9_\-]+", "page"="\d+"})
     */
    public function index(Request $request, string $slug, int $page = 1)
    {   
        $posts_per_page = 9;

        $current_category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['slug' => $slug]);
        
        if (!$current_category) {
            throw $this->createNotFoundException('No category found');
        }

        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        $posts = $postRepository->getPostsPaginatedByCategory($page, $posts_per_page, $current_category->getId());      
        $total_posts = $postRepository->getPostCountByCategory($current_category->getId());
        $total_pages = ceil($total_posts / $posts_per_page);

        return $this->render('category.html.twig', [
            'category' => $current_category,
            'posts' => $posts,
            'total_posts' => $total_posts,
            'posts_per_page' => $posts_per_page,
            'total_pages' => $total_pages,
            'page' => $page,
            'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()
        ]);
    }
}