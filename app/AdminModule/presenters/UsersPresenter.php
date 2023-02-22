<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 22. 9. 2020
 * Time: 17:22
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\IUserFormFactory;
use App\Model\Enum\FlashMessages;
use App\Model\User;
use Nette\ComponentModel\IComponent;
use Nextras\Orm\Entity\IEntity;

class UsersPresenter extends BaseAppPresenter
{
    public ?User $editUser;

    /** @inject  */
    public IUserFormFactory $userFormFactory;

    public function beforeRender()
    {
        parent::beforeRender();
        $this->getTemplate()->roles = $this->orm->roles->findAll();
    }

    public function renderDefault(int $roleId = null): void
    {
        if ($roleId) {
            $role = $this->orm->roles->getById($roleId);
            $this->getTemplate()->role = $role;
            $this->getTemplate()->users = $this->orm->users->findBy(['role' => $role])->orderBy('active', 'DESC')->orderBy('name');
        } else {
            $this->getTemplate()->users = $this->orm->users->findAll()->orderBy('active', 'DESC')->orderBy('name');
        }
    }

    public function actionDetail(int $id): void
    {
        $this->redirect("default"); //not ready yet. Use overview
        /*
        /** @var User|null $user */
        /*
        $user = $this->orm->users->getById($id);
        if ($user == null) {
            $this->flashMessage($this->translator->translate("common.userNotFound"));
            $this->redirect("default");
        }
        $this->editUser = $user;*/
    }

    public function renderDetail(): void
    {
        $this->getTemplate()->item = $this->editUser;
    }


    public function actionEdit(int $id = null): void
    {
        if ($id === null) {
            $this->editUser = null;
        } else {
            /** @var User|null $user */
            $user = $this->orm->users->getById($id);
            if ($user == null) {
                $this->flashMessage($this->translator->translate("common.userNotFound"));
                $this->redirect("default");
            }
            $this->editUser = $user;
        }
    }

    public function renderEdit(): void
    {
        $this->getTemplate()->item = $this->editUser;
    }

    public function handleToggleActive(int $userId): void
    {
        /** @var User $user */
        $user = $this->orm->users->getById($userId);
        if ($user !== null) {
            $user->active = !$user->active;
            $this->orm->persistAndFlush($user);
            $this->flashMessage($this->translator->translate("common.userDeactivated"), FlashMessages::SUCCESS);
            $this->redirect('this');
        }
    }

    public function createComponentUserForm(string $name): ?IComponent
    {
        return $this->userFormFactory->create($this->editUser);
    }

}