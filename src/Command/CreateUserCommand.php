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

class CreateUserCommand extends Command
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
        ->setName('create:user')
        ->setDescription('Create user command')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("<info>Let's create a new user !");

        $helper = $this->getHelper('question');
        $user = new User();

        $username = $helper->ask($input, $output, new Question('Username ?', 'noname'));
        $user->setUsername($username);

        $exist = $this->manager->getRepository(User::class)->findOneByUsername($username);
        if($exist) {
            $output->writeln('<error>This username is already in use');
            return Command::FAILURE;
        }

        $email = $helper->ask($input, $output, new Question('Email ?', 'noname@mail.com'));
        $user->setEmail($email);

        $exist = $this->manager->getRepository(User::class)->findOneByEmail($email);
        if($exist) {
              $output->writeln('<error>This email is already in use');
              return Command::FAILURE;
          }

        $role = $helper->ask($input, $output, new Question('Role ?', 'ROLE_USER'));
        $user->setRoles([$role]);

        $question = new Question('Password ?');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $password = $helper->ask($input, $output, $question);
        $user->setPassword($this->hasher->hashPassword($user, $password));

        $this->manager->persist($user);
        $this->manager->flush();
        $output->writeln('Successful, you have created the user : '. $user->getUsername());

        return Command::SUCCESS;

    }
}
