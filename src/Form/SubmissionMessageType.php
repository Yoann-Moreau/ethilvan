<?php

namespace App\Form;

use App\Entity\SubmissionMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmissionMessageType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options): void {
		$builder
				->add('message', TextareaType::class, [
						'label'    => 'Message',
						'required' => true,
				])
				->add('images', FileType::class, [
						'label'    => 'Images',
						'multiple' => true,
						'required' => false,
				]);
	}


	public function configureOptions(OptionsResolver $resolver): void {
		$resolver->setDefaults([
				'data_class' => SubmissionMessage::class,
		]);
	}

}
