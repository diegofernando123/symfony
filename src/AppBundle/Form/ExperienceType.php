<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ExperienceType extends AbstractType
{
  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
        $builder
            ->add('company', null, ['label' => 'Company Name'])
            ->add('logo', FileType::class, [
                'label' => 'Upload/Change Company Logo',
                'data_class' => null,
                'required' => false
            ])
            ->add('title', null, ['label' => 'Job Title'])
            ->add('industry', ChoiceType::class, ['choices' => \App::getTable('AppBundle:Industry')->getIndustryList(), 'placeholder' => 'Choose...',
                'label' => 'Industry'
            ])
           ->add('location', null, ['label' => 'Location', 'attr' => ['placeholder' => 'Enter a location']])
           ->add('specialisation', null, ['label' => 'Specialisation',  'required' => false])
           ->add('startYear', ChoiceType::class, ['choices' => $this->getYearsRange(), 'placeholder' => 'Choose...',
                'label' => 'Time Period'
           ])
           ->add('startMonth', ChoiceType::class, ['choices' => $this->getMonthsRange(), 'placeholder' => '']
           )
           ->add('endYear', ChoiceType::class, ['required' => false, 'choices' => $this->getYearsRange(), 'placeholder' => 'Choose...']
           )
           ->add('endMonth', ChoiceType::class, ['required' => false, 'choices' => $this->getMonthsRange(), 'placeholder' => ''])
           ->add('isCurrent', CheckboxType::class, ['label' => 'I currently work here', 'required' => false, 'attr' => ['onclick' => 'toggleTimePeriod(this)']]);
    }

    public function getMonthsRange()
    {
    	return array(
    		'Jan' => 1,
    		'Feb' => 2,
    		'Mar' => 3,
    		'Apr' => 4,
    		'May' => 5,
    		'Jun' => 6,
    		'Jul' => 7,
    		'Aug' => 8,
    		'Sep' => 9,
    		'Oct' => 10,
    		'Nov' => 11,
    		'Dec' => 12
    	);
    }

    public function getYearsRange()
    {
    	$values = array();

        for ($i = date('Y'); $i >= date('Y') - 70; $i--) {
            $values[$i] = $i;
        }

        return $values;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Experience'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_experience';
    }
}
