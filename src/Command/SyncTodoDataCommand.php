<?php

namespace App\Command;

use App\Entity\Provider;
use App\Entity\TodoList;
use App\Provider\ProviderManager;
use App\Repository\RepositoryResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SyncTodoDataCommand extends Command
{
    /** @var EntityManagerInterface $em */
    private $em;

    protected static $defaultName = 'sync:todo-data';
    protected static $defaultDescription = 'Add a short description for your command';

    protected $adapterPath = "App\\Provider\\Adapters\\";

    /**
     * @param $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct(self::$defaultName);
        $this->em = $em;
    }


    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $providerManager = new ProviderManager();
        $providerRepo = $this->em->getRepository(Provider::class);
        $todoListRepo = $this->em->getRepository(TodoList::class);
        /** @var RepositoryResponse $allProviderResponse */
        $allProviderResponse = $providerRepo->findAll();
        if($allProviderResponse->isSuccess()){
            $allProvider = $allProviderResponse->getResponse();
            /** @var Provider $providerSingle */
            foreach ($allProvider as $providerSingle)
            {
                $adapterClassName = $this->adapterPath.$providerSingle->getCName();
                $providerAdapter = new $adapterClassName();
                $providerResponse = $providerManager->getProviderData($providerAdapter,$providerSingle);
                if($providerResponse !== null){
                    foreach ($providerResponse as $todo){
                        $hash = md5(json_encode($todo));
                        $todoRes = $todoListRepo->findOneBy(['hash' => $hash]);
                        if($todoRes){
                            continue;
                        }
                        $todo['hash'] = $hash;
                        $todoListRepo->insert($todo);
                    }
                }
            }
        }

        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return 0;
    }
}
