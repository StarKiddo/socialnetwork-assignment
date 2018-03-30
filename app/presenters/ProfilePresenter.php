<?php

namespace App\Presenters;

use Nette;
use Nette\Application\UI\Form;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;

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
        $groups = $this->database->table('groups');
        // Useři odpovídající hledání
        $searchResult = array();
        $searchGroups = array();

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
            foreach ($groups as $group) {
                $push = false;

                // Podle jména
				if (strpos(strtolower($group->name), strtolower($searchedString)) !== false)
				{
					$push = true;
				}

                if ($push)
				{
					array_push($searchGroups, $group);
				}
            }
        }
		else
		{
			$searchResult = $friends;
            $searchGroup = $groups;
		}
        $this->template->friends = $searchResult;
        $this->template->groups = $searchGroups;
        $this->template->linkedFriendsIds = $this->database->table('friends')->where('idUser1', $this->getUser()->id)->fetchPairs('idUser2', 'idUser1');
    }

    public function renderElse($id)
    {
        $this->template->visitedUser = $this->database->table('users')
							  ->where('id', $id)
							  ->fetch();
        $this->template->linkedFriendsIds = $this->database->table('friends')->where('idUser1', $this->getUser()->id)->fetchPairs('idUser2', 'idUser1');
        $user = $this->getUser();
        $this->template->stuffs = $this->database->table('stuff')->where('userId = ? OR shareId = ?', $id, $id)->order('addAt DESC');
        $this->template->likes = $this->database->table('likes')->where('userId', $this->getUser()->id)->fetchPairs('stuffId', 'userId');
        $this->template->users = $this->database->table('users');
        $this->template->friendsCount = $this->database->table('friends')->where('idUser1', $this->getUser()->id)->count();
        $this->template->groupsCount = $this->database->table('members')->where('memberId', $this->getUser()->id)->count();
    }

    public function renderFriends($id)
    {
        $friendsIds =  $this->database->table('friends')->where('idUser1', $id)->fetchPairs('idUser1', 'idUser2');
        $friends = array();
        foreach ($friendsIds as $friendId) {
            array_push($friends, $this->database->table('users')->where('id', $friendId)->fetch());
        }
        $this->template->friends = $friends;
        $this->template->linkedFriendsIds = $this->database->table('friends')->where('idUser1', $id)->fetchPairs('idUser2', 'idUser1');
        $this->template->user = $this->getUser();
    }

    public function renderMembers($id)
    {
        $friendsIds =  $this->database->table('members')->where('groupId', $id)->fetchPairs('groupId', 'memberId');
        $friends = array();
        foreach ($friendsIds as $friendId) {
            array_push($friends, $this->database->table('users')->where('id', $friendId)->fetch());
        }
        $this->template->friends = $friends;
        $this->template->linkedFriendsIds = $this->database->table('members')->where('groupId', $id)->fetchPairs('memberId', 'groupId');
        $this->template->user = $this->getUser();
    }
    public function renderGroups($id)
    {
        $friendsIds =  $this->database->table('members')->where('groupId', $id)->fetchPairs('memberId', 'groupId');
        $friends = array();
        foreach ($friendsIds as $friendId) {
            array_push($friends, $this->database->table('groups')->where('id', $friendId)->fetch());
        }
        $this->template->friends = $friends;
        $this->template->linkedFriendsIds = $this->database->table('members')->where('groupId', $id)->fetchPairs('memberId', 'groupId');
        $this->template->user = $this->getUser();
    }
    public function renderGroup($name)
    {
        $group = $this->database->table('groups')->where('name', $name)->fetch();
        $sessionGroupId = $this->getSession('GroupId');
        $sessionGroupId->id = $group->id;
        $this->template->group = $group;
        $this->template->user = $this->getUser();
        $this->template->memberCount = $this->database->table('members')->where('groupId', $group->id)->count();
        $this->template->linkedFriendsIds = $this->database->table('members')->where('groupId', $group->id)->fetchPairs('memberId', 'groupId');
        $this->template->stuffs = $this->database->table('stuff')->where('groupId = ?', $group->id)->order('addAt DESC');
        $this->template->users = $this->database->table('users');
        $this->template->likes = $this->database->table('likes')->where('userId', $this->getUser()->id)->fetchPairs('stuffId', 'userId');
    }
    public function renderCreateGroup()
    {
        $this->template->user = $this->database->table('users')->where('id', $this->getUser()->id)->fetch();
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

    public function createComponentUploadBackGroupForm()
    {
        // Vytvoří nový form
        $form = new Form;
        // Tlačítko pro vybrání souboru
        $form->addUpload('image')
        ->setRequired('Prosím vyberte soubor');
        // Odeslání
        $form->addSubmit('send', 'Nahrát');
        // Callback logiky
        $form->onSuccess[] = array($this, 'uploadBackGroupFormSucceeded');

        // Vrátí formulář
        return $form;
    }

    public function uploadBackGroupFormSucceeded(Form $form, $values)
    {
	    $sessionGroupId = $this->getSession('GroupId');
        $oldPic = $this->database->table('groups')
							  ->where('id', $sessionGroupId->id)
							  ->fetch()->picPath;

        // Práce se souborem
        $image = $values->image;
        $image->move('img/'.$values->image->name);
        $this->database->table('groups')->where('id', $sessionGroupId->id)->update(['picPath' => 'img/'.$values->image->name]);
        if ($oldPic != "img/main-pic.png") {
            FileSystem::delete($oldPic);

        }
        $this->redirect('this');
    }


    public function createComponentInfoForm()
    {
        $user = $this->database->table('users')
							  ->where('id', $this->getUser()->id)
							  ->fetch();
        // Vytvoří nový form
        $form = new Form;

        $form->addText('about')
        ->setDefaultValue($user->about);

        $form->addText("name", "Name")
        ->setDefaultValue($user->name);
        $form->addText("lastname", "Surname")
        ->setDefaultValue($user->lastname);
        // Odeslání;
        $form->addSubmit('save', 'Save');
        // Callback logiky
        $form->onSuccess[] = array($this, 'infoFormSucceeded');

        // Vrátí formulář
        return $form;
    }

    public function infoFormSucceeded(Form $form, $values)
    {
        $this->database->table('users')->where('id', $this->getUser()->id)->update([
            'about' => $values->about,
            'name' => $values->name,
            'lastname' => $values->lastname
        ]);

        $this->redirect('Profile:else', $this->getUser()->id);
    }

    public function createComponentCreateGroupForm()
    {
        $user = $this->database->table('users')
							  ->where('id', $this->getUser()->id)
							  ->fetch();
        // Vytvoří nový form
        $form = new Form;

        $form->addText('name', "Name")
        ->setDefaultValue($user->about);

        $form->addText("description", "Description")
        ->setDefaultValue($user->name);

        $form->addSubmit('create', 'Create');
        // Callback logiky
        $form->onSuccess[] = array($this, 'createGroupFormSucceeded');

        // Vrátí formulář
        return $form;
    }

    public function createGroupFormSucceeded(Form $form, $values)
    {
        $this->database->table('groups')->insert([
            'name' => $values->name,
            'description' => $values->description,
        ]);

        $this->database->table('members')->insert([
            'groupId' => $this->database->table('groups')->where('name', $values->name),
            'memberId' => $this->getUser()->id
        ]);


        $this->redirect('Profile:group', $values->name);
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
                'content' => $values->text
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
    public function createComponentStuffGroupForm()
    {
        // Vytvoří nový form
        $form = new Form;
        // Popis dokumenu
		$form->addText('text', '')
			 ->setRequired('');
        // Odeslání
        $form->addSubmit('send', 'Nahrát');
        // Callback logiky
        $form->onSuccess[] = array($this, 'stuffGroupFormSucceeded');

        // Vrátí formulář
        return $form;
    }

    public function stuffGroupFormSucceeded(Form $form, $values)
    {
        $sessionGroupId = $this->getSession('GroupId');
        $videoPath;
        if (Strings::contains($values->text, "www.youtube.com")) {
            $videoPath = Strings::split($values->text, '~=~');

            $this->database->table('stuff')->insert([
                'content' => $values->text,
                'userId' => $this->getUser()->id,
                'addAt' => date('Y-m-d H:i:s'),
                'pic' => "https://img.youtube.com/vi/".$videoPath[1]."/default.jpg",
                'groupId' => $sessionGroupId->id
            ]);
        }
        else if (Strings::contains($values->text, ".jpg")||Strings::contains($values->text, ".jpeg")||Strings::contains($values->text, ".gif")||Strings::contains($values->text, ".tif")||Strings::contains($values->text, ".bmp")||Strings::contains($values->text, ".png"))
        {
            $this->database->table('stuff')->insert([
                'content' => $values->text,
                'userId' => $this->getUser()->id,
                'addAt' => date('Y-m-d H:i:s'),
                'content' => $values->text,
                'groupId' => $sessionGroupId->id
            ]);
        }
        else
        {
            $this->database->table('stuff')->insert([
                'content' => $values->text,
                'userId' => $this->getUser()->id,
                'addAt' => date('Y-m-d H:i:s'),
                'groupId' => $sessionGroupId->id
            ]);
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

    public function actionLike($stuffId, $visitedUserId)
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
        $this->redirect('Profile:else', $visitedUserId);
    }
    public function actionDelete($stuffId, $visitedUserId)
    {
        $this->database->table('stuff')->where('id = ?', $stuffId)->delete();

        $this->redirect('Profile:else', $visitedUserId);
    }

    public function actionGroupLike($stuffId)
    {
        $sessionGroupId = $this->getSession('GroupId');
        $group = $this->database->table('groups')->where('id', $sessionGroupId->id)->fetch();
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
        $this->redirect('Profile:group', $group->name);
    }

    public function actionGroupDelete($stuffId)
    {
        $sessionGroupId = $this->getSession('GroupId');
        $group = $this->database->table('groups')->where('id', $sessionGroupId->id)->fetch();
        $groups = $this->database->table('groups')->fetch();
        $sessionGroupId = $this->getSession('GroupId');
        $this->database->table('stuff')->where('id = ?', $stuffId)->delete();

        $this->redirect('Profile:group', $group->name);
    }

    public function actionShare($text, $id, $visitedUserId, $pic)
    {
            $this->database->table('stuff')->insert([
            'content' => $text,
            'shareId' => $this->getUser()->id,
            'addAt' => date('Y-m-d H:i:s'),
            'userId' => $id,
            'pic' => $pic
        ]);
        $this->redirect('Profile:else', $visitedUserId);
    }

    public function actionGroupShare($text, $id, $pic)
    {
        $sessionGroupId = $this->getSession('GroupId');
        $group = $this->database->table('groups')->where('id', $sessionGroupId->id)->fetch();
        $groups = $this->database->table('groups')->fetch();
            $this->database->table('stuff')->insert([
            'content' => $text,
            'shareId' => $this->getUser()->id,
            'addAt' => date('Y-m-d H:i:s'),
            'userId' => $id,
            'pic' => $pic
        ]);
        $this->redirect('Profile:group', $group->name);
    }

    public function actionJoinGroup($id)
    {
        if(!$c=$this->database->table('members')->where('groupId = ? AND memberId = ?', $id, $this->getUser()->id)->count())
        {
            $this->database->table('members')->insert([
                'memberId' => $this->getUser()->id,
                'groupId' => $id
            ]);
        }
        else {
                $this->database->table('members')->where('groupId = ? AND memberId = ?', $id, $this->getUser()->id)->delete();
        }

        $name = $this->database->table('groups')->where('id', $id)->fetch()->name;
        $this->redirect('Profile:group', $name);

    }

}
