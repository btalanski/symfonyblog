<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Post;
use App\Entity\Category;

class BlogPostController extends AbstractController
{
    /**
     * @Route("/blog/{id}-{slug}", name="blog_post", requirements={"slug"="[a-z0-9_\-]+", "id"="\d+"})
     */
    public function view(int $id, string $slug)
    {   
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        
        $post = $this->getDoctrine()->getRepository(Post::class)->findOneBy(['slug' => $slug, 'id' => (int)$id]);
        if (!$post) {
            throw $this->createNotFoundException('No post found');
        }

        $media = $post->getMedia();
        $post_categories = $post->getCategories();
        $author = $post->getAuthor();

        $posts = $this->getDoctrine()->getRepository(Post::class)->getRecentPostsArray(3);
        if (!$posts) {
            $posts = array();
        }

        return $this->render('post.html.twig', [
            'post' => $post,
            'posts' => $posts,
            'media' => $media,
            'post_categories' => $post_categories,
            'categories' => $categories,
            'author' => $author,
            'mark' => ""
        ]);
    }

    /**
     * @Route("/blog/{page}", name="blog_index", requirements={"id"="\d+"})
     */
    public function index(int $page = 1)
    {   
        $posts_per_page = 9;
        $postRepository = $this->getDoctrine()->getRepository(Post::class);
        
        $excluded_categories = $this->getDoctrine()->getRepository(Category::class)->getExcludeFromHomeList();
        
        $posts = $postRepository->getPostsPaginated($page, $posts_per_page, $excluded_categories);      
        $total_posts = $postRepository->getPostCount($excluded_categories);
        $total_pages = ceil($total_posts / $posts_per_page);

        return $this->render('posts.html.twig', [
            'posts' => $posts,
            'total_posts' => $total_posts,
            'posts_per_page' => $posts_per_page,
            'total_pages' => $total_pages,
            'page' => $page,
            'categories' => $this->getDoctrine()->getRepository(Category::class)->findAll()
        ]);
    }
}