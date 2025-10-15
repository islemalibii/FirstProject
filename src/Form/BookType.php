<?php

namespace App\Form;

use App\Entity\Author;
use App\Entity\Book;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('title', null, [
            'label' => 'Title',
            'required' => true,
        ])
        ->add('category', ChoiceType::class, [
            'label' => 'Category',
            'choices' => [
                'Science-Fiction' => 'Science-Fiction',
                'Mystery' => 'Mystery',
                'Autobiography' => 'Autobiography',
            ],
            'required' => true,
        ])
        ->add('author', EntityType::class, [
            'label' => 'Author',
            'class' => Author::class,
            'choice_label' => 'username',
            'required' => true,
        ])
        ->add('publicationDate', DateType::class, [
            'label' => 'Publication Date',
            'widget' => 'single_text',  
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
