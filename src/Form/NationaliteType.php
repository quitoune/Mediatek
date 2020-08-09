<?php
namespace App\Form;

use App\Entity\Nationalite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class NationaliteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pays')
            ->add('feminin', TextType::class, array(
            'label' => 'Au féminin'
        ))
            ->add('masculin', TextType::class, array(
            'required' => false,
            'label' => 'Au masculin'
        ))
            ->add('etat', TextType::class, array(
            'required' => false
        ))
            ->add('save', SubmitType::class, array(
            'label' => $options['label_submit'],
            'attr' => array(
                'class' => 'btn btn-media'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Nationalite::class,
            'label_submit' => 'Enregistrer'
        ));
    }
}
