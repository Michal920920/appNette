<?php

namespace App\Model;

use Nette;
use Nette\Http\Session;
use Nette\SmartObject;
use Nette\Security\User;
use Nette\Database\Context;
use Tracy\Debugger;
class TodoService {

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
		COLUMN_NODE_DONE = 'node_done',
                COLUMN_POSITION = 'position';

	public function __construct(Session $session, Context $database, User $user) {

		$this->sessionToDo = $session->getSection('sessionToDo');
		$this->user = $user;
		$this->database = $database;
	}

	public function getNodes() {

		if ($this->user->getIdentity()) {
			return $this->database->table(self::TABLE_NAME)
                                ->where(self::COLUMN_USER_ID, [$this->user->getIdentity()->id])
                                ->order("position ASC");
		}else {
			return $this->sessionToDo->nodes;
		}
	}

	public function getNode($id) {

		if ($this->user->getIdentity()) {
			return $this->database->table(self::TABLE_NAME)
                                    ->where(self::COLUMN_NODE_ID, [$id]);
		}else {
                    $nodes = $this->sessionToDo->nodes;
                    foreach($nodes as $key => $val){
                        if($val['node_id'] == $id){
                            return $nodes[$key];
                            break;
                            }
                        }
		}
	}

	public function addNode($value) {

		if ($this->user->getIdentity()) {
                    
                    //uložení poslední node z db
                    $lastNode = $this->database->table(self::TABLE_NAME)
                            ->where(self::COLUMN_USER_ID, $this->user->getIdentity()->id)
                            ->order(self::COLUMN_POSITION .' DESC')
                            ->limit(1)
                            ->fetch();
                    //vložení nové node
                    $this->database->table(self::TABLE_NAME)
                            ->insert([self::COLUMN_NODE      => $value,
                                      self::COLUMN_USER_ID   => $this->user->getIdentity()->id,
                                      self::COLUMN_NODE_DONE => '',
                                      self::COLUMN_POSITION  => $lastNode['position'] !== null ? $lastNode['position'] +1 : 0]);
                              
		}else {
			if (isset($this->sessionToDo->nodes)) {
                                //jaká je nejvyšší pozice
                                $lastNodePos = end($this->sessionToDo->nodes);
                                
                                //jaké je nejvyšší id
                                $nodes = $this->sessionToDo->nodes;
                                $max = -1;
                                $maxNodeId = null;
                                foreach($nodes as $key=>$val){
                                    if($val['node_id']>$max){
                                       $max = $val['node_id'];
                                       $maxNodeId = $val;
                                    }
                                }
				$this->sessionToDo->nodes[] = ["node" => $value, "node_done" => '', "node_id" => $maxNodeId['node_id'] +1, "position" => $lastNodePos['position'] +1];
			}else {
				$this->sessionToDo->nodes[0] = ["node" => $value, "node_done" => '', "node_id" => 1, "position" => 0];
			}Debugger::barDump($this->sessionToDo->nodes);
		}
	}

	public function deleteNode($id, $pos) {

		if ($this->user->getIdentity()) {
                    
                    //uložení deletované node
                    $delNode = $this->database->table(self::TABLE_NAME)
                            ->where(self::COLUMN_NODE_ID, $id)
                            ->order(self::COLUMN_POSITION .' ASC')
                            ->fetch();
                    
                    //delete node
                    $this->database->table(self::TABLE_NAME)
                            ->where(self::COLUMN_NODE_ID, [$id, 
                                    self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                            ->delete();
                    
                    //nahraď position u nodes, kde je position větší než u deletované
                    $this->database->table(self::TABLE_NAME)
                            ->where(self::COLUMN_POSITION.' > ?', $delNode['position'])
                            ->where( self::COLUMN_USER_ID, $this->user->getIdentity()->id)
                            ->update([self::COLUMN_POSITION.'-=' => 1]);
                            
		}else {
                        $delNode = $this->sessionToDo->nodes[$pos];
                        $nodes = $this->sessionToDo->nodes;
                        
                        //aktualizuje pozice
                        foreach($nodes as $key => $val){
                            if($val['position'] > $delNode['position']){
                                $nodes[$key]['position'] = $val['position']-1;
                            }
                        }
                        //vymaže požadovaný záznam a přeřadí prvky od indexu 0
                        unset($nodes[$pos]);
                        $nodes = array_values($nodes);
			$this->sessionToDo->nodes = $nodes;
		}
	}

	public function editNode($id, $value) {
        //update node dle vstupu uživatele
		if ($this->user->getIdentity()) {
                    $this->database->table(self::TABLE_NAME)
                            ->where(self::COLUMN_NODE_ID, [$id,
                                    self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                            ->update([self::COLUMN_NODE => $value]);
		}else {
                        $nodes = $this->sessionToDo->nodes;
                        foreach($nodes as $key => $val){
                            if($val['node_id'] == $id){
                                $nodes[$key]['node'] = $value;
                                break;
                            }
                        }
                        $this->sessionToDo->nodes = $nodes;
                    }
	}

	public function doneNode($id, $done) {
            //označení node jako 'done'
		if ($this->user->getIdentity()) {
			$this->database->table(self::TABLE_NAME)
                                ->where(self::COLUMN_NODE_ID, [$id,
                                        self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
				->update([self::COLUMN_NODE_DONE => $done]);
		}else {
                        $nodes = $this->sessionToDo->nodes;
                        foreach($nodes as $key => $val){
                            if($val['node_id'] == $id){
                                $nodes[$key]['node_done'] = $done;
                                break;
                            }     
                    }
                    $this->sessionToDo->nodes = $nodes;
		}
	}
        
        public function editOrder($order){
            //správa pořadí nodes
            if ($this->user->getIdentity()){
                foreach($order as $key => $value){
                    $this->database->table(self::TABLE_NAME)
                         ->where(self::COLUMN_NODE_ID, [$value,
                                 self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                         ->update([self::COLUMN_POSITION => $key]);
                    }
            }else{
                $nodes = $this->sessionToDo->nodes;
                $order = array_flip($order);
                
                foreach($nodes as $key => $val){
                    $nodes[$key]['position'] = $order[$val['node_id']];
                }
                usort($nodes, array($this, 'sortByPosition'));
                
                $this->sessionToDo->nodes = $nodes;
            }
        }
        
        public function dropNodes() {

		unset($this->sessionToDo->id);
		unset($this->sessionToDo->nodes);
	}
        

        public function sortByPosition($node1, $node2) {
            return strnatcmp($node1['position'], $node2['position']);
        }
}

