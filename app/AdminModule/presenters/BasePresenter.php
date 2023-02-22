<?php
/**
 * Created by PhpStorm.
 * User: Petr Šebela
 * Date: 21. 9. 2020
 * Time: 23:26
 */

declare(strict_types=1);

namespace App\AdminModule\Presenters;

use App\Model\Orm;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{

    /** @inject */
    public Orm $orm;

    public function __construct(Orm $orm)
    {
        parent::__construct();
        $this->orm = $orm;
    }

    public function startup()
    {
        parent::startup();
    }

    public function beforeRender()
    {
        parent::beforeRender();
    }

    protected function getPureName(): string
    {
        $pos = strrpos($this->name, ':');
        if (is_int($pos)) {
            return substr($this->name, $pos + 1);
        }
        return $this->name;
    }

    public function isLinkCurrentIn(string $links): bool
    {
        foreach(explode('|', $links) as $item) {
            if($this->isLinkCurrent($item)){
                return TRUE;
            }
        }
        return FALSE;
    }
}
