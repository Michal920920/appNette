<?php

namespace App\Model\Repository;

use Nette\Database\Table\Selection;
use Nette\Database\Table\ActiveRow;

class SubnodesRepository extends BaseRepository {

    protected $table = "subnodes";
    protected $columnId = "id";
    protected $columnUserId = "user_id";
    protected $columnNodeId = "node_id";
    protected $columnPosition = "position";
    protected $columnSubDone = "subnode_done";
    protected $columnSubnode = "subnode";

    public function findAllByUser($userId): Selection {
            return $this->findAll()->where($this->columnUserId, $userId);
    }

    public function fetchAllByUser($userId): array {
            return $this->findAll()
                    ->where($this->columnUserId, $userId)
                    ->order($this->columnPosition." ASC")
                    ->fetchAll();
    }
    public function fetchAllSubnodesByNodeId($nodeId): array {
            return $this->findAll()
                    ->where($this->columnNodeId, $nodeId)
                    ->order($this->columnPosition." ASC")
                    ->fetchAll();
    }
    public function fetchLastByNodeId($nodeId){
            return $this->findAll()
                    ->where($this->columnNodeId, $nodeId)
                    ->order($this->columnPosition." DESC")
                    ->limit(1)
                    ->fetch();
    }
    public function insertSubnode($value, $nodeId, $userId, $lastSubnode): void{
            $this->findAll()
                    ->insert([$this->columnSubnode  => $value,
                            $this->columnUserId   => $userId,
                            $this->columnSubDone  => '',
                            $this->columnPosition => $lastSubnode['position']
                               !== null ? $lastSubnode['position'] +1 : 0,
                            $this->columnNodeId   => $nodeId]);

    }
    public function deleteSubnode($id, $userId): void{
            $this->findAll()
                    ->where($this->columnId, [$id])
                    ->where($this->columnUserId, $userId)
                    ->delete();
    }
    public function updateSubnodesPosition($nodeId, $position, $userId): void{
            $this->findAll()
                    ->where($this->columnPosition." > ?", $position)
                    ->where($this->columnId, $nodeId)
                    ->where($this->columnUserId, $userId)
                    ->update([$this->columnPosition."-=" => 1]);
    }
    public function updateSingleSubnode($id, $value, $userId): void{
            $this->findAll()
                    ->where($this->columnId, [$id])
                    ->where($this->columnUserId, $userId)
                    ->update([$this->columnSubnode => $value]);
    }
    public function editOrder($position, $id, $userId, $nodeId): void{
            $this->findAll()
                    ->where($this->columnId, [$id])
                    ->where($this->columnUserId, $userId)
                    ->where($this->columnNodeId, $nodeId)
                    ->update([$this->columnPosition => $position]);
    }
}