<?php

namespace App\Model;

use Nette\Http\Session;
use Nette\SmartObject;

class TodoService{
    
    use SmartObject;

    private $sessionToDo;
    
        public function __construct(Session $session){
            
            $this->sessionToDo = $session->getSection('sessionToDo');
        }
        
        public function getNodes(){
            
            return $this->sessionToDo->nodes;
        }
        
        public function addNode($value){
            
                if($this->sessionToDo->nodes){
                    $this->sessionToDo->nodes[] = $value;
                }else{
                    $this->sessionToDo->nodes[1] = $value;
                }
        }
        
        public function deleteNode($id){
            
            unset($this->sessionToDo->nodes[$id]);
        }   
        
        public function editNode($id, $value){
            
            $this->sessionToDo->nodes[$id]  = $value;
        }   
        
        public function dropNodes(){
            
           unset($this->sessionToDo->id);
           unset($this->sessionToDo->nodes);
        }   
}