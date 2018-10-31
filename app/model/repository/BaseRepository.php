<?php

namespace App\Model\Repository;

use Nette\Database\Context;

abstract class BaseRepository {

    /** @var Context */
    protected $db;

    protected $table = null;

    function __construct(Context $db) {
            $this->db = $db;
    }

    public function findAll() {
            return $this->db->table($this->table);
    }

    public function findAllById($id) {
            return $this->findAll()->where("id", $id);
    }
}