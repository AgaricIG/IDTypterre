<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PrintUserCommand extends Command
{
    private $output;
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        parent::__construct();
    }
    protected function configure()
    {
        $this
        ->setName('print:user')
        ->setDescription('print user details')
        ->addOption('username', 'u', InputOption::VALUE_REQUIRED, 'username ?', null)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if($input->getOption('username') !== null) {
            $output->writeln('<comment>Search for user: '.$input->getOption('username'));
            $users = $this->manager->getRepository(User::class)->findBy(['username' => $input->getOption('username')]);
        } else {
            $users = $this->manager->getRepository(User::class)->findAll();
        }

        if(empty($users)) {
            $output->writeln('<info>Aucun utilisateur trouvé...');
        } else {
            $output->writeln('<info>'.count($users).' utilisateur trouvé :');
            dump($users);
        }

        return Command::SUCCESS;
    }
}
