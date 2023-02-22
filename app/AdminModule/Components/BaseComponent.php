<?php
declare(strict_types=1);

namespace App\AdminModule\Components;

use App\Model\Orm;
use Contributte\Translation\Translator;
use Nette\Application\UI\Control;
use Nette\Utils\Strings;

/**
 * @property-read Translator $translator
 */
abstract class BaseComponent extends Control
{

	protected Orm $orm;
	private Translator $translator;
    public string $langDomain;

	public function __construct(Orm $orm, Translator $translator)
	{
		$this->translator = $translator;
		$this->orm = $orm;
        $reflect = new \ReflectionClass($this);
        $this->langDomain = "forms.".$reflect->getShortName();
	}

	public function getTranslator(): Translator
	{
		return $this->translator;
	}

	protected function setTemplateFile(?string $file = NULL): void
	{
		$reflection = $this->getReflection();
        $fileName = $reflection->getFileName();
        if ($fileName == false) {
            throw new \Exception("File " . $reflection->getFileName() . " not found.");
        }
		$dir = pathinfo($fileName, PATHINFO_DIRNAME);
		
		$ext = '.latte';
		if ($file === NULL) {
			$file = $reflection->getShortName() . $ext;
		} elseif (!Strings::endsWith($file, $ext)) {
			$file .= $ext;
		}
		
		if(is_file($dir . DIRECTORY_SEPARATOR . $file)){
			$this->getTemplate()->setFile($dir . DIRECTORY_SEPARATOR . $file);
		}
	}


}