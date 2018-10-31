<?php
declare(strict_types=1);
namespace App\Model\DataManager;
use Nette;
use Nette\Security as NS;
use App\Model\Repository\UserRepository;
/**
 * Users management.
 */ 
class UserAuthenticateDataManager implements NS\IAuthenticator
{
	/** @var UserRepository @inject */
	public $userRepository;
        
        function authenticate(array $credentials)
        {
            list($username, $password) = $credentials;
            $row = $this->userRepository->fetchSingleByUsername($username);
            if (!$row) {
                throw new NS\AuthenticationException('User not found.');
            }

            if (!NS\Passwords::verify($password, $row->password)) {
                throw new NS\AuthenticationException('Invalid password.');
            }
            return new \Nette\Security\Identity($row['id'], null, $row);
           
        }              

}