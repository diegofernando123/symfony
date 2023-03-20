<?php
namespace AppBundle\Helpers;

use Doctrine\Logic\ContentBlockTable;

use Symfony\Component\Templating\Helper\Helper;

class BlockHelper extends Helper
{
		public function header($id) {
			$block = ContentBlockTable::getInstance()->getBySlug($id);
			if($block == null) {
				return "";
			}
			return $block['title'];
		}
		
		public function content($id) {
			$block = ContentBlockTable::getInstance()->getBySlug($id);
			
			if($block == null) {
				return "";
			}
			
			return $block['content'];
		}
		
		public function getName() {
			return "blocks";
		}
}
