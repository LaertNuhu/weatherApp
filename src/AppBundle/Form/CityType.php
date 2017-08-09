<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class CityType
 *
 * @package AppBundle\Form
 */
class CityType extends AbstractType
{
    /**
     *  Builds a form
     *
     * @param FormBuilderInterface $builder
     * @param array                $options options can be defined
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            TextType::class,
            array('attr'=>array('placeholder'=>'City'))
        )->add(
            'Search',
            SubmitType::class,
            array('attr'=> array('label'=>'Create Todo',
                'class'=>'btn btn-primary','style'=>'margin-bottom:-8px;width:auto'))
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array('data_class' => 'AppBundle\Entity\City')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_city';
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     *
     * @deprecated Deprecated since Symfony 2.8, to be removed in Symfony 3.0.
     *             Use the fully-qualified class name of the type instead.
     */
    public function getName()
    {
        // TODO: Implement getName() method.
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options
     *
     * @deprecated since version 2.7, to be renamed in 3.0.
     *             Use the method configureOptions instead. This method will be
     *             added to the FormTypeInterface with Symfony 3.0.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        // TODO: Implement setDefaultOptions() method.
    }
}
