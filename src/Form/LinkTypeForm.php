<?php

namespace App\Form;

use App\Entity\Link;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkTypeForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('original_url', UrlType::class, [
                'label' => 'Введите ссылку:',
                'attr' => [
                    'placeholder' => 'Введите ссылку',
                    'style' => 'width:300px;',
                ],
                'required' => true,
            ])
            ->add('is_one_time', CheckboxType::class, [
                'label' => 'Одноразовая ссылка',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'label_attr' => [
                    'class' => 'form-check-label'
                ],
                'row_attr' => [
                    'class' => 'form-check'
                ]
            ])
            ->add('expiration_date', null, [
                'label' => 'Дата устаревания ссылки',
                'required' => false,
                'widget' => 'single_text',
                'attr' => [
                    'class' => 'date-picker-input'
                ],
                'row_attr' => [
                    'class' => 'date-picker'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Link::class,
        ]);
    }
}
