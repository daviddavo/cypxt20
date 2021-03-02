<?php

namespace App\Form;

use App\Entity\OnlineCall;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('fromName', TextType::class, [
                'label' => '¿De parte de quién es el ' .$options["singular"]. '?',
                'help' => 'Tu nombre (o pseudónimo)',
                'required' => true
            ])
            ->add('name', TextType::class, [
                'label' => '¿Para quién es el '.$options["singular"].'?',
                'help' => 'Nombre, pseudónimo o apodo de la persona a la que se lo dedicas (puedes poner «¡Yo mismo!» o «Mi menda» si es para ti)',
                'required' => true,
                ])
            ->add('age', IntegerType::class, [
                'label' => "Edad",
                'help' => 'La edad aproximada de para quien va dirigido el '.$options["singular"],
                'required' => true,
                'attr' => [
                    'min' => 0,
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
                'required' => true,
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    /*'14:00 - 15:00' => 14,
                    '15:00 - 16:00' => 15,
                    '16:00 - 17:00' => 16,
                    '17:00 - 18:00' => 17,*/
                    '18:00 - 19:00' => 18,
                    '19:00 - 20:00' => 19,
                    '20:00 - 21:00' => 20,
                    '21:00 - 22:00' => 21
                ]
            ])
            ->add('number', TelType::class, [
                'label' => 'Número fijo o móvil (preferiblemente fijo)',
                'help' => '¿A qué número quieres que te llamemos?',
                'required' => true
            ])
            ->add('comment', TextAreaType::class, [
                'label' => 'Comentarios',
                'help' => '¿Algo más? ¿Hay más gente contigo? ¿Quieres que recitemos varios '.$options["plural"].' para varias personas de tu familia?',
                'required' => false,
            ])->add('submit', SubmitType::class, [
                'label' => 'Enviar'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OnlineCall::class,
            'singular' => 'poema',
            'plural' => 'poemas'
        ]);
    }
}
