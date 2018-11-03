<?php

namespace App\Model\Repository;

use Nette\Database\Table\Selection;
use Nette\Database\Table\ActiveRow;

class NodesRepository extends BaseRepository {

    protected $table = "nodes";
    protected $columnUserId = "user_id";
    protected $columnNodeId = "node_id";
    protected $columnPosition = "position";
    protected $columnNode = "node";
    protected $columnDone = "node_done";
    protected $columnDate = "date";
    protected $columnSubnode = "subnode";

    public function findAllByUser($userId): Selection {
            return $this->findAll()->where($this->columnUserId, $userId);
    }

    public function fetchAllByUser($userId): array {
            return $this->findAll()
                    ->where($this->columnUserId, [$userId])
                    ->order($this->columnPosition." ASC")
                    ->fetchAll();
    }
    
    public function fetchAllByNodeId($nodeId): array {
            return $this->findAll()
                    ->where($this->columnNodeId, [$nodeId])
                    ->order($this->columnPosition." DESC")
                    ->fetchAll();
    }
    
    public function fetchByNodeId($nodeId) {
            return $this->findAll()
                    ->where($this->columnNodeId, $nodeId)
                    ->order($this->columnPosition." ASC")
                    ->fetch();
    }
    
    public function fetchLastByUser($userId) {
            return $this->findAll()
                    ->where($this->columnUserId, $userId)
                    ->order($this->columnPosition." DESC")
                    ->limit(1)
                    ->fetch();
    }
    
    public function insertNode($value, $date, $userId, $lastNode): void{
            $this->findAll()
                    ->insert([$this->columnNode   => $value,
                            $this->columnUserId   => $userId,
                            $this->columnDone     => '',
                            $this->columnPosition => $lastNode['position'] !== null ? $lastNode['position'] +1 : 0,
                            $this->columnDate     => $date]);
    }
    
    public function deleteNode($nodeId, $userId): void{
            $this->findAll()
                    ->where($this->columnNodeId, [$nodeId])
                    ->where($this->columnUserId, $userId)
                    ->delete();
    }
    
    public function updateNodesPosition($position, $userId): void{
            $this->findAll()
                    ->where($this->columnPosition." > ?", $position)
                    ->where($this->columnUserId, $userId)
                    ->update([$this->columnPosition.'-=' => 1]);
    }
    
    public function updateSingleNode($nodeId, $value, $date, $userId): void{
            $this->findAll()
                    ->where($this->columnNodeId, [$nodeId])
                    ->where($this->columnUserId, $userId)
                    ->update([$this->columnNode => $value,
                              $this->columnDate => $date]);
    }
    
    public function updateNodeDone($nodeId, $userId, $done): void{
            $this->findAll()
                    ->where($this->columnNodeId, [$nodeId])
                    ->where($this->columnUserId, $userId)
                    ->update([$this->columnDone => $done]);
    }
    
    public function updateNodeHasSubnode($nodeId, $userId, $index = null): void{
            $this->findAll()
                    ->where($this->columnNodeId, [$nodeId])
                    ->where($this->columnUserId, $userId)
                    ->update([$this->columnSubnode => $index]);
    }
    
    public function editOrder($position, $nodeId, $userId): void{
            $this->findAll()
                    ->where($this->columnNodeId, [$nodeId])
                    ->where($this->columnUserId, $userId)
                    ->update([$this->columnPosition => $position]);
    }
}