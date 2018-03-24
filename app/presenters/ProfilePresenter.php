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
}
