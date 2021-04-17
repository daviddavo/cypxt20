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
                'help' => 'Tu nombre',
                'required' => true
            ])
            ->add('name', TextType::class, [
                'label' => '¿Para quién es el '.$options["singular"].'?',
                'help' => 'Nombre de la persona a la que se lo dedicas',
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
            ->add('number', TelType::class, [
                'label' => 'Número fijo o móvil (preferiblemente fijo)',
                'help' => '¿A qué número quieres que llamemos?',
                'required' => true
            ])
            ->add('comment', TextAreaType::class, [
                'label' => 'Comentarios',
                'help' => '¿Algo más? ¿Habrá más gente con él/ella? ¿Quieres que contemos varios '.$options["plural"].' para varias personas?',
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
