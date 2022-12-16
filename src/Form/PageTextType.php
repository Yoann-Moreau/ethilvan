<?php


namespace App\Form;


use App\Entity\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PageTextType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('page', TextType::class, [
						'label'    => 'Page',
						'required' => true,
				])
				->add('title', TextType::class, [
						'label'    => 'Titre',
						'required' => false,
				])
				->add('text', TextareaType::class, [
						'label'    => 'Texte',
						'required' => false,
				])
				->add('text_order', NumberType::class, [
						'label'    => 'Ordre du texte',
						'required' => false,
				]);
	}

	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => Text::class,
		]);
	}
}
