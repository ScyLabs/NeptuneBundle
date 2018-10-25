<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17/10/2018
 * Time: 14:18
 */

namespace ScyLabs\NeptuneBundle\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ContainerInterface;


class InstallCommand extends Command
{

    private $container;
    private $verbose;
    private $input;
    private $output;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
        $this->verbose = true;
    }

    protected function configure()
    {
        $this->setName('scylabs:neptune:install')
            ->setDescription('NeptuneBundle nstallation procedure')
            ->setHelp('This command help you to install NeptuneBundle')
            ->setDefinition(array(
                new InputArgument('db_user', InputArgument::REQUIRED, 'Database user'),
                new InputArgument('db_password', InputArgument::REQUIRED, 'Database Password'),
                new InputArgument('db_name', InputArgument::REQUIRED, 'Database Name'),
                new InputArgument('db_host', InputArgument::REQUIRED, 'Database Host'),
                new InputArgument('mailer_type', InputArgument::REQUIRED, 'Mailer type'),
                new InputArgument('mailer_username', InputArgument::REQUIRED, 'Mailer Username'),
                new InputArgument('mailer_password', InputArgument::REQUIRED, 'Mailer password'),
                new InputArgument('app_env', InputArgument::REQUIRED, 'Environement'),

            ))
            ->addOption('create-database','c')
            ->addOption('shutup','s')
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $rootDir = $this->container->getParameter('kernel.project_dir');
        $this->input = $input;
        $this->output = $output;

        if(file_exists($rootDir.'/var/installed')){
            $this->say('<error>The installation has already been completed</error>');
            die();
        }

        $db_user = $input->getArgument('db_user');
        $db_password = $input->getArgument('db_password');
        $db_name = $input->getArgument('db_name');
        $db_host = $input->getArgument('db_host');
        $mailer_type = $input->getArgument('mailer_type');
        $mailer_username = $input->getArgument('mailer_username');
        $mailer_password = $input->getArgument('mailer_password');
        $app_env = $input->getArgument('app_env');

        $create_database = $input->getOption('create-database');
        $this->verbose = !$input->getOption('shutup');


        $envDist = $rootDir . '/.env.dist';
        $envFile = $rootDir . '/.env';
        $tmpEnv = $rootDir . '/.env.tmp';

        $this->say([
            "<info>Welcome to the NeptuneBundle Installer</info>",
            '<info>============</info>',
            '',
            '<info>You will be guided, and different things will be asked of you. Answer the questions simply.</info>',
        ]);


        if (!file_exists($envDist)) {
            $this->say(
                '<error>File .env.dist not found in the project directory</error>'
            );
            return;
        };

        $this->sleep(1);
        $this->say('<info>Yours choices is very...</info>');
        $this->sleep(2);
        $this->say('<info>Interesting... </info>');
        $this->sleep(3);
        $this->say('<info>Well...Ok , Thanks for yours informations , i configure your .env.dist ;o</info>');

        $this->say('<info>3</info>');
        $this->sleep(1);
        $this->say('<info>2</info>');
        $this->sleep(1);
        $this->say('<info>1</info>');
        $this->sleep(1);
        $this->say('<info>Ready ... Go</info>');

        // Copy to .env.dist file -> .env
        copy($envDist, $tmpEnv);
        $env = file_get_contents($tmpEnv);

        $secret = md5(rand(0, rand(0, time())) . time());
        $env = str_replace('%app_secret%', $secret, $env);

        $this->say('<info>Secret OK...</info>');

        $this->sleep(1);
        $database_url = 'mysql://' . $db_user . ':' . $db_password . '@' . $db_host . '/' . $db_name;
        $env = str_replace('%database_url%', $database_url, $env);
        $this->say('<info>BDD OK ...</info>');
        $this->sleep(1);

        switch ($mailer_type) {
            case 'gmail':
                $mailer_url = 'gmail://' . $mailer_username . ':' . $mailer_password . '@' . 'localhost';
                break;
            default:
                $mailer_url = 'gmail://' . $mailer_username . ':' . $mailer_password . '@' . 'localhost';
                break;
        }
        $env = str_replace('%mailer_url%', $mailer_url, $env);
        $this->say('<info>Mailer ok ... </info>');

        $this->sleep(1);

        $env = str_replace('%app_env%', $app_env, $env);
        $this->say('<info>Environement OK...</info>');

        $this->sleep(3);
        $f = fopen($tmpEnv, 'w+');
        fwrite($f, $env);
        fclose($f);
        copy($tmpEnv, $envFile);
        unlink($tmpEnv);
        $this->say("The configuration i'ts okay . and the file i'ts ready to work ... But ...");

        $this->sleep(3);

        $this->say("What ?? What's appening ? Why you change my color ? ");

        $this->sleep('2');

        $this->say("Are you serious men ?");

        $this->sleep(3);
        $this->say("You thing , because i'm a Robot , you can change my color without punishment ?");
        $star = '*';
        for ($i = 0; $i < 5; $i++) {
            $this->sleep(1);
            $this->say($star);

            $star .= '*';
        }

        $this->say("Okay ... Okay ... I stop it ...");
        $this->sleep(1);
        $this->say('<info>Ready to update dataBase =)</info>');
        $this->sleep(1);
        $this->say('<info>Ho , my color .. Thanks</info>');


        $argv = array();
        $commandInput = new ArrayInput($argv);
        if ($create_database === true) {
            $command = $this->getApplication()->find('doctrine:database:create');
            $command->run($commandInput, $output);
            $this->say('<info>Database created ;-)</info>');
        }
        $command = $this->getApplication()->find('doctrine:schema:update');

        $this->sleep(2);
        $commandInput = new ArrayInput(array('--dump-sql'=>true));
        $command->run($commandInput,$output);
        $this->say('Hey boy . Look at this , is very biutiful SQL. But ? Are you sure you want to run this ? ');

        if($this->sayYesQuestion('I can execute this SQL request ? [yes] :',$input,$output) === true){
            if($this->sayYesQuestion('Are you sure ? [ yes] : ',$input,$output) === true){
                $commandInput = new ArrayInput(array('--force'=>true));
                $command->run($commandInput,$output);

            }
        }
        $this->say('<info>Good boy ...</info>');
        $this->sleep(1);
        $this->say("<info>The database i'ts Okay ..</info>");
        $this->sleep(1);
        $this->say('<info>I send the defaults values to the database..</info>');
        $this->sleep(1);

        $commandInput = new ArrayInput(array());
        $command = $this->getApplication()->find('doctrine:fixtures:load');
        $command->run($commandInput,$output);

        $this->sleep(2);
        $this->say('<info>Installalation is finish</info>');
        $this->sleep(1);
        $this->say('<info>Congratulations, you managed to launch an order that did everything in your place</info>');
        $this->sleep(2);
        $this->say('<info>Please, go and never come back</info>');
        $this->sleep(2);
        $this->say('<info>Please remove .git and init a new Repository</info>');

        touch($rootDir.'/var/installed');

    }


    private function say($message){
        if($this->verbose === true){
            $this->output->writeln($message);
        }
    }
    private function sleep ($time = 1){
        if($this->verbose === true){
            sleep($time);
        }
    }
    private function sayYesQuestion($question,$input,$output){
        $question = new Question($question);
        $question->setValidator(function ($execute_sql){
            if(empty($execute_sql)){
                $execute_sql = 'yes';
            }
            elseif($execute_sql != 'yes'){
                throw new \Exception('Please say yes');
            }
            return $execute_sql;
        });
        $this->getHelper('question')->ask($input, $output, $question);

        return true;
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $questions = array();
        if (!$input->getArgument('db_user')) {
            $question = new Question('Please give me database username : ');
            $question->setValidator(function ($db_user) {
                if (empty($db_user)) {
                    throw new \Exception('Database user can not be empty');
                }
                return $db_user;
            });
            $questions['db_user'] = $question;
        }
        if (!$input->getArgument('db_password')) {
            $question = new Question('Please give me database password : ');
            $questions['db_password'] = $question;
        }
        if (!$input->getArgument('db_name')) {
            $question = new Question('Please give me database name  : ');
            $question->setValidator(function ($db_name) {
                if (empty($db_name)) {
                    throw new \Exception('Database name can not be empty');
                }
                return $db_name;
            });
            $questions['db_name'] = $question;
        }
        if (!$input->getArgument('db_host')) {
            $question = new Question('Please give me database host   [localhost:3306] : ');
            $question->setValidator(function ($db_host) {
                if (empty($db_host)) {
                    $db_host = '127.0.0.1:3306';
                }
                return $db_host;
            });
            $questions['db_host'] = $question;
        }
        if (!$input->getArgument('mailer_type')) {
            $question = new Question('Please give me the mailer type [gmail] : ');
            $question->setValidator(function ($mailer_type) {
                if (empty($mailer_type)) {
                    $mailer_type = 'gmail';
                }
                return $mailer_type;
            });
            $questions['mailer_type'] = $question;
        }
        if (!$input->getArgument('mailer_username')) {
            $question = new Question('Please give me the mailer username : ');
            $question->setValidator(function ($mailer_username) {
                if (empty($mailer_username)) {
                    throw new \Exception('Mailer username can not be empty');
                }
                return $mailer_username;
            });
            $questions['mailer_username'] = $question;
        }
        if (!$input->getArgument('mailer_password')) {
            $question = new Question('Please give me the mailer password : ');
            $question->setValidator(function ($mailer_password) {
                if (empty($mailer_password)) {
                    throw new \Exception('Mail username can not be empty');
                }
                return $mailer_password;
            });
            $questions['mailer_password'] = $question;
        }
        if (!$input->getArgument('app_env')) {
            $question = new Question('What is the Environement ? prod or dev ? [dev] :');
            $question->setValidator(function ($app_env) {
                if (empty($app_env)) {
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