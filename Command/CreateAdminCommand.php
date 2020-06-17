<?php

namespace ScyLabs\NeptuneBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use ScyLabs\NeptuneBundle\Entity\Admin;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use ScyLabs\NeptuneBundle\Repository\AdminRepository;
use Symfony\Bundle\FrameworkBundle\Command\SecretsSetCommand;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CreateAdminCommand extends Command
{
    protected static $defaultName = 'scylabs:neptune:create-admin';

    private $adminRepository;
    private $passwordEncoder;
    private $entityManager;

    public function __construct(AdminRepository $adminRepository,UserPasswordEncoderInterface $passwordEncoder,EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->adminRepository = $adminRepository;
        $this->passwordEncoder = $passwordEncoder;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add back-office user with E-mail')
            ->addArgument('email', InputArgument::OPTIONAL, 'Admin e-mail')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $io->writeln([
            '',
            '=================',
            '',
        ]);

        // If the first argv is send but it is not valid e-mail
        if(null !== $email && !filter_var(strtolower($email),FILTER_VALIDATE_EMAIL)){
            $io->error('Command argument is not a valid E-mail.');
            $email = null;
        }
        else if (null !== $email && null !== $this->adminRepository->findOneByEmail(strtolower($email))){
            // If is valid e-mail but aerly exist
            $io->error(sprintf('Admin aerly exists with %s E-mail. Please put another E-mail'));
            $email = null;
        }
        
        // If don't send argv or enter in prev conditions
        if (!$email) {
            // Request an e-mail
            $question = new Question('Please put a valid e-mail to your new admin');
            $question->setValidator(function($email){
                $email = strtolower($email);
                if(empty($email))
                    throw new Exception("E-mail can't be empty");
                else if (!filter_var($email,FILTER_VALIDATE_EMAIL))
                    throw new Exception('Invalid E-mail');
                else if(null !== $this->adminRepository->findOneByEmail($email))
                    throw new Exception(sprintf('Admin aerly exists with %s E-mail. Please put another E-mail',$email));
                return $email;

            });
            $email = $io->askQuestion($question);
            
        }

        $admin = new Admin();

        
        $admin->setEmail($email);
        // Request an firstName
        $firstNameQuestion = new Question('Please put a firstname to your new admin');
        $firstNameQuestion->setValidator(function($name){
            if(empty($name))
                throw new Exception("Firstname can't be empty");
           
            return $name;

        });
        $admin->setFirstname($io->askQuestion($firstNameQuestion));

        // Request an lastname
        $lastNameQuestion = new Question('Please put a lastname to your new admin');
        $lastNameQuestion->setValidator(function($name){
            if(empty($name))
                throw new Exception("Lastname can't be empty");
           
            return $name;

        });
        $admin->setName($io->askQuestion($lastNameQuestion));
        
        
        $roleQuestion = new ChoiceQuestion('How role you want to your new admin',[
            'ROLE_ADMIN',
            'ROLE_SUPER_ADMIN'
        ]);
        $role = $io->askQuestion($roleQuestion);

        $admin->addRole($role);
      

        while(true){
            $pass = $io->askHidden("Please put a password to your new admin");
            if(!preg_match("/^.*(?=.{8,})((?=.*[!@#$%^&*()\-_=+{};:,<.>]){1})(?=.*\d)((?=.*[a-z]){1})((?=.*[A-Z]){1}).*$/",$pass)){
                $io->error('The password must be at least 1 uppercase, 1 lowercase, 1 digit, 1 special character and be at least 8 characters long.');
                continue;
            }
            $confirm = $io->askHidden("Confirm password");
            
            if($pass === $confirm){
                break;
            }
            $io->error('Passwords are not the same');
        }
        
        $admin->setPassword($this->passwordEncoder->encodePassword($admin,$pass));
        
        $this->entityManager->persist($admin);
        $this->entityManager->flush();
        $io->success('Congratulations ! Your admin account has been created.');

        return 0;
    }
}
