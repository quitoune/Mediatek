<?php
namespace App\Form;

use App\Entity\EpisodePersonne;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Personne;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use App\Entity\Lieu;
use App\Repository\LieuRepository;
use App\Entity\Episode;
use App\Repository\EpisodeRepository;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Repository\PersonneRepository;
use App\Entity\Format;
use App\Repository\FormatRepository;

class EpisodePersonneType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['avec_personne']) {
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
            'required' => false,
            'label' => "Date d'achat",
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

        if ($options['avec_episode']) {
            $builder->add('episode', EntityType::class, array(
                'class' => Episode::class,
                'query_builder' => function (EpisodeRepository $fr) {
                    return $fr->createQueryBuilder('episode')
                        ->orderBy('episode.titre_original');
                },
                'choice_label' => ($options['vo'] ? 'titre_original' : 'titre')
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
            'data_class' => EpisodePersonne::class,
            'label_submit' => 'Valider',
            'avec_save' => true,
            'avec_personne' => true,
            'avec_episode' => true,
            'vo' => true
        ));
    }
}
