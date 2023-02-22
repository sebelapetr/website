<?php
declare(strict_types=1);

namespace App\AdminModule\Forms;

use App\AdminModule\Components\BaseComponent;
use App\Model\Enum\FlashMessages;
use App\Model\Orm;
use App\Model\Role;
use Contributte\Translation\Translator;
use Nette;
use Tracy\Debugger;

class EditRightsForm extends BaseComponent
{
    private Role $role;

    public function __construct(Role $role, Orm $orm, Translator $translator)
    {
        $this->role = $role;
        parent::__construct($orm, $translator);
    }

    public function render(): void
    {
        $this->getTemplate()->setFile(__DIR__ . '/EditRightsForm.latte');
        $this->getTemplate()->role = $this->role;
        $this->getTemplate()->rights = $this->orm->rights->findAll();
        $this->getTemplate()->render();
    }

    protected function createComponentForm(): Nette\Application\UI\Form
    {
        $form = new Nette\Application\UI\Form();

        $form->addCheckboxList('allowedActions', null, $this->orm->actions->findAll()->fetchPairs('id', 'name'));

        $form->addSubmit('send', $this->translator->translate($this->langDomain.'.edit'))
            ->setHtmlAttribute('class', 'btn btn-success btn-sm');

        $form->onSuccess[] = [$this, 'onSuccess']; /** @phpstan-ignore-line */

        $allowedActions = $this->role->actions->toCollection()->fetchPairs(null, 'id');

        $form->setDefaults(['allowedActions' => $allowedActions]);

        return $form;
    }

    public function onSuccess(Nette\Application\UI\Form $form, Nette\Utils\ArrayHash $values): void
    {
        foreach ($this->role->actions as $action) {
            $this->role->actions->remove($action);
        }

        foreach ($values->allowedActions as $actionId)
        {
            $actionEnt = $this->orm->actions->getById($actionId);
            if ($actionEnt !== null) {
                $this->role->actions->add($actionEnt);
            }
        }

        $this->orm->persistAndFlush($this->role);

        $this->getPresenter()->flashMessage($this->translator->translate($this->langDomain.'.success'), FlashMessages::SUCCESS);

        $this->getPresenter()->redirect('edit', ['id' => $this->role->id]);
    }
}