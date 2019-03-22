<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use App\Entity\Media;
use App\Service\FileUploader;

class AdminMediaController extends AbstractController
{
    
    /**
     * @Route("admin/media/index", name="media_index")
     */
    public function index()
    {   
        $media = $this->getDoctrine()->getRepository(Media::class)->findBy([], ['createDateTime' => 'DESC']);

        return $this->render('admin/media/index.html.twig', [
            'media' => $media
        ]);
    }

    /** 
     * @Route("admin/media/add", name="media_new")
     */
    public function new(Request $request, FileUploader $fileUploader)
    {          
        $media = new Media();
        $form = $this->createFormBuilder($media)
            ->add('description', TextareaType::class, ['attr' => ['maxlength' => 255], 'required' => false])
            ->add('credit', TextType::class, ['attr' => ['maxlength' => 255], 'required' => false])
            ->add('mediaData', FileType::class, ['label' => 'Choose file to upload', 'required' => true])
            ->add('save', SubmitType::class, ['label' => 'Save changes'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $media = $form->getData();

            $file = $media->getMediaData();
            $fileName = $fileUploader->upload($file);

            $fileArray = explode('.', $fileName);
            $media->setFileName($fileArray[0]);
            $media->setFileExtension($fileArray[1]);
            $media->setCreateDateTime(new \DateTime("now"));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($media);
            $entityManager->flush();
    
            return $this->redirectToRoute('media_index');
        }

        return $this->render('admin/media/form.html.twig', [
            'form' => $form->createView(),
            'title' => "Add new Media"
        ]);
    }

    /**
     * @Route("admin/media/edit/{id}", name="media_edit", requirements={"id"="\d+"})
     */
    public function update(Request $request, FileUploader $fileUploader, $id)
    {   
        $entityManager = $this->getDoctrine()->getManager();

        $media = $this->getDoctrine()->getRepository(Media::class)->find($id);

        if (!$media) {
            throw $this->createNotFoundException('No media found for id ');
        }

        $form = $this->createFormBuilder($media)
        ->add('description', TextareaType::class, ['attr' => ['maxlength' => 255], 'required' => false])
        ->add('credit', TextType::class, ['attr' => ['maxlength' => 255], 'required' => false])
        ->add('mediaData', FileType::class, ['label' => 'Choose file to upload', 'required' => false])
        ->add('save', SubmitType::class, ['label' => 'Save changes'])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $media = $form->getData();
            $file = $media->getMediaData();

            if($file){
                $fileName = $fileUploader->upload($file, $media->getFileName());
                $fileArray = explode('.', $fileName);
                $media->setFileExtension($fileArray[1]);
                $entityManager->persist($media);
            }
            
            $entityManager->persist($media);
            $entityManager->flush();
        
            return $this->redirectToRoute('media_index');
        }

        return $this->render('admin/media/form.html.twig', [
            'form' => $form->createView(),
            'media' => $media,
            'title' => 'Edit media'
        ]);
    }
}