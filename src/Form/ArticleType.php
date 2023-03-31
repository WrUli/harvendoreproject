<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', TextType::class, [
            'label' => 'Nom',
            'attr' => [
                'placeholder' => 'Le nom de votre article'
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Votre champs name est vide'
                ]),
                new Length([
                    'min' => 4,
                    'max' => 50,
                    'minMessage' => 'Votre nom fait malheureusement moins de 5 caractères',
                    'maxMessage' => 'Votre nom est trop long'
                ])
            ]
        ])
            ->add('article_text')
            // ->add('createDate')
            // ->add('updateDate')
            ->add('submit', SubmitType::class, [
                'label' => 'Soumettre l\'article'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
