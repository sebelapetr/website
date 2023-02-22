<?php

namespace App\AdminModule\Components\Datagrids;

use App\Model\Orm;
use Nette;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridFilterNotFoundException;

class BaseDatagrid extends DataGrid
{
	protected Orm $orm;
    public string $langDomain;

	public function __construct(Orm $orm, Nette\ComponentModel\IContainer $parent = null, ?string $name = null)
	{
		parent::__construct($parent, $name);
		$this->orm = $orm;
        $reflect = new \ReflectionClass($this);
        $this->langDomain = "datagrids.".$reflect->getShortName();
	}

	public function render(): void
	{
		$start = $this->createTemplate();
		$start->setFile(__DIR__."/datagridPrepend.latte");
		$end = $this->createTemplate();
		$end->setFile(__DIR__."/datagridAppend.latte");

		$start->render();
		try {
			parent::render();
		} catch(DataGridFilterNotFoundException $e) {
			foreach($this->presenter->getSession()->getIterator() as $sectionTitle){
                $sectionTitle = strval($sectionTitle);
				if(stripos($sectionTitle, 'datagrid') !== FALSE){
					$this->presenter->getSession($sectionTitle)->remove();
				}
			}
			parent::render();
		}
		$end->render();
	}

}