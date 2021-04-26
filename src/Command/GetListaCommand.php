<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Calculo\Calculo;

/**
 * Class GetListaCommand
 * @package App\Command
 */
class GetListaCommand extends Command
{

    /**
     * @var Calculo $calculoService
     */
    protected $calculoService;

    /**
     * GetListaCommand constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    protected function configure()
    {
        $this->setName('app:getLista')->setDescription('GetLista');
        $this->addArgument('csv');
        $this->addArgument('json');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'GetLista',
            '========',
            '',
        ]);

        $resultado = $this->calculoService->calcula($input->getArgument('csv'), $input->getArgument('json'));

        $output->writeln([
            $resultado->getContent(),
            '',
        ]);

        return 0;
    }

    /**
     * @param Calculo $calculo
     * @required
     */
    public function setCalculo(Calculo $calculoService)
    {
        $this->calculoService = $calculoService;
    }
}
