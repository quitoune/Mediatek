<?php
namespace App\Form;

use App\Entity\Lieu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class LieuType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class, array(
            'required' => false
        ))
            ->add('numero_voie', IntegerType::class, array(
            'required' => false,
            'attr' => array(
                'min' => 0
            )
        ))
            ->add('mention', TextType::class, array(
            'required' => false
        ))
            ->add('voie', TextType::class, array(
            'required' => false
        ))
            ->add('complement', TextType::class, array(
            'required' => false
        ))
            ->add('code_postal', IntegerType::class, array(
            'required' => false,
            'attr' => array(
                'min' => 0
            )
        ))
            ->add('ville', TextType::class, array(
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
            'data_class' => Lieu::class,
            'label_submit' => 'Enregistrer'
        ));
    }
}
