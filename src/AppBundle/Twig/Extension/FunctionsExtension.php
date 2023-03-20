<?php
 
namespace AppBundle\Twig\Extension;
 
class FunctionsExtension extends \twig_Extension
{
        /**
         *Return the function registered as twig extension
         *
         *@return array
         */
        public function getFunctions()
        {
                return array(
                        'file_exists' => new \Twig\TwigFunction('file_exists', 'file_exists'),
                        );
        }
 
        public function getName()
        {
                return 'functions';
        }
}