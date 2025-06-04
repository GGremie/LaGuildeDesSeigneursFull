<?php

namespace App\Form;

use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CharacterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier')
            ->add('name')
            ->add('slug')
            ->add('kind')
            ->add('surname')
            ->add('caste')
            ->add('knowledge')
            ->add('intelligence')
            ->add('strength')
            ->add('image')
            ->add('creation')
            ->add('modification')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Character::class,
        ]);
    }
}
