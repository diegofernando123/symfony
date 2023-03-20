<?php

namespace AppBundle\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ExperienceEnd extends Constraint
{
	public $message = "End date is required";
	public $shouldBeLater = "End date should be later than start date";
}
