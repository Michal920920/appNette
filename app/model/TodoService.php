<?php

namespace App\Model;
use Nette;
use Nette\Http\Session;
use Nette\SmartObject;
use Nette\Security\User;
use Nette\Database\Context;

class TodoService{
    
    use SmartObject;

    private $sessionToDo;
    /** @var Nette\Database\Context */
    private $database;
    /** @var Nette\Security\User */
    private $user;
    
             const
             TABLE_NAME = 'nodes',
             COLUMN_NODE_ID = 'node_id',
             COLUMN_NODE = 'node',
             COLUMN_USER_ID = 'user_id',
             COLUMN_NODE_DONE = 'node_done';
    
        public function __construct(Session $session, Context $database, User $user){
            
            $this->sessionToDo = $session->getSection('sessionToDo');
            $this->user = $user;
            $this->database = $database;
            
        }
        
        public function getNodes(){
            
            if($this->user->getIdentity()){
                return $this->database->fetchPairs('SELECT * FROM nodes WHERE user_id = ?', $this->user->getIdentity()->id);
            }else{
                return $this->sessionToDo->nodes;
            }
        }
        
        public function addNode($value){
            
            if($this->user->getIdentity()){
                $this->database->table(self::TABLE_NAME)->insert(array(
                    self::COLUMN_NODE => $value,
                    self::COLUMN_USER_ID => $this->user->getIdentity()->id,
                    self::COLUMN_NODE_DONE => 0
                ));
            }else{
                
            if($this->sessionToDo->nodes){
                $this->sessionToDo->nodes[] = array("node" => $value,"node_done" => 0);
            }else{
                 $this->sessionToDo->nodes[1] = array("node" => $value,"node_done" => 0);
                }
            }
        }
        
        public function deleteNode($id){
            
            if($this->user->getIdentity()){
                $this->database->query(
                        'DELETE FROM `nodes`
                         WHERE `nodes`.`node_id` = ?
                         AND `nodes`.`user_id` = ?', 
                         $id, $this->user->getIdentity()->id 
                        );
            }else{
                unset($this->sessionToDo->nodes[$id]);
            }
        }   
        
        public function editNode($id, $value){
            
            if($this->user->getIdentity()){
                $this->database->query(
                        'UPDATE `nodes`
                         SET `node` = ?
                         WHERE `nodes`.`node_id` = ?
                         AND `nodes`.`user_id` = ?', 
                         $value, $id, $this->user->getIdentity()->id 
                        );
            }else{
                $this->sessionToDo->nodes[$id]['node'] = $value;
            }
        }
        
        public function doneNode($id){
            if($this->user->getIdentity()){
                $this->database->query(
                        'UPDATE `nodes`
                         SET `node_done` = 1
                         WHERE `nodes`.`node_id` = ?
                         AND `nodes`.`user_id` = ?', 
                         $id, $this->user->getIdentity()->id 
                        );
            }else{
              $this->sessionToDo->nodes[$id]['node_done'] = true;
            }
        }  
        
        public function dropNodes(){
            
           unset($this->sessionToDo->id);
           unset($this->sessionToDo->nodes);
        }   
}