<?php
namespace App\Form;

use App\Entity\FilmPersonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Film;
use App\Repository\FilmRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Entity\Format;
use App\Repository\FormatRepository;
use App\Entity\Lieu;
use App\Repository\LieuRepository;
use App\Entity\Personne;
use App\Repository\PersonneRepository;

class FilmPersonneType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_personne']) {
            // $builder->add('personne', ChoiceType::class, array(
            // 'choice_label' => 'username',
            // 'choices' => $options['select_personne']
            // ));
            $builder->add('personne', EntityType::class, array(
                'class' => Personne::class,
                'query_builder' => function (PersonneRepository $pr) {
                    return $pr->createQueryBuilder('personne')
                        ->orderBy('personne.nom')
                        ->addOrderBy('personne.prenom');
                },
                'choice_label' => function (Personne $personne) {
                    return $personne->getPrenom() . ' ' . $personne->getNom();
                }
            ));
        }

        $builder->add('format', EntityType::class, array(
            'class' => Format::class,
            'query_builder' => function (FormatRepository $fr) {
                return $fr->createQueryBuilder('format')
                    ->andWhere('format.objet IN (0,2)')
                    ->orderBy('format.nom');
            },
            'choice_label' => 'nom'
        ))
            ->add('date_achat', BirthdayType::class, array(
            'label' => "Date d'achat",
            'required' => false,
            'widget' => 'choice',
            'format' => 'ddMMyyyy'
        ))
            ->add('lieu', EntityType::class, array(
            'class' => Lieu::class,
            'query_builder' => function (LieuRepository $lr) {
                return $lr->createQueryBuilder('lieu')
                    ->orderBy('lieu.nom');
            },
            'choice_label' => 'nom'
        ));
        // ->add('lieu', ChoiceType::class, array(
        // 'choice_label' => 'nom',
        // 'choices' => $options['select_lieu']
        // ));

        if ($options['avec_film']) {
            $builder->add('film', EntityType::class, array(
                'class' => Film::class,
                'query_builder' => function (FilmRepository $fr) {
                    return $fr->createQueryBuilder('film')
                        ->orderBy('film.titre');
                },
                'choice_label' => 'titre'
            ));
        }

        if ($options['avec_save']) {
            $builder->add('save', SubmitType::class, array(
                'label' => $options['label_submit'],
                'attr' => array(
                    'class' => 'btn btn-media'
                )
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FilmPersonne::class,
            'label_submit' => 'Valider',
            'avec_save' => true,
            'select_personne' => array(),
            'select_lieu' => array(),
            'avec_personne' => true,
            'avec_film' => true
        ));
    }
}
