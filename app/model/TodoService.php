<?php

namespace App\Model;

use Nette;
use Nette\SmartObject;
use Nette\Security\User;
use Nette\Database\Context;
use Tracy\Debugger;
class TodoService {

	use SmartObject;

	/** @var Nette\Database\Context */
	private $database;
	/** @var Nette\Security\User */
	private $user;

	const
		TABLE_NODES = 'nodes',
                TABLE_SUBNODES = 'subnodes',
                COLUMN_ID = 'id',
		COLUMN_NODE_ID = 'node_id',
		COLUMN_NODE = 'node',
		COLUMN_USER_ID = 'user_id',
		COLUMN_NODE_DONE = 'node_done',
                COLUMN_SUBNODE_DONE = 'subnode_done',
                COLUMN_SUBNODE = 'subnode',
                COLUMN_POSITION = 'position',
                COLUMN_DATE = 'date';

	public function __construct(Context $database, User $user) {
		$this->user = $user;
		$this->database = $database;
	}

	public function getNodes() {

		if ($this->user->getIdentity()){
			return $this->database->table(self::TABLE_NODES)
                                ->where(self::COLUMN_USER_ID, [$this->user->getIdentity()->id])
                                ->order("position ASC")->fetchAll();
		}
	}
        
        public function getSubnode($id) {

		if ($this->user->getIdentity()) {
			return $this->database->table(self::TABLE_SUBNODES)
                                    ->where(self::COLUMN_NODE_ID, [$id])
                                    ->order("position ASC")->fetchAll();
		}
	}
        
        public function getNodeIdbySubnode($id) {

		if ($this->user->getIdentity()) {
			return $this->database->table(self::TABLE_SUBNODES)
                                    ->where(self::COLUMN_ID, [$id])->fetch();
		}
	}
        
	public function getNode($nodeId) {

		if ($this->user->getIdentity()) {
			return $this->database->table(self::TABLE_NODES)
                                    ->where(self::COLUMN_NODE_ID, [$nodeId])->fetchAll();
		}
	}
        public function getLastNode() {

		if ($this->user->getIdentity()) {
                    return $lastNode = $this->database->table(self::TABLE_NODES)
                        ->where(self::COLUMN_USER_ID, $this->user->getIdentity()->id)
                        ->order(self::COLUMN_POSITION .' DESC')
                        ->limit(1)
                        ->fetch();
		}
	}
        public function getLastSubnode($id) {

		if ($this->user->getIdentity()) {
                    return $this->database->table(self::TABLE_SUBNODES)
                        ->where(self::COLUMN_NODE_ID, $id)
                        ->order(self::COLUMN_POSITION .' DESC')
                        ->limit(1)
                        ->fetch();
		}
	}

	public function addNode($value, $date) {

		if ($this->user->getIdentity()) {
                    //uložení poslední node z db
                    $lastNode = $this->getLastNode();
                    //vložení nové node
                    $this->database->table(self::TABLE_NODES)
                            ->insert([self::COLUMN_NODE      => $value,
                                      self::COLUMN_USER_ID   => $this->user->getIdentity()->id,
                                      self::COLUMN_NODE_DONE => '',
                                      self::COLUMN_POSITION  => $lastNode['position'] !== null ? $lastNode['position'] +1 : 0,
                                      self::COLUMN_DATE => $date]);     
                 }
	}
        
        public function addSubnode($value, $nodeId = null) {
            
		if ($this->user->getIdentity()) {
                    
                     $lastSubnode = $this->getLastSubnode($nodeId);
                     
                     $this->database->table(self::TABLE_SUBNODES)
                            ->insert([self::COLUMN_SUBNODE  => $value,
                                      self::COLUMN_USER_ID => $this->user->getIdentity()->id,
                                      self::COLUMN_SUBNODE_DONE => '',
                                      self::COLUMN_POSITION  => $lastSubnode['position'] !== null ? $lastSubnode['position'] +1 : 0,
                                      self::COLUMN_NODE_ID => $nodeId]);
                    

                     $this->database->table(self::TABLE_NODES)
                                ->where(self::COLUMN_NODE_ID, [$nodeId,
                                        self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
				->update([self::COLUMN_SUBNODE => 1]);
                }
		
	}

	public function deleteNode($nodeId) {

		if ($this->user->getIdentity()) {
                    
                    //uložení deletované node
                    $delNode = $this->database->table(self::TABLE_NODES)
                            ->where(self::COLUMN_NODE_ID, $nodeId)
                            ->order(self::COLUMN_POSITION .' ASC')
                            ->fetch();
                    
                    $this->database->table(self::TABLE_SUBNODES)
                            ->where(self::COLUMN_NODE_ID, [$nodeId, 
                                    self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                            ->delete();
                    //delete node
                    $this->database->table(self::TABLE_NODES)
                            ->where(self::COLUMN_NODE_ID, [$nodeId, 
                                    self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                            ->delete();
                    
                    //nahraď position u nodes, kde je position větší než u deletované
                    $this->database->table(self::TABLE_NODES)
                            ->where(self::COLUMN_POSITION.' > ?', $delNode['position'])
                            ->where( self::COLUMN_USER_ID, $this->user->getIdentity()->id)
                            ->update([self::COLUMN_POSITION.'-=' => 1]);
                            
		}
	}
        
        public function deleteSubnode($id) {

		if ($this->user->getIdentity()) {
                    //uložení deletované subnode
                    $delNode = $this->database->table(self::TABLE_SUBNODES)
                            ->where(self::COLUMN_ID, $id)
                            ->order(self::COLUMN_POSITION .' ASC')
                            ->fetch();
                    //delete subnode
                    $this->database->table(self::TABLE_SUBNODES)
                            ->where(self::COLUMN_ID, [$id, 
                                    self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                            ->delete();
                    //nahraď position u subnode, kde je position větší než u deletované
                    $this->database->table(self::TABLE_SUBNODES)
                            ->where(self::COLUMN_POSITION.' > ?', $delNode['position'])
                            ->where(self::COLUMN_ID, $id)
                            ->where(self::COLUMN_USER_ID, $this->user->getIdentity()->id)
                            ->update([self::COLUMN_POSITION.'-=' => 1]);
                            
		}
	}


	public function editNode($nodeId, $value, $date) {
        //update node dle vstupu uživatele
		if ($this->user->getIdentity()) {
                    $this->database->table(self::TABLE_NODES)
                            ->where(self::COLUMN_NODE_ID, [$nodeId,
                                    self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                            ->update([self::COLUMN_NODE => $value]);
		}
	}
        
        public function editSubnode($id, $value) {
        //update subnode dle vstupu uživatele
		if ($this->user->getIdentity()) {
                    $this->database->table(self::TABLE_SUBNODES)
                            ->where(self::COLUMN_ID, [$id,
                                    self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                            ->update([self::COLUMN_SUBNODE => $value]);
		}
	}

	public function doneNode($nodeId, $done) {
            //označení node jako 'done'
		if ($this->user->getIdentity()) {
			$this->database->table(self::TABLE_NODES)
                                ->where(self::COLUMN_NODE_ID, [$nodeId,
                                        self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
				->update([self::COLUMN_NODE_DONE => $done]);
		}
	}
        
        public function editOrder($order, $table, $id = null){
            //správa pořadí nodes
            if ($this->user->getIdentity()){
                if($table == self::TABLE_SUBNODES){
                    foreach($order as $key => $value){
                        $this->database->table(self::TABLE_SUBNODES)
                             ->where(self::COLUMN_ID, [$value,
                                     self::COLUMN_USER_ID, [$this->user->getIdentity()->id],
                                    self::COLUMN_NODE_ID, $id])
                             ->update([self::COLUMN_POSITION => $key]);
                    }
                }else{
                   foreach($order as $key => $value){
                        $this->database->table(self::TABLE_NODES)
                             ->where(self::COLUMN_NODE_ID, [$value,
                                     self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
                             ->update([self::COLUMN_POSITION => $key]);
                    }
                }

            }
        }
        
        public function editNodeHasSubnodes($nodeId, $index) {
            
		if ($this->user->getIdentity()) {
			$this->database->table(self::TABLE_NODES)
                                ->where(self::COLUMN_NODE_ID, [$nodeId,
                                        self::COLUMN_USER_ID, [$this->user->getIdentity()->id]])
				->update([self::COLUMN_SUBNODE => $index]);
		}
	}
        
}

