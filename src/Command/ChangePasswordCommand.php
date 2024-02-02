<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordCommand extends Command
{
    private $output;
    private $manager;

    public function __construct(EntityManagerInterface $manager, UserPasswordHasherInterface $hasher)
    {
        $this->manager = $manager;
        $this->hasher = $hasher;
        parent::__construct();
    }
    protected function configure()
    {
        $this
        ->setName('change:password')
        ->setDescription('Change password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Let's change a password !");

        $helper = $this->getHelper('question');

        $username = $helper->ask($input, $output, new Question('Username ? ', ''));

        $user = $this->manager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            $output->writeln('<error>Cant find this username...');
            return Command::FAILURE;
        }

        $question = new Question('Password ? ');
        $password = $helper->ask($input, $output, $question);

        if (empty($password)) {
            $output->writeln('<error>Please provide a valid password...');
            return Command::FAILURE;
        }

        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->manager->persist($user);
        $this->manager->flush();
        $output->writeln('Success ! ');
        return Command::SUCCESS;
    }
}
