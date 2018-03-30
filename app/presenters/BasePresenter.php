<?php

namespace App\Presenters;

use Nette;
use Tracy\Debugger;
use Nette\Application\UI\Form;
use App\Forms;

// Base presenetr pro všechny apliakční presentery
abstract class BasePresenter extends Nette\Application\UI\Presenter
{

        // Databáze
        public $database;

        // Session
        public $session;

        // Sekce
        public $mySection;

    // Konstruktor
    function __construct(Nette\Database\Context $database, Nette\Http\Session $session)
    {
      \Tracy\Debugger::enable(\Tracy\Debugger::DEVELOPMENT);
        $this->database = $database;
        $this->session = $session;
        $this->mySection = $session->getSection('franta');
    }

	/**
	 * Sign-in form factory.
	 */

	 public function createComponentSearchForm()
	{
		// Vytvoří nová formulář
		$form = new Form;

		$form->addText('name', 'name')
			->setRequired('Please enter searched human being.');

		$form->addSubmit('send', 'Search');
		// Callback logiky
		$form->onSuccess[] = array($this, 'searchFormSucceeded');

		// Vrátí instanci formuláře
		return $form;
	}

	// Po úspěšném odeslání vyhledávacího formuláře
	public function searchFormSucceeded(Form $form, $values)
	{
		$this->redirect('Profile:result', $values->name);
	}

	//protected function createComponentSearchForm(): Form
	//{
    //    return $this->searchFactory->create();
	//}
    public function beforeRender()
	{
		// Získá usera
		$user = $this->getUser();

		// Zjistí jestli je user přihlášený a když ne, přesměruje na login.
		if (!$user->isLoggedIn())
		{
			$this->redirect('Sign:in');
		}

	}
}
