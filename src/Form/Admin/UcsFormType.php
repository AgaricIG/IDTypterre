<?php

namespace App\Form\Admin;

use App\Entity\Ucs;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class UcsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, [
              'label' => 'ID',
              'required' => false,
              'attr' => ['placeholder' => "ex: 001"],
            ])
            ->add('name', TextType::class, [
              'label' => "Libellé",
              'attr' => ['placeholder' => "ex: Vallees des rivieres vosgiennes (Centre et Sud)"],
            ])
            ->add('geom', TextareaType::class, [
              'label' => "Géométrie",
              'required' => false,
              'attr' => ['style' => "min-height:400px;font-familly:monospace;font-size:11px"]
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            $form = $event->getForm();

            // auto prefix the ID when entity is new
            if (null === $entity->getId() && !empty($_ENV['UCS_ID_PREFIX'])) {
                $form->add('id', TextType::class, [
                  'label' => 'ID',
                  'required' => false,
                  'data' => $_ENV['UCS_ID_PREFIX']
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ucs::class,
        ]);
    }
}
