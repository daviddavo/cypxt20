<?php
namespace App\Command;

use App\Entity\OnlineCall;
use App\Repository\OnlineCallRepository;

use App\Util\CardPDF;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'cypxt:cards-pdf',
    description: 'Creates the cards pdf.',
    hidden: false,
)]
class CardsPdfCommand extends Command
{
    protected static $defaultName = 'cypxt:cards-pdf';

    public function __construct(
        protected OnlineCallRepository $callRepository,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp("This command allows you to generate the cards pdf")
            ->addOption('drawBoxes', 'b', InputOption::VALUE_NEGATABLE, "Wether to print boxes for debugging")
            ->addOption('drawLines', 'l', InputOption::VALUE_NEGATABLE, "Wether to print lines for debugging")
            ->addOption('height', 'H', InputOption::VALUE_REQUIRED, 'Altura de las tarjetas', 105)
            ->addOption('width', 'W', InputOption::VALUE_REQUIRED, 'Ancho de las tarjetas', 150)
            ->addOption('firstLineHeight', null, InputOption::VALUE_REQUIRED, 'Alto de la primera línea', 14)
            ->addOption('compact', 'c', InputOption::VALUE_NEGATABLE, 'Usar modo compacto', False)
            ->addArgument('output', InputArgument::REQUIRED, "Output file name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fname = $input->getArgument('output');
        $w = $input->getOption('width');
        $h = $input->getOption('height');

        $output->writeln('Generating pdf');

        $calls = $this->callRepository->findAll();
        $pdf = new CardPDF($calls, 
            'Título',
            $h, $w,
            firstLineHeight: $input->getOption('firstLineHeight'),
            drawBoxes: $input->getOption('drawBoxes'),
            drawLines: $input->getOption('drawLines'),
            compact: $input->getOption('compact'),
        );

        $pdf->drawAll();

        $doc = $pdf->Output('', 'S');
        file_put_contents($fname, $doc);

        return Command::SUCCESS;
    }
}