<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;

abstract class AbstractForm extends AbstractType implements \ArrayAccess, \Iterator
{
	private $index = 0;
	
	public function offsetExists($offset) {
		return in_array($offset, $this->vars);
	}
	
	public function offsetGet($offset) {
		return $this->$offset;
	}
	
	public function offsetSet($offset, $value) {
		$this->$offset = $value;
	}
	
	public function offsetUnset($offset) {
		$this->$offset = null;
	}

	public function current() {
		$var = $this->vars[$this->index];
		return $this->$var;
	}
	
	public function key() {
		return $this->vars[$this->index];
	}
	
	public function next() {
		$this->index++;
	}
	
	public function rewind() {
		$this->index = 0;
	}
	
	public function valid() {
		return $this->index < count($this->vars);
	}
	
	public function __get($key) {
		if(isset($this->$key)) {
			return $this->$key;
		}
		return null;
	}

	public function __set($key, $value) {
		$this->$key = $value;
	}

	public function setData($data) {
		foreach($data as $key => $value) {
			$this->$key = $value;
		}
	}

}
