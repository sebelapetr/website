<?php declare(strict_types = 1);

namespace App\Console;

use App\Model\Services\Emails\EmailSenderService;
use Contributte\Deployer\Config\Config;
use Contributte\Deployer\Config\Section;
use Contributte\Deployer\Manager;
use Nette\DI\Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class EmailsSenderProcessor extends Command
{
    public EmailSenderService $emailSenderService;

    public function __construct(Container $container, string $name = null)
    {
        /** @var EmailSenderService $emailSenderService */
        $emailSenderService = $container->getByName('emailSenderService');
        $this->emailSenderService = $emailSenderService;
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('send-emails');
        $this->setDescription('Send emails in queue.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        var_dump("Start sending emails");
        $this->emailSenderService->sendEmailsInQueue();
        var_dump("Done");
        return 0;
    }
}
