<?php

namespace App\Form;

use App\Entity\Aprovisionnement;
use App\Entity\Article;
use App\Entity\Commande;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class ApprovisionnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantite', NumberType::class, [
            'constraints' => [
                new Assert\NotBlank(['message' => 'La quantité est obligatoire.']),
                new Assert\Positive(['message' => 'La quantité doit être supérieure à 0.']),
            ],
            'attr' => [
                'min' => 1,
            ],
        ])
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Aprovisionnement::class,
        ]);
    }
}
