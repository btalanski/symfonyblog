<?php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class MarkdownType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // hidden fields cannot have a required attribute
            'required' => false,
            // Pass errors to the parent
            'error_bubbling' => true,
            'compound' => false,
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // var_dump($view->vars);
        // die();
        // $name = $form->getName();
        // $view->vars = array_replace($view->vars, [
        //     'name' => "cu"
        // ]);

    }

    public function getParent()
    {
        return TextareaType::class;
    }

    public function getBlockPrefix()
    {
        return 'markdown_editor';
    }
}