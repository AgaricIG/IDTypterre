<?php

namespace App\Form\Admin;

use App\Entity\Uts;
use App\Entity\FicheSuppl;
use App\Entity\Application;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FicheSupplFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
          ->add('application', EntityType::class, [
            'class' => Application::class,
            'choice_label' => 'name',
            'label' => "Pour l'application",

          ])
          ->add('url', TextType::class, [
            'label' => "URL de la fiche",
            'attr' => ['placeholder' => "ex: /pdf/extranet/001.pdf"]
          ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FicheSuppl::class,
        ]);
    }
}
