<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Command;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\PersonPhotoProvider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PictureFetchCommand extends Command
{
    protected static $defaultName = 'dbp:relay:greenlight-connector-campusonline:picture-fetch';

    private $provider;

    public function __construct(PersonPhotoProvider $provider)
    {
        parent::__construct();

        $this->provider = $provider;
    }

    protected function configure()
    {
        $this->setDescription('Download a picture for a CO user based on an API user ID');
        $this->addArgument('user-id', InputArgument::REQUIRED, 'The API user ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $provider = $this->provider;
        $userId = $input->getArgument('user-id');

        $data = $provider->getPhotoDataForUser($userId);
        $filename = urlencode($userId).'.jpg';
        $output->writeln('Creating '.$filename);
        file_put_contents($filename, $data);

        return 0;
    }
}
