<?php

namespace AppBundle\Form;

use AppBundle\Entity\RaceCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditRaceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startTime',DateTimeType::class, array(
                'years' => range(2017, 2027),
            ))
            ->add('distance')
            ->add('maxRunners')
            ->add('categories', EntityType::class, array(
                'class' => RaceCategory::class,
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
                'attr' => array(
                    'class' => '',
                ),
            ))
        ;

        $builder->get('categories')
            ->addModelTransformer(new CallbackTransformer(
                function ($tagsAsArray) {

                    return implode(', ', $tagsAsArray);
                },
                function ($tagsAsString) {
                    // transform the string back to an array
                    return explode(', ', $tagsAsString);
                }
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Race'
        ));
    }

}
