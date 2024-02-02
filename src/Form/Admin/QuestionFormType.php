<?php

namespace App\Form\Admin;

use App\Entity\Media;
use App\Entity\Question;
use App\Form\Admin\MediaFormType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
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

class QuestionFormType extends AbstractType
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class, [
              'attr' => ['placeholder' => "ex: 	Des traces d'hydromorphie (taches rouilles/grises) sont-elles observées à moins de 50 cm de profondeur ?"],
            ])
            ->add('media', MediaFormType::class, [
              'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        // to support frontend changes of the associated media
        if ($form->get('media')->getData() && $data['media']['id']) {
            // we need to fetch the existing media
            $media = $this->em->getRepository(Media::class)->findOneById($data['media']['id']);
            // refresh the question entity ( otherwise doctrine will try to persist the existing media)
            $question = $form->getData();
            $this->em->refresh($question);
            // set media and form
            $question->setMedia($media);
            $form->setData($question);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
