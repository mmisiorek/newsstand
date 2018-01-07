<?php
/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 16.05.2017
 * Time: 20:15
 */

namespace AppBundle\Form\Type;


use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserVerificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', RepeatedType::class, array(
            'required' => true,
            'error_bubbling' => true,
            'type' => PasswordType::class,
            'first_options' => array('label' => 'Password', 'constraints' => new NotBlank()),
            'second_options' => array('label' => 'Repeat password'),
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class
        ));
    }
}