<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\File;

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
        ->add('article_text', TextareaType::class, [
            'label' => 'Article text',
            'required' => true,
            'attr' => [
                'placeholder' => 'Entrer le texte pour votre article',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter some text for your article',
                ]),
                new Length([
                    'min' => 5,
                    'max' => 2000,
                    'minMessage' => 'Votre article doit faire au moins 5 caractères',
                    'maxMessage' => 'Votre article doit faire au plus 2000 caractères',
                ]),
                new Type([
                    'type' => 'string',
                    'message' => 'The article text must be a string',
                ]),
            ]
        ])
            // ->add('createDate')
            // ->add('updateDate')
        ->add('submit', SubmitType::class, [
                'label' => 'Soumettre l\'article'
        ])
        ->add('img', FileType::class, [
            'label' => 'Votre image (png, jpeg, webp)',
            'required' => false,
            'mapped' => false,
            'constraints' => [
                new File([
                    'maxSize' => '1024k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/webp'
                    ],
                    'mimeTypesMessage' => 'Seul les formats png, jpg ou encore webp sont acceptés',
                ])
                    
                
            ],
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
