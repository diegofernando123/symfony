<?php
namespace AppBundle\Helpers;

use Doctrine\Logic\ConfigTable;

use Symfony\Component\Templating\Helper\Helper;

class ConfigHelper extends Helper
{
		public function get($name) {
			$field = ConfigTable::getInstance()->getByName($name);
			if($field == null) {
				return "";
			}
			return $field['value'];
		}

		public function has($name) {
			$field = ConfigTable::getInstance()->getByName($name);
			
			if($field == null) {
				return false;
			}

			return strlen($field['value']) > 0;
		}

		public function getName() {
			return "config";
		}
}
