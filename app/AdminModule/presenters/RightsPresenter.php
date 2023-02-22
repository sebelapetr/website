<?php
declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Forms\IEditRightsFormFactory;
use App\Model\Role;
use Nette\ComponentModel\IComponent;

class RightsPresenter extends BaseAppPresenter {

    public Role $editRole;

    /** @inject */
    public IEditRightsFormFactory $editRightsFormFactory;

    public function beforeRender()
    {
        parent::beforeRender();
        $this->getTemplate()->roles = $this->orm->roles->findAll();
    }

    public function renderDefault(int $roleId = null): void
    {
        if ($roleId) {
            /** @var Role|null $role */
            $role = $this->orm->roles->getById($roleId);
            if ($role !== null) {
                $this->editRole = $role;
                $this->getTemplate()->role = $role;
            }
        } else {
            $this->getTemplate()->users = $this->orm->users->findAll()->orderBy('active', 'DESC')->orderBy('name');
        }
    }


    public function createComponentEditRightsForm(string $name): ?IComponent
    {
        return $this->editRightsFormFactory->create($this->editRole);
    }

}