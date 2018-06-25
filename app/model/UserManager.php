<?php
declare(strict_types=1);
namespace App\Model;
use Nette;
use Nette\Security as NS;
use Nette\Security\Passwords;
use NS\User;
/**
 * Users management.
 */
class UserManager implements NS\IAuthenticator
{
	use Nette\SmartObject;
        
         const
             TABLE_NAME = 'users',
             COLUMN_ID = 'id',
             COLUMN_NAME = 'username',
             COLUMN_PASSWORD_HASH = 'password',
             COLUMN_ROLE = 'role';
        
	/** @var Nette\Database\Context */
	private $database;
        
        public function __construct(Nette\Database\Context $database)
	{
		$this->database = $database;
	}
	/**
	 * Performs an authentication.
	 * @throws Nette\Security\AuthenticationException
	 */
        function authenticate(array $credentials)
        {
            list($username, $password) = $credentials;
            $row = $this->database->table('users')
                ->where('username', $username)->fetch();

            if (!$row) {
                throw new NS\AuthenticationException('User not found.');
            }

            if (!NS\Passwords::verify($password, $row->password)) {
                throw new NS\AuthenticationException('Invalid password.');
            }
            return new \Nette\Security\Identity($row[self::COLUMN_ID], null, $row);
           
        }

	/**
	 * Adds new user.
	 * @throws DuplicateNameException
	 */
	public function add(string $username,string $password): void
	{
		try {
                         $this->database->table(self::TABLE_NAME)->insert(array(
                             self::COLUMN_NAME => $username,
                             self::COLUMN_PASSWORD_HASH => Passwords::hash($password),
                          ));

		} catch (Nette\Database\UniqueConstraintViolationException $e) {
			throw new DuplicateNameException;
		}
	}
         public function duplicity($column, $username){
                 $count = $this->database->table(self::TABLE_NAME)->where($column, $username)->count();
                 if($count > 0){
                     return true;
                 }else{
                     return false;
                 }                 
}
           
         public function getUsers(){
                 return $this->database->table(self::TABLE_NAME);
                 }
         public function deleteUser($id){
                 $this->database->query('DELETE FROM users WHERE id = ?', $id);
                 }                        

}