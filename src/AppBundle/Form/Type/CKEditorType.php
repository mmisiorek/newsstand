<?php
/**
 * Created by PhpStorm.
 * User: marcinmisiorek
 * Date: 13.05.2017
 * Time: 22:44
 */

namespace AppBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CKEditorType extends AbstractType
{
    public function getParent()
    {
        return TextareaType::class;
    }

    public function getBlockPrefix()
    {
        return 'ckeditor';
    }
}