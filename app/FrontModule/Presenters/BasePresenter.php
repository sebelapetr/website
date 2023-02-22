<?php

declare(strict_types=1);

namespace App\FrontModule\Presenters;

use App\Model\Orm;
use Nette;
use Nette\Caching\Cache;
use Nextras\Dbal\Connection;


abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @inject */
    public Orm $orm;

    public Connection $connection;

    //public Cache $cache;

    public function __construct(Orm $orm, connection $connection)
    {
        parent::__construct();
        $this->orm = $orm;
        $this->connection = $connection;
    }

    public function startup()
    {
        parent::startup();
    }

    public function beforeRender()
    {
        parent::beforeRender();
    }
}
