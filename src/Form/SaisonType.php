<?php
namespace App\Form;

use App\Entity\Saison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SaisonType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class, array(
            'required' => false
        ))
            ->add('numero_saison', IntegerType::class, array(
            'label' => 'Numéro de la saison',
            'attr' => array(
                'min' => 1
            )
        ))
            ->add('nombre_episode', IntegerType::class, array(
            'label' => "Nombre d'épisode",
            'attr' => array(
                'min' => 1
            )
        ))
        ->add('serie', EntityType::class, array(
            'label' => 'Série',
            'class' => Serie::class,
            'choice_label' => ($options['vo'] ? 'titre_original' : 'titre'),
            'query_builder' => function (SerieRepository $sr) {
                return $sr->createQueryBuilder('s')
                    ->orderBy('s.titre_original');
            }
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
            'data_class' => Saison::class,
            'label_submit' => 'Enregistrer',
            'vo' => true
        ));
    }
}
