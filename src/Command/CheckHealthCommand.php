<?php

declare(strict_types=1);

namespace Dbp\Relay\GreenlightConnectorCampusonlineBundle\Command;

use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\CampusonlineService;
use Dbp\Relay\GreenlightConnectorCampusonlineBundle\Service\LdapService;
use Psr\Log\NullLogger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckHealthCommand extends Command
{
    protected static $defaultName = 'dbp:relay:greenlight-connector-campusonline:check-health';

    private $ldap;
    private $co;

    public function __construct(LdapService $ldap, CampusonlineService $co)
    {
        parent::__construct();

        $this->co = $co;
        $this->ldap = $ldap;
    }

    protected function configure()
    {
        $this->setDescription('Download a picture for a CO user based on an API user ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->ldap->setLogger(new NullLogger());
        $output->writeln('<comment>GREENLIGHT-CONNECTOR-CAMPUSONLINE:</comment>');
        $output->writeln('  <comment>LDAP:</comment>');
        try {
            $this->ldap->checkConnection();
            $output->writeln('    <comment>CONNECTION:</comment> <info>OK</info>');
        } catch (\Throwable $e) {
            $output->writeln('    <comment>CONNECTION:</comment> <error>ERROR</error>');
            $output->writeln((string) $e);

            return 1;
        }

        $output->writeln(' <comment>CAMPUSONLINE:</comment>');
        try {
            $this->co->checkConnection();
            $output->writeln('    <comment>CONNECTION:</comment> <info>OK</info>');
        } catch (\Throwable $e) {
            $output->writeln('    <comment>CONNECTION:</comment> <error>ERROR</error>');
            $output->writeln((string) $e);

            return 1;
        }

        return 0;
    }
}
