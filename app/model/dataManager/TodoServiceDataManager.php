<?php

namespace App\Model\DataManager;

use App\Model\Repository\NodesRepository;
use App\Model\Repository\SubnodesRepository;
use App\Model\DataManager\UserDataManager;

class TodoServiceDataManager {

	/** @var NodesRepository @inject */
	public $nodeRepository;
        
	/** @var SubnodesRepository @inject */
	public $subnodeRepository;

        /** @var UserDataManager @inject */
	public $userDataManager;
        
        public function getNode($id) {
                 if ($this->userDataManager->isLogged()) {
			return $this->nodeRepository->fetchAllByNodeId($id);
		} else {
			return null;
		}
	}
        
        public function getNodes(): array {
            if ($this->userDataManager->isLogged()) {
                    return $this->nodeRepository->fetchAllByUser($this->userDataManager->getLoggedUserId());
            } else {
                    return [null];
            }
	}
        
        public function getSubnode($id) {
            if ($this->userDataManager->isLogged()) {
                    return $this->subnodeRepository->fetchAllSubnodesByNodeId($id);
            } else {
                    return null;
            }
	}
        
        public function getLastNode() {
            
            if ($this->userDataManager->isLogged()) {
                    return $this->nodeRepository->fetchLastByUser($this->userDataManager->getLoggedUserId());
            } else {
                    return null;
            }
	}
        
        public function getLastSubnode($id) {
            if ($this->userDataManager->isLogged()) {
                    return $this->subnodeRepository->fetchLastByNodeId($id);
            } else {
                    return null;
            }
	}
        
        public function addNode($value, $date) {
            $lastNode = $this->getLastNode();
             
            if ($this->userDataManager->isLogged()) {
                    return $this->nodeRepository
                            ->insertNode($value, $date, $this->userDataManager->getLoggedUserId(), $lastNode);
            } else {
                    return null;
            }
	}
        
        public function deleteNode($nodeId) {
            if ($this->userDataManager->isLogged()) {
                  $delNode = $this->nodeRepository->fetchByNodeId($nodeId);
                  $this->subnodeRepository->deleteSubnode($nodeId, $this->userDataManager->getLoggedUserId());
                  $this->nodeRepository->deleteNode($nodeId, $this->userDataManager->getLoggedUserId());
                  $this->nodeRepository->updateNodesPosition($delNode['position'], $this->userDataManager->getLoggedUserId());
            } else {
                    return null;
            }
	}
        
        public function editNode($nodeId, $value, $date) {
            if ($this->userDataManager->isLogged()) {
                    return $this->nodeRepository
                            ->updateSingleNode($nodeId, $value, $date, $this->userDataManager->getLoggedUserId());
            } else {
                    return null;
            }
	}
        
        public function doneNode($nodeId, $done) {
            if ($this->userDataManager->isLogged()) {
                    return $this->nodeRepository
                            ->updateNodeDone($nodeId, $this->userDataManager->getLoggedUserId(), $done);
            } else {
                    return null;
            }
	}
        
        public function editNodeHasSubnode($nodeId, $index) {
            if ($this->userDataManager->isLogged()) {
                    return $this->nodeRepository
                            ->updateNodeHasSubnode($nodeId, $this->userDataManager->getLoggedUserId(),$index);
            } else {
                    return null;
            }
	}
        
        public function addSubnode($value, $nodeId) {
            $lastSubnode = $this->getLastSubnode($nodeId);
           
            if ($this->userDataManager->isLogged()) {
                    $this->subnodeRepository
                            ->insertSubnode($value, $nodeId, $this->userDataManager->getLoggedUserId(), $lastSubnode);
                    $this->nodeRepository
                             ->updateNodeHasSubnode($nodeId, $this->userDataManager->getLoggedUserId(), "1");
            } else {
                    return null;
            }
	}
        
        public function deleteSubnode($nodeId) {
           if ($this->userDataManager->isLogged()) {
                  $delNode = $this->subnodeRepository->findAllById($nodeId)->fetch();
                  $this->subnodeRepository->deleteSubnode($nodeId, $this->userDataManager->getLoggedUserId());
                  $this->subnodeRepository->updateSubnodesPosition($nodeId, $delNode['position'], $this->userDataManager->getLoggedUserId());
            } else {
                    return null;
            }
	}
        
        public function editSubnode($id, $value) {
            if ($this->userDataManager->isLogged()) {
                    return $this->subnodeRepository
                            ->updateSingleSubnode($id, $value, $this->userDataManager->getLoggedUserId());
            } else {
                    return null;
            }
	}
        
        public function editOrder($order, $table, $id = null) {
            if ($this->userDataManager->isLogged()) { 
                if($table == "subnodes") {
                     foreach($order as $key => $value){
                        $this->subnodeRepository
                            ->editOrder($key, $value, $this->userDataManager->getLoggedUserId(), $id);
                     }
                } else if($table == "nodes") {
                    foreach($order as $key => $value){
                        $this->nodeRepository
                            ->editOrder($key, $value, $this->userDataManager->getLoggedUserId());
                     }
                }
            } else {
                    return null;
            }
	}
        
}