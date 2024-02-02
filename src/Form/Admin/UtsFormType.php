<?php

namespace App\Form\Admin;

use App\Entity\Uts;
use Symfony\Component\Form\FormEvent;
use App\Form\Admin\FicheSupplFormType;
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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UtsFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', TextType::class, [
              'label' => 'ID',
              'required' => false,
              'attr' => ['placeholder' => "ex: 200_1"],
            ])
            ->add('name', TextType::class, [
              'label' => "Nom",
              'attr' => ['placeholder' => "ex: Argile limoneuse, peu caillouteuse, profonde, calcique, hydromorphe des collines sous-vosgiennes"],
            ])
            ->add('fiche', TextType::class, [
              'label' => "URL de la fiche publique",
              'required' => false,
              'attr' => ['placeholder' => "ex: /pdf/fiche_08532.pdf"],
            ])
            ->add('ficheSuppls', CollectionType::class, [
              'entry_type' => FicheSupplFormType::class,
              'entry_options' => ['label' => false],
              'allow_add' => true,
              'by_reference' => false,
              'allow_delete' => true,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $entity = $event->getData();
            $form = $event->getForm();

            // auto prefix the ID when entity is new
            if (!$entity || null === $entity->getId()) {
                $form->add('id', TextType::class, [
                  'label' => 'ID',
                  'required' => false,
                  'data' => $_ENV['UTS_ID_PREFIX']
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Uts::class,
        ]);
    }
}
