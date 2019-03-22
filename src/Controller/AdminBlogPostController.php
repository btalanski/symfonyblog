<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Category;
use App\Entity\Media;
use App\Form\Type\MarkdownType;
use App\Service\FileUploader;

class AdminBlogPostController extends AbstractController
{
    /**
     * @Route("admin/posts/index", name="post_index")
     */
    public function index()
    {   
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();

        return $this->render('admin/post/index.html.twig', [
            'posts' => $posts
        ]);
    }

    /**
     * @Route("admin/posts/new", name="post_new")
     */
    public function new(Request $request, FileUploader $fileUploader)
    {
        $post = new Post();

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, ['attr' => ['maxlength' => 1500, 'required' => true]])
            ->add('slug', TextType::class, ['attr' => ['maxlength' => 2000, 'required' => true]])
            ->add('excerpt', TextareaType::class, ['attr' => ['maxlength' => 300, 'required' => true]])
            ->add('text', MarkdownType::class, ['attr' => ['name' => 'markdown_text']])
            ->add('tag', TextType::class)
            ->add('mediaData', FileType::class, ['label' => 'Post media', 'required' => true])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('keywords', TextType::class, ['attr' => ['maxlength' => 500, 'required' => true]])
            ->add('published', ChoiceType::class, [
                'choices'  => [
                    'Draft' => 0,
                    'Published' => 1,
                ],
                'label' => 'Status'
            ])
            ->add('save', SubmitType::class, ['label' => 'Create post'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $post->getMediaData();
            $fileName = $fileUploader->upload($file, null, array('post_highlight'));

            $fileArray = explode('.', $fileName);
            $media = new Media();
            $media->setFileName($fileArray[0]);
            $media->setFileExtension($fileArray[1]);
            $media->setCreateDateTime(new \DateTime("now"));

            $post = $form->getData();
            $post->setCreateDateTime( new \DateTime("now"));
            $post->setMedia($media);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->persist($media);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('admin/post/form.html.twig', [
            'form' => $form->createView(),
            'title' => 'Add new post'
        ]);
    }
    /**
     * @Route("admin/posts/update/{id}", name="post_edit", requirements={"id"="\d+"})
     */
    public function update(Request $request, FileUploader $fileUploader, $id)
    {   
        $entityManager = $this->getDoctrine()->getManager();

        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException('No post found for id ');
        }

        $media = $post->getMedia();

        $form = $this->createFormBuilder($post)
            ->add('title', TextType::class, ['attr' => ['maxlength' => 1500, 'required' => true]])
            ->add('slug', TextType::class, ['attr' => ['maxlength' => 2000, 'required' => true]])
            ->add('excerpt', TextareaType::class, ['attr' => ['maxlength' => 300, 'required' => true]])
            ->add('text', MarkdownType::class, ['attr' => ['name' => 'markdown_text']])
            ->add('tag', TextType::class)
            ->add('mediaData', FileType::class, ['label' => 'Add new Post media', 'required' => false])
            ->add('categories', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'name',
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('keywords', TextType::class, ['attr' => ['maxlength' => 500, 'required' => true]])
            ->add('published', ChoiceType::class, [
                'choices'  => [
                    'Draft' => 0,
                    'Published' => 1,
                ],
                'label' => 'Status'
            ])
            ->add('save', SubmitType::class, ['label' => 'Save changes'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $post = $form->getData();
            $post->setLastUpdateDateTime( new \DateTime("now"));

            $file = $post->getMediaData();

            if($file){
                $media = $post->getMedia();

                $fileName = $fileUploader->upload($file, $media->getFileName(), array('post_highlight'));
                $fileArray = explode('.', $fileName);
                $media->setFileName($fileArray[0]);
                $media->setFileExtension($fileArray[1]);
                $entityManager->persist($media);
            }
            
            $entityManager->persist($post);
            $entityManager->flush();

            return $this->redirectToRoute('post_index');
        }

        return $this->render('admin/post/form.html.twig', [
            'form' => $form->createView(),
            'media' => $media,
            'title' => 'Edit post'
        ]);
    }
}