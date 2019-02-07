<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 14/06/2018
 * Time: 12:23
 */

namespace ScyLabs\NeptuneBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ValidForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' =>true,
            'roles'     => ['ROLE_ADMIN']
        ));
    }
}