<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use App\Entity\Category;

class AdminCategoryController extends AbstractController
{
    /**
     * @Route("admin/categories/index", name="category_index")
     */
    public function index()
    {   
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("admin/categories/new", name="category_new")
     */
    public function new(Request $request)
    {
        $category = new Category();

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, ['attr' => ['maxlength' => 200, 'required' => true]])
            ->add('slug', TextType::class, ['attr' => ['maxlength' => 255, 'required' => true]])
            ->add('description', TextareaType::class, ['attr' => ['maxlength' => 255]])
            ->add('exclude_from_index', CheckboxType::class, ['label' => 'Exclude from Home and Post index','required' => false])
            ->add('keywords', TextType::class, ['attr' => ['maxlength' => 255]])
            ->add('save', SubmitType::class, ['label' => 'Create category'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $category = $form->getData();
            $category->setCreateDateTime( new \DateTime("now"));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
    
            return $this->redirectToRoute('category_index');
        }

        return $this->render('admin/category/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/categories/edit/{id}", name="category_edit", requirements={"id"="\d+"})
     */
    public function update(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findOneBy(['id' => $id]);
        
        if (!$category) {
            throw $this->createNotFoundException('No category found');
        }

        $form = $this->createFormBuilder($category)
            ->add('name', TextType::class, ['attr' => ['maxlength' => 200, 'required' => true]])
            ->add('slug', TextType::class, ['attr' => ['maxlength' => 255, 'required' => true]])
            ->add('description', TextareaType::class, ['attr' => ['maxlength' => 255]])
            ->add('exclude_from_index', CheckboxType::class, ['label' => 'Exclude from Home and Post index','required' => false])
            ->add('keywords', TextType::class, ['attr' => ['maxlength' => 255]])
            ->add('save', SubmitType::class, ['label' => 'Save changes'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $category = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();
    
            return $this->redirectToRoute('category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}