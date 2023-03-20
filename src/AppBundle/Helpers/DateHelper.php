<?php
namespace AppBundle\Helpers;

use Doctrine\Logic\ContentBlockTable;

use Symfony\Component\Templating\Helper\Helper;

class DateHelper extends Helper
{

		function time_elapsed_string($ago, $full = false) {
			$now = new \DateTime;
			$diff = $now->diff($ago);

			$diff->w = floor($diff->d / 7);
			$diff->d -= $diff->w * 7;

			$string = array(
				'y' => 'year',
				'm' => 'month',
				'w' => 'week',
				'd' => 'day',
				'h' => 'hour',
				'i' => 'minute',
				's' => 'second'
			);

			foreach ($string as $k => &$v) {
				if ($diff->$k) {
					$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
				} else {
					unset($string[$k]);
				}
			}

			if (!$full) $string = array_slice($string, 0, 1);
			return $string ? implode(', ', $string) . ' ago' : 'just now';
		}

		public function show($date) {
			if(gettype($date) == "string") {
				$date = new \DateTime($date);
			}

			$today = mktime(0, 0, 0);

			if($date->getTimestamp() >= $today) {
				return $date->format("H:i");
			}

			$startweek = $today - date("w") * 3600 * 24;

			if($date->getTimestamp() >= $startweek) {
				return $date->format("D H:i");
			}

			$startyear = mktime(0, 0, 0, 0, 0);

			if($date->getTimestamp() >= $startyear) {
				return $date->format("d M");
			}

			$value = $date->format("d.m.Y H:i");
			return $value;
		}

		public function format($date, $format = "d.m.Y") {

			if(gettype($date) == "string") {
				$date = new \DateTime($date);
			}

			$value = $date->format($format);
			return $value;
		}

		public function getName() {
			return "date";
		}
}
