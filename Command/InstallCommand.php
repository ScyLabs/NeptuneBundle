<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17/10/2018
 * Time: 14:18
 */

namespace ScyLabs\NeptuneBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstallCommand extends Command
{

    private $container;
    public function __construct(ContainerInterface $container){
        parent::__construct();
        $this->container = $container;
    }

    protected function configure(){
        $this->setName('scylabs:neptune:install')
            ->setDescription('NeptuneBundle nstallation procedure')
            ->setHelp('This command help you to install NeptuneBundle')
            ->setDefinition(array(
                new InputArgument('db_user',InputArgument::REQUIRED,'Database user'),
                new InputArgument('db_password',InputArgument::REQUIRED,'Database Password'),
                new InputArgument('db_name',InputArgument::REQUIRED,'Database Name'),
                new InputArgument('db_host',InputArgument::REQUIRED,'Database Host'),
                new InputArgument('mailer_type',InputArgument::REQUIRED,'Mailer type'),
                new InputArgument('mailer_username',InputArgument::REQUIRED,'Mailer Username'),
                new InputArgument('mailer_password',InputArgument::REQUIRED,'Mailer password'),
                new InputArgument('app_env',InputArgument::REQUIRED,'Environement')


            ))
        ;
    }
    protected function execute(InputInterface $input,OutputInterface $output){
        $output->writeln([
            "<info>Welcome to the NeptuneBundle Installer</info>",
            '<info>============</info>',
            '',
            '<info>You will be guided, and different things will be asked of you. Answer the questions simply.</info>',
        ]);

        $db_user = $input->getArgument('db_user');
        $db_password = $input->getArgument('db_password');
        $db_name = $input->getArgument('db_name');
        $db_host = $input->getArgument('db_host');
        $mailer_type = $input->getArgument('mailer_type');
        $mailer_username = $input->getArgument('mailer_username');
        $mailer_password = $input->getArgument('mailer_password');
        $app_env = $input->getArgument('app_env');

        $rootDir = $this->container->getParameter('kernel.project_dir');
        $envDist = $rootDir.'/.env.dist';
        $envFile = $rootDir.'/.env';
        $tmpEnv = $rootDir.'/.env.tmp';
        if(!file_exists($envDist)){
            $output->writeln(
                '<error>File .env.dist not found in the project directory</error>'
            );
            return;
        };
        //sleep(1);
        $output->writeln('<info>Yours choices is very...</info>');
        //sleep(2);
        $output->writeln('<info>Interesting... </info>');
        //sleep(3);
        $output->writeln('<info>Well...Ok , Thanks for yours informations , i configure your .env.dist ;o</info>');

        $output->writeln('<info>3</info>');
        //sleep(1);
        $output->writeln('<info>2</info>');
        //sleep(1);
        $output->writeln('<info>1</info>');
        //sleep(1);
        $output->writeln('<info>Ready ... Go</info>');

        // Copy to .env.dist file -> .env
        copy($envDist,$tmpEnv);
        $env = file_get_contents($tmpEnv);

        $secret = md5(rand(0,rand(0,time())).time());
        $env = str_replace('%app_secret%',$secret,$env);

        $output->writeln('<info>Secret OK...</info>');

        //sleep(1);
        $database_url = 'mysql://'.$db_user.':'.$db_password.'@'.$db_host.'/'.$db_name;
        $env = str_replace('%database_url%',$database_url,$env);
        $output->writeln('<info>BDD OK ...</info>');
        //sleep(1);

        switch ($mailer_type){
            case 'gmail':
                $mailer_url = 'gmail://'.$mailer_username.':'.$mailer_password.'@'.$mailer_password;
                break;
            default:
                $mailer_url = 'gmail://'.$mailer_username.':'.$mailer_password.'@'.'localhost';
                break;
        }
        $env = str_replace('%mailer_url%',$mailer_url,$env);
        $output->writeln('<info>Mailer ok ... </info>');

        //sleep(1);
        
        $env = str_replace('%app_env%',$app_env,$env);
        $output->writeln('<info>Environement OK...</info>');
        
        //sleep(3);
        $f = fopen($tmpEnv,'w+');
        fwrite($f,$env);
        fclose($f);
        copy($tmpEnv,$envFile);
        unlink($tmpEnv);
        $output->writeln("The configuration i'ts okay . and the file i'ts ready to work ... But ...");
        
        //sleep(3);
        
        $output->writeln("What ?? What's appening ? Why you change my color ? ");
        
        //sleep('2');
        
        $output->writeln("Are you serious men ?");
        
        //sleep(3);
        $output->writeln("You thing , because i'm a Robot , you can change my color without punishment ?");
        $star = '*';
        for($i = 0 ;$i < 5;$i++){
            //sleep(1);
            $output->writeln($star);

            $star .= '*';
        }

        $output->writeln("Okay ... Okay ... I stop it ...");
        //sleep(1);
        $output->writeln('<info>Ready to update dataBase =)</info>');
        //sleep(1);
        $output->writeln('<info>Ho , my color .. Thanks</info>');

        $command = $this->getApplication()->find('d:s:u');
        $argv = array(
            'command'       => 'd:s:u',
            'name'          =>  'Schema update',
            '--dump-sql'    =>  true
        );
    }

    protected function interact(InputInterface $input,OutputInterface $output){
        $questions = array();
        if(!$input->getArgument('db_user')){
            $question = new Question('Please give me database username : ');
            $question->setValidator(function($db_user){
               if(empty($db_user)){
                   throw new \Exception('Database user can not be empty');
               }
               return $db_user;
            });
            $questions['db_user'] = $question;
        }
        if(!$input->getArgument('db_password')){
            $question = new Question('Please give me database password : ');
            $questions['db_password'] = $question;
        }
        if(!$input->getArgument('db_name')){
            $question = new Question('Please give me database name  : ');
            $question->setValidator(function($db_name){
                if(empty($db_name)){
                    throw new \Exception('Database name can not be empty');
                }
                return $db_name;
            });
            $questions['db_name'] = $question;
        }
        if(!$input->getArgument('db_host')){
            $question = new Question('Please give me database host   [localhost:3306] : ');
            $question->setValidator(function($db_host){
                if(empty($db_host)){
                    $db_host = 'localhost:3306';
                }
                return $db_host;
            });
            $questions['db_host'] = $question;
        }
        if(!$input->getArgument('mailer_type')){
            $question = new Question('Please give me the mailer type [gmail] : ');
            $question->setValidator(function($mailer_type){
                if(empty($mailer_type)){
                    $mailer_type = 'gmail';
                }
                return $mailer_type;
            });
            $questions['mailer_type'] = $question;
        }
        if(!$input->getArgument('mailer_username')){
            $question = new Question('Please give me the mailer username : ');
            $question->setValidator(function($mailer_username){
                if(empty($mailer_username)){
                    throw new \Exception('Mailer username can not be empty');
                }
                return $mailer_username;
            });
            $questions['mailer_username'] = $question;
        }
        if(!$input->getArgument('mailer_password')){
            $question = new Question('Please give me the mailer password : ');
            $question->setValidator(function($mailer_password){
                if(empty($mailer_password)){
                    throw new \Exception('Mail username can not be empty');
                }
                return $mailer_password;
            });
            $questions['mailer_password'] = $question;
        }
        if(!$input->getArgument('app_env')){
            $question = new Question('What is the Environement ? prod or dev ? [dev] :');
            $question->setValidator(function($app_env){
                if(empty($app_env)){
                    $app_env = 'dev';
                }
                return $app_env;
            });
            $questions['app_env'] = $question;
        }

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

}