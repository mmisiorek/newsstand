<?php
/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 13.05.2017
 * Time: 12:20
 */

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use AppBundle\Validator\Constraint\UniqueEmailAddress;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', TextType::class, array(
            'required' => true,
            'error_bubbling' => true
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'constraints' => array(new UniqueEmailAddress())
        ));
    }
}