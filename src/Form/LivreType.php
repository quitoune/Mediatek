<?php
namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Type;
use App\Entity\Saga;
use App\Entity\Categorie;
use App\Repository\TypeRepository;
use App\Repository\SagaRepository;
use App\Repository\CategorieRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use App\Entity\Photo;

class LivreType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre')
            ->add('titre_original')
            ->add('auteur')
            ->add('type', EntityType::class, array(
            'class' => Type::class,
            'choice_label' => 'nom',
            'query_builder' => function (TypeRepository $tr) {
                return $tr->createQueryBuilder('t')
                    ->orderBy('t.nom');
            }
        ))
            ->add('premiere_edition', IntegerType::class, array(
            'label' => 'Année de la première édition',
            'attr' => array(
                'min' => 1700
            )
        ))
            ->add('tome', IntegerType::class, array(
            'required' => false
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
                    ->andWhere('c.objet IN (0,1)')
                    ->orderBy('c.nom');
            }
        ));

//         $builder->add('photo', PhotoType::class, array(
//             'add' => true,
//             'label' => 'Photo',
//             'required' => false,
//             'avec_bouton' => false
//         ));

        $builder->add('photo', EntityType::class, array(
            'class' => Photo::class,
            'required' => false,
            'choice_label' => 'nom'
        ));
            
        $builder->add('saga', EntityType::class, array(
            'class' => Saga::class,
            'required' => false,
            'choice_label' => 'nom',
            'query_builder' => function (SagaRepository $sr) {
                return $sr->createQueryBuilder('s')
                    ->orderBy('s.nom');
            }
        ))
            ->add('save', SubmitType::class, array(
            'label' => 'Enregistrer',
            'attr' => array(
                'class' => 'btn btn-media'
            )
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'data_class' => Livre::class,
            'avec_categorie' => true
        ));
    }
}
