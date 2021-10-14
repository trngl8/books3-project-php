<?php

namespace App\Form;

use App\Entity\Profile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('email', EmailType::class)
            ->add('locale', TextType::class, [
                'disabled' => 'disabled'
            ])
            ->add('timezone', TimezoneType::class)
            ->add('status', TextType::class)
            ->add('emoji', ChoiceType::class, [
                'choices' => [
                    'default' => null,
                    'orange' => 'book_orange',
                    'green' => 'book_green',
                    'red' => 'book_red',
                    'blue' => 'book_blue',
                ],
                'choice_translation_domain' => 'emoji'
            ])
            ->add('active', CheckboxType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profile::class,
        ]);
    }
}