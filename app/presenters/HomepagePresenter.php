<?php

namespace App\Presenters;

use App\Model\TodoService;
use App\Components\TodoControl;

class HomepagePresenter extends BasePresenter{

    /**
     * @var TodoService
     */
    private $todoService;

    public function __construct(TodoService $todoService){
        
		parent::__construct();
		$this->todoService = $todoService;
	}
        
    	public function actionDefault(){
            
            $this->template->nodes = $this->todoService->getNodes();
	}
        /*
	 *
	 * @return   TodoControl|NULL
	 */
        protected function createComponentTodo() {
            
            $control = new TodoControl();
            $control->setService($this->todoService);
            return $control;
        }
        
        public function actionDrop(){ 
            
           $control = $this->getComponent('todo');
           $control->drop();
           $this->redirect('Homepage:');
        }   
}