<?php

namespace App\Presenters;

use Nette;
use App\Model\DataManager\UserDataManager;
use Nette\Application\UI\Form;

class SignPresenter extends BasePresenter{
    
    /** @var UserDataManager @inject*/
    public $userManager;
    
    protected function createComponentSignInForm()
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')
            ->setRequired('Prosím vyplňte své uživatelské jméno.');

        $form->addPassword('password', 'Heslo:')
            ->setRequired('Prosím vyplňte své heslo.');

        $form->addSubmit('send', 'Přihlásit');

        $form->onSuccess[] = [$this, 'signInFormSucceeded'];
        return $form;
    }
    
        public function signInFormSucceeded(Form $form, Nette\Utils\ArrayHash $values)
    {
        try {
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Homepage:');

        }catch (Nette\Security\AuthenticationException $e) {
            $form->addError('Nesprávné přihlašovací jméno nebo heslo.');
        }
    }
    
    protected function createComponentRegistrationForm()
    {
        $form = new Form;
        $form->addText('username', 'Uživatelské jméno:')
             ->addRule([$this, 'duplicityUsername'], 'Uživateské jméno již existuje.')
             ->setRequired('Prosím vyplňte své uživatelské jméno.');

         $form->addPassword('password', 'Heslo')
            ->setRequired()
            ->addRule(Form::MIN_LENGTH, 'Položka %label musí obsahovat min. %d znaků', 5)
            ->addRule(Form::MAX_LENGTH, 'Položka %label může obsahovat max. %d znaků', 255); 
         
        $form['password']->getControlPrototype()->autocomplete('off');
        
        $form->addPassword('password_again', 'Heslo (znovu)')
            ->setRequired()
            ->addConditionOn($form['password'], Form::FILLED)   
            ->addRule(Form::EQUAL, "Hesla se musí shodovat!", $form["password"])
            ->addRule(Form::MIN_LENGTH, 'Položka %label musí obsahovat min. %d znaků', 5)
            ->addRule(Form::MAX_LENGTH, 'Položka %label může obsahovat max. %d znaků', 255);     
        
        $form->addEmail('email', 'Email')
            ->setRequired('Prosím vyplňte svůj email.');

        $form->addSubmit('send', 'Registrovat');

        $form->onSuccess[] = [$this, 'RegistrationFormSucceeded'];
        return $form;
    }
    
    public function duplicityUsername($item)
    {
       if($this->userManager->duplicity($item->value)){
            return false;
       }else{
           return true;
       }
       
    }
    
    public function registrationFormSucceeded(Form $form, Nette\Utils\ArrayHash $values)
    {
        $this->userManager->add($values->username, $values->password);
        $this->getUser()->login($values->username, $values->password);
        $this->flashMessage('Byl jste úspěšně zaregistrován', 'success');
        $this->redirect('Homepage:');
        
    }
    public function actionOut()
    {
       $this->getUser()->logout(true);
       
       $this->flashMessage('Odhlášení bylo úspěšné.');
       $this->redirect('Homepage:');
    }
    
    public function renderProfile()
    {
            $this->template->users = $this->userManager->getUsers();

    }
    
    public function handleDelete($id){
            
            $this->userManager->deleteUser($id);
            $this->getPresenter()->postGet('this');
            $this->redrawControl('usersListContainer');
        
        }
}

