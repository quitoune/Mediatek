<?php
namespace App\Form;

use App\Entity\Film;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Saga;
use App\Repository\SagaRepository;
use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class FilmType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre')
            ->add('titre_original')
            ->add('realisateur', TextType::class, array(
            'label' => 'Réalisateur'
        ))
            ->add('annee', IntegerType::class, array(
            'label' => 'Année',
            'attr' => array(
                'min' => 1900,
                'max' => 2100
            )
        ))
            ->add('duree', IntegerType::class, array(
            'label' => 'Durée (min)',
            'attr' => array(
                'min' => 0,
                'max' => 1000
            )
        ))
            ->add('volet', IntegerType::class, array(
            'required' => false
        ))
            ->add('saga', EntityType::class, array(
            'class' => Saga::class,
            'choice_label' => 'nom',
            'required' => false,
            'query_builder' => function (SagaRepository $sr) {
                return $sr->createQueryBuilder('saga')
                    ->orderBy('saga.nom');
            },
            'choice_attr' => function ($choiceValue, $key, $value) {
                return array(
                    'id' => 'saga_' . $value
                );
            }
        ))
            ->add('categories', EntityType::class, array(
            'class' => Categorie::class,
            'choice_label' => 'nom',
            'multiple' => true,
            'required' => false,
            'attr' => array(
                'class' => 'multiple'
            ),
            'query_builder' => function (CategorieRepository $cr) {
                return $cr->createQueryBuilder('c')
                    ->andWhere('c.objet IN (0,2)')
                    ->orderBy('c.nom');
            }
        ))
            ->add('description', TextareaType::class)
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
            'allow_extra_fields' => true,
            'data_class' => Film::class,
            'label_submit' => 'Valider'
        ));
    }
}
