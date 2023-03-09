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
            ->addArgument('output', InputArgument::REQUIRED, "Output file name");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fname = $input->getArgument('output');
        $drawBoxes = $input->getOption('drawBoxes');

        $output->writeln('Generating pdf');

        $calls = $this->callRepository->findAll();
        $pdf = new CardPDF($calls, drawBoxes: $drawBoxes);

        $pdf->drawAll();

        $doc = $pdf->Output('', 'S');
        file_put_contents($fname, $doc);

        return Command::SUCCESS;
    }
}