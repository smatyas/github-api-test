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
use Github\ResultPager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepoListCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:repo:list')
            ->setDescription('List the repositories.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getAuthenticatedClient();

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