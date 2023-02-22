<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:31
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\AdminModule\Components\IMenuComponentFactory;
use App\AdminModule\Components\MenuComponent;
use App\Model\Enum\FlashMessages;
use App\Model\Exceptions\PermissonsException;
use App\Model\Orm;
use Contributte\Translation\LocalesResolvers\Session;
use Contributte\Translation\Translator;

abstract class BaseAppPresenter extends BasePermissonPresenter
{

    /** @inject */
    public Translator $translator;

    /** @inject */
    public Session $translatorSessionResolver;

    /** @inject */
    public IMenuComponentFactory $menuComponentFactory;

    /** @inject */
    public Orm $orm;

    public function startup()
    {
        parent::startup();

        if (!$this->isLinkCurrent('Dashboard:default') && !$this->isLinkCurrent('Authentication:*')) {
            try {
                $isAllowed = $this->user->isAllowed(strtolower($this->getPureName()), 'read');
                if (!$isAllowed) {
                    $this->flashMessage($this->translator->translate("common.notAllowed"), FlashMessages::DANGER);
                    $this->redirect('Dashboard:');
                }
            } catch (PermissonsException $exception)
            {
                $this->flashMessage($exception->getMessage(), FlashMessages::DANGER);
                $this->redirect('Dashboard:default');
            }
        }

    }

    public function beforeRender()
    {
        parent::beforeRender();
        $prefixedTranslator = $this->translator->createPrefixedTranslator('app');
        $this->getTemplate()->title = $prefixedTranslator->translate(lcfirst($this->getPureName()).'.title');
    }

    public function createComponentMenuComponent(string $name): MenuComponent
    {
        return $this->menuComponentFactory->create();
    }


    public function handleLogout(): void
    {
        $this->getUser()->logout();
        $this->flashMessage($this->translator->translate("common.logoutSuccess"));
        $this->redirect(AuthenticationPresenter::BASE_UNLOGGED_LINK);
    }
}
