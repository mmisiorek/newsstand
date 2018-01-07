<?php
/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 13.05.2017
 * Time: 19:34
 */

namespace AppBundle\Form\Type;

use AppBundle\Entity\News;
use AppBundle\Form\EventListener\NewsTypeEventSubscriber;
use AppBundle\Service\FileManager\FileManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array(
            'required' => true,
            'error_bubbling' => true
        ))
        ->add('photo', FileType::class, array(
            'required' => true,
            'error_bubbling' => true,
            'mapped' => false
        ))
        ->add('body', CKEditorType::class, array(
            'required' => true,
            'error_bubbling' => true
        ))
        ->addEventSubscriber(new NewsTypeEventSubscriber($options['fileManager'], $options['tokenStorage']));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('fileManager', 'tokenStorage'));

        $resolver->setDefaults(array(
            'data_class' => News::class
        ));

        $resolver->setAllowedTypes('fileManager', array(FileManagerInterface::class));
        $resolver->setAllowedTypes('tokenStorage', array(TokenStorageInterface::class));
    }
}