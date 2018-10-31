<?php
declare(strict_types=1);
namespace App\Model\DataManager;

use Nette;
use Nette\Security\Passwords;
use Nette\Security\User;
use App\Model\Repository\UserRepository;
/**
 * Users management.
 */ 
class UserDataManager
{
	/** @var UserRepository @inject */
	public $userRepository;
        
        /** @var User */
	public $user;
        
        public function __construct(User $user)
	{
                $this->user = $user;
	}
        
	public function getLoggedUserId(): int {
		return $this->user->getIdentity()->id;
	}

	public function isLogged(): bool {
		return $this->user->getIdentity() ? true : false;
	}
	/**
	 * Adds new user.
	 * @throws DuplicateNameException
	 */
	public function add(string $username,string $password): void
	{
		try {
                    $this->userRepository->insertUser($username, Passwords::hash($password));
		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}
         public function duplicity($username){
                 $count =  $this->userRepository->findByUsername($username)->count();
                 if($count > 0){
                     return true;
                 }else{
                     return false;
                 }                 
        }
           
        public function getUsers(){
                 return $this->userRepository->findAll()->fetchAll();
        }
        
        public function deleteUser($id){
                 $this->userRepository->deleteUser($id);
        }                        

}