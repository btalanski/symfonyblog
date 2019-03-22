<?php
namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use App\Entity\User;

class AdminUsersController extends AbstractController
{
    /**
     * @Route("admin/users/index", name="user_index")
     */
    public function index()
    {         
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        if (!$users) {
            $users = array();
        }

        return $this->render('admin/user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("admin/users/new", name="user_new")
     */
    public function new(Request $request, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, ['attr' => ['maxlength' => 255, 'required' => true]])
            ->add('email', EmailType::class, ['attr' => ['maxlength' => 255, 'required' => true]])
            ->add('username', TextType::class, ['attr' => ['maxlength' => 255, 'required' => true]])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('userRoles', ChoiceType::class, [
                'choices'  => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Add user'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setCreateDateTime(new \DateTime("now"));
            
            $password = $user->getPassword();
            $encodedPassword = $encoder->encodePassword($user, $password);
            
            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("admin/users/edit/{id}", name="user_edit", requirements={"id"="\d+"})
     */
    public function update(Request $request, UserPasswordEncoderInterface $encoder, $id)
    {   
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $id]);

        $form = $this->createFormBuilder($user)
            ->add('name', TextType::class, ['attr' => ['maxlength' => 255, 'required' => true]])
            ->add('email', EmailType::class, ['attr' => ['maxlength' => 255, 'required' => true]])
            ->add('username', TextType::class, ['attr' => ['maxlength' => 255, 'required' => true]])
            ->add('userRoles', ChoiceType::class, [
                'choices'  => [
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Save changes'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setCreateDateTime(new \DateTime("now"));
            
            $password = $user->getPassword();
            $encodedPassword = $encoder->encodePassword($user, $password);
            
            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
    
            return $this->redirectToRoute('user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}