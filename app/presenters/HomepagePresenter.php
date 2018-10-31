<?php

namespace App\Presenters;

use App\Components\TodoControlFactory;
use App\Components\TodoControl;

class HomepagePresenter extends BasePresenter {

	/** @var TodoControlFactory @inject */
	public $todoControlFactory;

	public $user;

	public function actionDefault() {

	}

	/**
	 * Todo list
	 * @return TodoControl
	 */
	protected function createComponentTodo() {
		$control = $this->todoControlFactory->create();
		return $control;
	}

	public function actionDrop() {

		$control = $this->getComponent('todo');
		$control->drop();
		$this->redirect('Homepage:');
	}
}