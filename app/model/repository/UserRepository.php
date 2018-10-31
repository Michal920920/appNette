<?php

namespace App\Model\Repository;

use Nette\Database\Table\Selection;
use Nette\Database\Table\ActiveRow;

class UserRepository extends BaseRepository {

    protected $table = "users";
    protected $columnUsername = "username";
    protected $columnPassword = "password";
    protected $columnRole = "role";
         

    public function findAllByUserId($userId): Selection {
            return $this->findAll()->where($this->columnUserId, $userId);
    }

    public function insertUser($username, $password) {
            return $this->findAll()
                    ->insert([$this->columnUsername => $username,
                              $this->columnPassword => $password]);
    }
    
    public function findByUsername($username): Selection {
            return $this->findAll()
                    ->where($this->columnUsername, [$username]);
    }
    
    public function fetchSingleByUsername($username): ActiveRow {
            return $this->findAll()
                    ->where($this->columnUsername, [$username])->fetch();
    }
    
    public function deleteUser($userId): void{
            $this->findAll()
                    ->where($this->columnUserId, $userId)
                    ->delete();
    }
}