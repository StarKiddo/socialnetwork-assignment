<?php

namespace App\Presenters;

use Nette;
use Tracy\Debugger;

// Base presenetr pro všechny apliakční presentery
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
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
}
