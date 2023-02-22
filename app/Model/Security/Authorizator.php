<?php
declare(strict_types=1);

namespace App\Model;

use App\Model\Exceptions\PermissonsException;
use Contributte\Translation\Translator;

class Authorizator implements \Nette\Security\Authorizator
{
    public Orm $orm;
    public Translator $translator;

    function __construct(Orm  $orm, Translator $translator)
    {
        $this->orm = $orm;
        $this->translator = $translator;
    }

    function isAllowed($role, $resource, $privilege): bool
    {
        /** @var Role|null $roleEnt */
        $roleEnt = $this->orm->roles->getBy(['intName' => $role]);

        if (!$roleEnt)
        {
            throw new PermissonsException($this->translator->translate("common.roleNotFound"));
        }

        /** @var Right|null $right */
        $right = $this->orm->rights->getBy(['name' => $resource]);

        if (!$right)
        {
            throw new PermissonsException($this->translator->translate("common.permissionNotFound", ["name" => $resource]));
        }
        /** @var Action|null $action */
        $action = $this->orm->actions->getBy(['name' => $privilege, 'right' => $right]);
        if (!$action)
        {
            throw new PermissonsException($this->translator->translate("common.actionNotFound", ["name" => $resource, "action" => $privilege]));
        }

        if ($roleEnt->actions->toCollection()->getBy(['id' => $action->id]))
        {
            return true;
        } else {
            return false;
        }
    }
}