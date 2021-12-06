<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Command;

use Dbp\CampusonlineApi\UCard\UCardException;
use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\CampusonlineService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PictureFetchCommand extends Command
{
    protected static $defaultName = 'dbp:picture-fetch';

    private $service;

    public function __construct(CampusonlineService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    protected function configure()
    {
        $this->setDescription('Download pictures for a CO user');
        $this->addArgument('ident', InputArgument::REQUIRED, 'The IDENT-NR-OBFUSCATED of the user.');
    }

    /**
     * @throws UCardException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service = $this->service;

        $ident = $input->getArgument('ident');
        $cards = $service->getCardsForIdent($ident);
        if (count($cards) === 0) {
            $output->writeln("No pictures found for '$ident'");

            return 0;
        }
        foreach ($cards as $card) {
            $pic = $service->getCardPicture($card);
            $filename = $card->ident.'-'.$card->cardType.'-'.$pic->id.'.jpg';
            $output->writeln('Creating '.$filename);
            file_put_contents($filename, $pic->content);
        }

        return 0;
    }
}
