<?php

namespace App\Form\Admin;

use App\Entity\Media;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MediaFormType extends AbstractType
{
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class, [
              'required' => false,
            ])
            ->add('description', TextType::class, [
              'attr' => ['placeholder' => "ex: 	Estimation texture de sol"],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => ["Schéma, photo, scan" => "image", "Vidéo" => "video", "Aucun" => ''],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('url', TextType::class, [
              'required' => false,
              'label' => "URL",
              'attr' => ['placeholder' => "ex: 	/images/texture_sol.png"],
            ])
            ->add('file', FileType::class, [
              'mapped' => false,
              'label' => "Fichier",
              'required' => false,
              'attr' => array(
                        'accept' => 'image/*',
              ),
            ])
        ;

        $builder->addEventListener(FormEvents::SUBMIT, [$this, 'onSubmit']);
    }

    public function onSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        if ($form->get('file')->getData()) {
            $image = $form->get('file')->getData();
            $newFilename = $image->getClientOriginalName();
            $newFilename = strtolower($newFilename);
            $dir = $this->params->get('kernel.project_dir').'/public/'.Media::DIR;
            $path = $dir.'/'.$newFilename;

            $fs = new Filesystem();
            if ($fs->exists($path.'/')) {
                $form->get('file')->addError(new FormError("Un fichier de ce nom existe déjà. Pour ne pas écraser le fichier existant, veuillez renommer ce ficher..."));
            } else {
                $image->move($dir, $newFilename);
                $data->setUrl(Media::DIR.'/'.$newFilename);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Media::class,
        ]);
    }
}
