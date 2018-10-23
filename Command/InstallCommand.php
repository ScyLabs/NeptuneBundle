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

class InstallCommand extends Command
{


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
            $question->setValidator(function($db_password){
                if(empty($db_password)){
                    throw new \Exception('Database password can not be empty');
                }
                return $db_password;
            });
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

        foreach ($questions as $name => $question) {
            $answer = $this->getHelper('question')->ask($input, $output, $question);
            $input->setArgument($name, $answer);
        }
    }

}