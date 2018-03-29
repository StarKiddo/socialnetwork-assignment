<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;


class ProfilePresenter extends BasePresenter
{

    public function renderDefault()
    {
        $user = $this->getUser();
        $this->template->user = $this->database->table('users')
							  ->where('id', $user->id)
							  ->fetch();

    }

    public function renderResult($searchedString = '')
    {
        $friends = $this->database->table('users');
        // Useři odpovídající hledání
        $searchResult = array();

        // Projede všechny usery a hledá shodu
        if ($searchedString != '')
        {
			// Vyhledávání
            foreach ($friends as $friend)
            {
				$push = false;

				// Podle jména
				if (strpos(strtolower($friend->name), strtolower($searchedString)) !== false)
				{
					$push = true;
				}
				// Podle příjmení
				if (strpos(strtolower($friend->lastname), strtolower($searchedString)) !== false)
				{
					$push = true;
				}
                if (strpos(strtolower($friend->email), strtolower($searchedString)) !== false)
				{
					$push = true;
				}

				if ($push)
				{
					array_push($searchResult, $friend);
				}
            }
        }
		else
		{
			$searchResult = $friends;
		}
        $this->template->friends = $searchResult;

        $this->template->linkedFriendsIds = $this->database->table('friends')->where('idUser1', $this->getUser()->id)->fetchPairs('idUser2', 'idUser1');
    }

    public function renderElse($id)
    {
        $this->template->user = $this->database->table('users')
							  ->where('id', $id)
							  ->fetch();
        $this->template->linkedFriendsIds = $this->database->table('friends')->where('idUser1', $this->getUser()->id)->fetchPairs('idUser2', 'idUser1');

    }

    public function createComponentUploadProfForm()
    {
        // Vytvoří nový form
        $form = new Form;
        // Tlačítko pro vybrání souboru
        $form->addUpload('image')
             ->setRequired('Prosím vyberte soubor');
        // Odeslání
        $form->addSubmit('send', 'Nahrát');
        // Callback logiky
        $form->onSuccess[] = array($this, 'uploadProfFormSucceeded');

        // Vrátí formulář
        return $form;
    }

    public function uploadProfFormSucceeded(Form $form, $values)
    {
        $oldPic = $this->database->table('users')
							  ->where('id', $this->getUser()->id)
							  ->fetch()->profPicPath;

        // Práce se souborem
        $image = $values->image;
        $image->move('img/'.$values->image->name);
        $this->database->table('users')->where('id', $this->getUser()->id)->update(['profPicPath' => 'img/'.$values->image->name]);
        if ($oldPic != "img/main-pic.png") {
            FileSystem::delete($oldPic);

        }
        $this->redirect('this');
    }
    public function createComponentUploadBackForm()
    {
        // Vytvoří nový form
        $form = new Form;
        // Tlačítko pro vybrání souboru
        $form->addUpload('image')
             ->setRequired('Prosím vyberte soubor');
        // Odeslání
        $form->addSubmit('send', 'Nahrát');
        // Callback logiky
        $form->onSuccess[] = array($this, 'uploadBackFormSucceeded');

        // Vrátí formulář
        return $form;
    }

    public function uploadBackFormSucceeded(Form $form, $values)
    {
        $oldPic = $this->database->table('users')
							  ->where('id', $this->getUser()->id)
							  ->fetch()->headerPicPath;

        // Práce se souborem
        $image = $values->image;
        $image->move('img/'.$values->image->name);
        $this->database->table('users')->where('id', $this->getUser()->id)->update(['headerPicPath' => 'img/'.$values->image->name]);
        if ($oldPic != "img/main-pic.png") {
            FileSystem::delete($oldPic);

        }
        $this->redirect('this');
    }

    public function actionAddfriend($id)
    {
        if(!$c=$this->database->table('friends')->where('idUser1 = ? AND idUser2 = ? OR idUser1 = ? AND idUser2 = ?', $id, $this->getUser()->id, $this->getUser()->id, $id)->count())
        {
            $this->database->table('friends')->insert([
                'idUser1' => $this->getUser()->id,
                'idUser2' => $id
            ]);

            $this->database->table('friends')->insert([
                'idUser1' => $id,
                'idUser2' => $this->getUser()->id
            ]);
        }
        else {
                $this->database->table('friends')->where('idUser1 = ? AND idUser2 = ? OR idUser1 = ? AND idUser2 = ?', $id, $this->getUser()->id, $this->getUser()->id, $id)->delete();
        }

        $this->redirect('Profile:else', $id);

    }
}
