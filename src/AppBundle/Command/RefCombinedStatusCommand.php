<?php
/**
 * This file is part of the github-api-test package.
 *
 * (c) Mátyás Somfai <somfai.matyas@gmail.com>
 * Created at 2016.09.12.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Command;

use Github\Api\Repo;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefCombinedStatusCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:ref:combined-status')
            ->setDescription('Get the combined status for a specific ref.')
            ->addArgumentRepository()
            ->addArgument(
                'ref',
                InputArgument::REQUIRED,
                'Ref to fetch the status for. It can be a SHA, a branch name, or a tag name.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var Repo $repo */
        $repo = $client->api('repo');
        $result = $repo->statuses()->combined(
            $username,
            $repository,
            $input->getArgument('ref')
        );

        $table = new Table($output);
        $table->setHeaders(['state', 'description', 'context', 'url']);
        $rows = 0;
        foreach ($result['statuses'] as $item) {
            $table->addRow([$item['state'], $item['description'], $item['context'], $item['target_url']]);
            $rows++;
        }
        $table->render();
        $output->writeln(sprintf('Listed %d statuses.', $rows));
        switch ($result['state']) {
            case 'failure':
                $combinedState = '<error>failure</error>';
                break;
            case 'pending':
                $combinedState = '<comment>pending</comment>';
                break;
            case 'success':
                $combinedState = '<info>success</info>';
                break;
            default:
                $combinedState = $result['state'];
                break;
        }
        $output->writeln(sprintf('Combined state: %s', $combinedState));
    }
}