<?php

namespace App\Presenters;

use Nette\Http\Session;
use ToDoControl;

class HomepagePresenter extends BasePresenter
{
        private $sessionToDo;
        
        public function __construct(Session $session){
            $this->sessionToDo = $session->getSection('sessionToDo');
        }

    	public function renderDefault(){
            
            if(!isset($this->template->nodes)){
               $this->template->nodes = $this->sessionToDo->nodes;
           }
	}
        
        
        public function handleAddNode($value){
            
                if($this->sessionToDo->nodes){
                    $this->sessionToDo->nodes[] = $value;
                }else{
                    $this->sessionToDo->nodes[1] = $value;
                }
                
            $this->template->nodes = $this->sessionToDo->nodes;
            $this->redrawControl('wholeList');
        }
        
        public function handleDelete($id){
            
            unset($this->sessionToDo->nodes[$id]);
            $this->template->nodes = $this->sessionToDo->nodes;
            $this->redrawControl('wholeList');
            
          
        }   
        
        public function handleEdit($id, $value){
            
            $this->template->nodes = $this->isAjax()
                ? []
                : $this->sessionToDo->nodes;
            
            $this->sessionToDo->nodes[$id]  = $value; 
            $this->template->nodes[$id] = $value;
            $this->redrawControl('toDoList');
            
          
        }   
        
        public function actionDrop(){
           unset($this->sessionToDo->id);
           unset($this->sessionToDo->nodes);
           $this->redirect('Homepage:');
        }   
}