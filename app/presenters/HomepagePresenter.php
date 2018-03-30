<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\Strings;


class HomepagePresenter extends BasePresenter
{
    public function renderDefault()
    {

        $this->template->stuffs = $this->database->table('stuff')->order('addAt DESC');
        $this->template->user = $this->database->table('users')
							  ->where('id', $this->getUser()->id)
							  ->fetch();

        $this->template->users = $this->database->table('users');
        $this->template->likes = $this->database->table('likes')->where('userId', $this->getUser()->id)->fetchPairs('stuffId', 'userId');
        $this->template->linkedFriendsIds = $this->database->table('friends')->where('idUser1', $this->getUser()->id)->fetchPairs('idUser2', 'idUser1');
    }
    public function createComponentStuffForm()
    {
        // Vytvoří nový form
        $form = new Form;
        // Popis dokumenu
		$form->addText('text', '')
			 ->setRequired('');
        // Odeslání
        $form->addSubmit('send', 'Nahrát');
        // Callback logiky
        $form->onSuccess[] = array($this, 'stuffFormSucceeded');

        // Vrátí formulář
        return $form;
    }

    public function stuffFormSucceeded(Form $form, $values)
    {
        $videoPath;
        if (Strings::contains($values->text, "www.youtube.com")) {
            $videoPath = Strings::split($values->text, '~=~');

            $this->database->table('stuff')->insert([
                'content' => $values->text,
                'userId' => $this->getUser()->id,
                'addAt' => date('Y-m-d H:i:s'),
                'pic' => "https://img.youtube.com/vi/".$videoPath[1]."/default.jpg"
            ]);
        }
        else if (Strings::contains($values->text, ".jpg")||Strings::contains($values->text, ".jpeg")||Strings::contains($values->text, ".gif")||Strings::contains($values->text, ".tif")||Strings::contains($values->text, ".bmp")||Strings::contains($values->text, ".png"))
        {
            $this->database->table('stuff')->insert([
                'content' => $values->text,
                'userId' => $this->getUser()->id,
                'addAt' => date('Y-m-d H:i:s'),
                'pic' => $values->text
            ]);
        }
        else
        {
            $this->database->table('stuff')->insert([
                'content' => $values->text,
                'userId' => $this->getUser()->id,
                'addAt' => date('Y-m-d H:i:s')
            ]);
        }
        $this->redirect('this');
    }

    public function actionLike($stuffId)
    {
        if (!$c=$this->database->table('likes')->where('stuffId = ? AND userId = ?', $stuffId, $this->getUser()->id)->count())
        {
            $this->database->table('likes')->insert([
                'stuffId' => $stuffId,
                'userId' => $this->getUser()->id
            ]);
        }
        else
        {
            $this->database->table('likes')->where('stuffId = ? AND userId = ?', $stuffId, $this->getUser()->id)->delete();
        }
        $this->redirect('Homepage:');
    }
    public function actionDelete($stuffId)
    {
        $this->database->table('stuff')->where('id = ?', $stuffId)->delete();

        $this->redirect('Homepage:');
    }

    public function actionShare($text, $id, $picPath)
    {
            $this->database->table('stuff')->insert([
            'content' => $text,
            'shareId' => $this->getUser()->id,
            'addAt' => date('Y-m-d H:i:s'),
            'userId' => $id,
            'pic' => $picPath
        ]);
        $this->redirect('Homepage:');
    }
}
