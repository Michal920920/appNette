<?php

namespace App\Components;

use App\Model\DataManager\TodoServiceDataManager;
use Nette\Application\UI;

class TodoControl extends UI\Control {

	/** @var TodoServiceDataManager @inject */
	public $todoService;
        
	public function render() {
                if(!isset($this->template->nodes)){
                    $this->template->nodes = $this->todoService->getNodes();
                }
		$this->template->setFile(__DIR__ . '/TodoControl.latte');
		$this->template->render();
	}
        
        public function handleGetSubnode($id){
           if($id){
               $this->template->subnodes = $this->todoService->getSubnode($id);
            }
               $this->redrawControl('wholeList');
            }
        
        public function handleGetBoxSubnode($id, $value){
           if($id){
               
                $this->template->boxSubnodes = $this->todoService->getSubnode($id);
                $this->template->boxNodeVal = $value;
                $this->template->boxNodeId = $id;
                $this->redrawControl('box');
           }else{
               $this->template->boxNodeId = null;
               $this->redrawControl('wholeList');
           } 
        }

        public function handleAddNode($value, $date, $subValue = array()) {

		$this->todoService->addNode($value, $date);
                //pokud z js přišla subnodes, ulož je k dané node  
                $id = $this->todoService->getLastNode();
                if($subValue){
                    foreach($subValue as $key){
                        $this->todoService->addSubnode($key, $id);
                    }
                    $this->todoService->editNodeHasSubnode($id, 1);
                }
		$this->redrawControl('wholeList');
	}
        
        public function handleEditNode($value, $date, $subValue = array(), $id = null) {
           
                $oldSub = array_values($this->todoService->getSubnode($id));
                $this->todoService->editNode($id, $value, $date);
                
                //uprav hodnoty
                foreach($oldSub as $value => $key){
                    if(isset($subValue[$value]) && $oldSub[$value]['subnode'] !== $subValue[$value]){
                        $this->todoService->editSubnode($oldSub[$value]['id'], $subValue[$value]);
                        
                    }else if(!isset($subValue[$value])){
                        $this->todoService->deleteSubnode($oldSub[$value]['id']);
                    }
                }
                foreach($subValue as $value => $key){
                    if(!isset($oldSub[$value]) && count($subValue) > count($oldSub)){
                        $this->todoService->addSubnode($subValue[$value],$id);
                    }  
                }
                
                //pokud node nemá subnodes, nastav jí hodnotu 'subnodes' v db na 0   
                if($this->todoService->getSubnode($id)){
                    $this->template->subnodes = $this->todoService->getSubnode($id);
                    
                }else{
                    $this->todoService->editNodeHasSubnode($id, 0);
                }
                $this->redrawControl('toDoListNodes');
            }
        
        public function handleAddSubnode($value, $id) {
                
                $this->todoService->addSubnode($value, $id);
                $this->redrawControl('toDoListNodes');
        }

	public function handleDelete($id, $position) {
            
		$this->todoService->deleteNode($id, $position);
		$this->redrawControl('wholeList');
	}
        
	public function handleDone($id, $done) {

		$this->todoService->doneNode($id, $done == "done" ? "" : "done");
                $this->template->nodes = $this->todoService->getNode($id);
		$this->redrawControl('toDoListNodes');
	}

	public function handleUpdateTask($id, $value) {
            
		$this->todoService->editNode($id, $value);
                $this->template->nodes = array($this->todoService->getNode($id));
		$this->redrawControl('toDoListNodes');
	}

        public function handleUpdateOrder($order = array(), $table, $id) {
            
                if($table == 'subnodes' || ($table == 'nodes' && isset($id))){
                    
                   $this->todoService->editOrder($order, $table, $id);
                   $this->template->subnodes = $this->todoService->getSubnode($id);
                   $this->redrawControl('wholeList');
                    
                }else if($table == 'nodes'){
                    $this->todoService->editOrder($order, $table);
                    $this->redrawControl('wholeList');
                }
	}
}
