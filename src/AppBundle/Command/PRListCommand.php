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

use Github\Api\PullRequest;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PRListCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:pr:list')
            ->setDescription('List the pull requests of the given repository.')
            ->addArgumentRepository()
            ->addOption(
                'state',
                null,
                InputOption::VALUE_REQUIRED,
                'Either open, closed, or all to filter by state.',
                'all'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var PullRequest $pr */
        $pr = $client->api('pull_request');
        $prs = $pr->all($username, $repository, ['state' => $input->getOption('state')]);

        $table = new Table($output);
        $table->setHeaders(['id', 'state', 'title', 'url', 'base', 'head']);
        $rows = 0;
        foreach ($prs as $item) {
            $table->addRow([
                $item['id'],
                $item['state'],
                $item['title'],
                $item['html_url'],
                $item['base']['label'],
                $item['head']['label']
            ]);
            $rows++;
        }
        $table->render();
        $output->writeln(sprintf('Listed %d pull requests.', $rows));
    }
}