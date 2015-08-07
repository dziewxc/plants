<?php
namespace Dogs\DogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name');
    }
    
    public function getParent()
    {
        return 'fos_user_registration';
    }
    
    public function getName()
    {
        return 'user_registration';
    }
}