<?php
/**
 * This file is part of the github-api-test package.
 *
 * (c) Mátyás Somfai <somfai.matyas@gmail.com>
 * Created at 2016.09.08.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Command;

use Github\Api\CurrentUser;
use Github\Client;
use Github\ResultPager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepoListCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('repo:list')
            ->setDescription('List the repositories.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = new Client();
        $client->authenticate(
            $this->getContainer()->getParameter('github_token'),
            null,
            Client::AUTH_HTTP_TOKEN
        );

        /** @var CurrentUser $currentUser */
        $currentUser = $client->api('me');
        $paginator = new ResultPager($client);
        $repos = $paginator->fetchAll($currentUser, 'repositories', ['all']);

        $table = new Table($output);
        $table->setHeaders(['id', 'full name', 'url']);
        $rows = 0;
        foreach ($repos as $item) {
            $table->addRow([$item['id'], $item['full_name'], $item['html_url']]);
            $rows++;
        }
        $table->render();
        $output->writeln(sprintf('Listed %d repositories.', $rows));
    }
}