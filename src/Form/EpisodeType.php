<?php
namespace App\Form;

use App\Entity\Episode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Saison;
use App\Repository\SaisonRepository;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EpisodeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('titre', TextType::class, array(
            'required' => false
        ))
            ->add('titre_original')
            ->add('numero_episode', IntegerType::class, array(
            'label' => 'Numéro de l\'épisode',
            'attr' => array(
                'min' => 1
            )
        ))
            ->add('numero_production', IntegerType::class, array(
            'label' => 'Numéro de production',
            'attr' => array(
                'min' => 1
            )
        ))
            ->add('premiere_diffusion', DateType::class, array(
            'label' => 'Première diffusion',
            'widget' => 'choice',
            'format' => 'dd/MM/yyyy',
            'years' => range(1975, date('Y') + 2),
            'required' => false
        ))
            ->add('duree', IntegerType::class, array(
            'label' => 'Durée (min)',
            'attr' => array(
                'min' => 0
            )
        ))
            ->add('saison', EntityType::class, array(
            'class' => Saison::class,
            'label' => 'Série / Saison',
            'choice_label' => function (Saison $saison) {
                return 'Saison ' . $saison->getNumeroSaison();
            },
            'group_by' => function (Saison $saison) {
                return $saison->getSerie()
                    ->getTitreOriginal();
            },
            'query_builder' => function (SaisonRepository $sr) {
                return $sr->createQueryBuilder('s')
                    ->orderBy('s.numero_saison');
            }
        ))
            ->add('description', TextareaType::class, array(
            'required' => false
        ))
            ->add('save', SubmitType::class, array(
            'label' => $options['label_submit'],
            'attr' => array(
                'class' => 'btn btn-media'
            )
        ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Episode::class,
            'label_submit' => 'Enregistrer',
            'allow_extra_fields' => true
        ));
    }
}
