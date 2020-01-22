<?php 

namespace App\Traits;

trait Checkable
{

	public function addChecked() {
		$this->checked++;
	}	
	
	public function addPassed() {
		$this->passed++;
	}	

}