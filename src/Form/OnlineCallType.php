<?php

namespace App\Form;

use App\Entity\OnlineCall;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OnlineCallType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nombre o pseudónimo',
                'help' => 'Tu nombre o uno inventado para poderte dar un trato personal'
                ])
            ->add('age', IntegerType::class, [
                'label' => "Edad",
                'help' => 'La edad aproximada de para quien va dirigido el poema',
                'attr' => [
                    'min' => 5,
                    'max' => 100
                ]
            ])
            /*->add('type', ChoiceType::class, [
                'label' => "Tipo",
                'help' => '¿Qué tipo de Poema te apetece escuchar?',
                'choices' => [
                    'Caballerías' => 0,
                    'Romántico' => 1,
                    'Infantil' => 2
                ]
            ])*/
            ->add('hours', ChoiceType::class, [
                'label' => 'Horas',
                'help' => '¿A qué horas te podemos llamar?',
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    '14-15' => 14,
                    '15-16' => 15,
                    '16-17' => 16,
                    '17-18' => 17,
                    '18-19' => 18,
                    '19-20' => 19]
            ])
            ->add('number', TelType::class, [
                'label' => 'Número fijo o móvil (preferiblemente fijo)',
                'help' => '¿A qué número quieres que te llamemos?',
            ])
            ->add('comment', TextAreaType::class, [
                'label' => 'Comentarios',
                'help' => '¿Algo más? ¿Hay más gente contigo? ¿Quieres que recitemos varios poemas para varios para varias personas de tu familia?'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OnlineCall::class,
        ]);
    }
}
