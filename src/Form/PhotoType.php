<?php
namespace App\Form;

use App\Entity\Photo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PhotoType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['add']){
            $builder->add('chemin', FileType::class, array(
                'label' => 'Photo',
                'data_class' => null,
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Choisir la photo'
                )
            ));
        }
        
        $builder->add('nom')
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
            'add' => false,
            'data_class' => Photo::class,
            'label_submit' => 'Enregistrer',
            'allow_extra_fields' => true
        ));
    }
}
