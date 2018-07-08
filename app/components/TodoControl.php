<?php

namespace App\Components;

use App\Model\TodoService;
use Nette\Application\UI;
use Tracy\Debugger;
use Tracy;

class TodoControl extends UI\Control {

	/** @var TodoService @inject */
	public $todoService;

	public $editableId = null;

	public function render() {
		$this->template->editableId = $this->editableId;
		$this->template->nodes = $this->todoService->getNodes();
		$this->template->setFile(__DIR__ . '/TodoControl.latte');
		$this->template->render();
	}

	public function handleAddNode($value) {

		$this->todoService->addNode($value);
		$this->redrawControl('wholeList');
	}

	public function handleDelete($id, $position) {
            
		$this->todoService->deleteNode($id, $position);
		$this->redrawControl('wholeList');
	}

	public function handleDone($id, $done) {

		$this->todoService->doneNode($id, $done == "done" ? "" : "done");
		$this->redrawControl('wholeList');
	}

	public function handleUpdateTask($id, $value) {
		$this->todoService->editNode($id, $value);
		$this->redrawControl("wholeList");
	}

	public function handleEdit($id) {
		$this->editableId = $id;
                $this->redrawControl('wholeList');
	}
        public function handleUpdateOrder($order = array()) {
                $this->todoService->editOrder($order);
		$this->redrawControl('wholeList');
	}
           public function handleDrop(){
            
           $this->todoService->dropNodes();
           $this->redirect('this');
        }  
}
