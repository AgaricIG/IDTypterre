<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use App\Service\Mailer;

class MailerTestCommand extends Command
{
    private $output;
    private $mailer;
    protected static $defaultName = 'mailer:test';

    public function __construct(Mailer $mailer) {
        $this->mailer = $mailer;
        parent::__construct();
    }

    protected function configure()
    {
        $this
        ->setDescription('Send an email to a test address')
        ->addArgument('address', InputArgument::OPTIONAL, 'host name ?','user@email.com')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $address = $input->getArgument('address');

        $output->writeln('<info>Sending email...');

        try {

            $this->mailer->sendTestEmail($address);
            $output->writeln('<comment>The email has just been sent !');

        } catch(\Exception $exception) {

            $output->writeln('<error>Oups, an error occured when sending the email...');
            dd($exception);
        }

        $output->writeln('<info>Terminate.');

        return Command::SUCCESS;
    }
}
