<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('comment_text', TextareaType::class, [
            'label' => 'Comment text',
            'required' => true,
            'attr' => [
                'placeholder' => 'Votre commentaire',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Votre commentaire ne doit pas être vide',
                ]),
                new Length([
                    'min' => 5,
                    'max' => 500,
                    'minMessage' => 'Votre commentaire doit faire au minimum 5 caractères',
                    'maxMessage' => 'Votre commentaire doit faire au maximum 500 caractères',
                ]),
                new Type([
                    'type' => 'string',
                    'message' => 'Vous ne pouvez envoyer ici que du texte',
                ]),
            ]
        ])
            // ->add('user')
            // ->add('article')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
