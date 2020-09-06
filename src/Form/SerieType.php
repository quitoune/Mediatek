<?php
namespace App\Form;

use App\Entity\Serie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Form\newForm\YesNoType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Saga;
use App\Repository\SagaRepository;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class SerieType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', TextType::class, array(
            'required' => false
        ))
            ->add('titre_original')
            ->add('annee', IntegerType::class, array(
            'label' => 'Année'
        ))
            ->add('nombre_saison', IntegerType::class, array(
            'label' => 'Nombre de saisons'
        ))
            ->add('nombre_episode', IntegerType::class, array(
            'label' => "Nombre d'épisodes"
        ))
            ->add('terminee', YesNoType::class, array(
            'label' => 'Série terminée ?'
        ))
            ->add('categories', EntityType::class, array(
            'class' => Categorie::class,
            'choice_label' => 'nom',
            'multiple' => true,
            'by_reference' => false,
            'query_builder' => function (CategorieRepository $cr) {
                return $cr->createQueryBuilder('c')
                    ->andWhere('c.objet IN (0,2)')
                    ->orderBy('c.nom');
            }
        ))
            ->add('saga', EntityType::class, array(
            'class' => Saga::class,
            'choice_label' => 'nom',
            'required' => false,
            'query_builder' => function (SagaRepository $sr) {
                return $sr->createQueryBuilder('s')
                    ->orderBy('s.nom');
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
            'data_class' => Serie::class,
            'allow_extra_fields' => true,
            'label_submit' => 'Enregistrer'
        ));
    }
}
