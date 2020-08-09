<?php
namespace App\Form;

use App\Entity\Saga;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class SagaType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom')
            ->add('saga', EntityType::class, array(
            'class' => Saga::class,
            'label' => 'Saga mère',
            'required' => false,
            'choice_label' => 'nomComplet'
        ))
            ->add('save', SubmitType::class, array(
            'label' => 'Enregister',
            'attr' => array(
                'class' => 'btn btn-media'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Saga::class
        ));
    }
}
