<?php

namespace App\Components;

use App\Model\TodoService;
use Nette\Application\UI;

class TodoControl extends UI\Control{
            
        /** @var     TodoService */
        private $service;

        /**
	 * Vstříkne službu, kterou tato komponenta bude používat pro práci s komentáři.
	 *
	 * @param    TodoService $service
	 * @return   void
	 */
	public function setService(TodoService $service){
            
		$this->service = $service;
	}
        
        public function render(){
            
            $this->template->setFile(__DIR__ . '/TodoControl.latte');
            if(!isset($this->template->nodes)){
               $this->template->nodes = $this->service->getNodes();
               
           }$this->template->render();
            
        }
        
        public function handleAddNode($value){
            
            $this->service->addNode($value);
            $this->redrawControl('wholeList');
        }
        
        public function handleDelete($id){
            
            $this->service->deleteNode($id);
            $this->redrawControl('wholeList');
            
        }   
        
        public function handleEdit($id, $value){
            
            $this->service->editNode($id, $value);
            $this->redrawControl('toDoList');
            
        }   
        
        public function drop(){
            
           $this->service->dropNodes();
        }   
}
