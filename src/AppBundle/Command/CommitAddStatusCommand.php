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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CommitAddStatusCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:commit:status-add')
            ->setDescription('Add a new status report to a commit.')
            ->addArgumentRepository()
            ->addArgument('sha', InputArgument::REQUIRED, 'The commit SHA.')
            ->addArgument(
                'state',
                InputArgument::REQUIRED,
                'The commit state. One of pending, success, error, or failure.'
            )
            ->addOption(
                'target-url',
                null,
                InputOption::VALUE_REQUIRED,
                'The target URL to associate with this status. (I.e. build output.)'
            )
            ->addOption(
                'description',
                null,
                InputOption::VALUE_REQUIRED,
                'A short description of the status.'
            )
            ->addOption(
                'context',
                null,
                InputOption::VALUE_REQUIRED,
                'A string label to differentiate this status from the status of other systems. (I.e. ci/jenkins)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var Repo $repo */
        $repo = $client->api('repo');
        $params = ['state' => $input->getArgument('state')];
        if ($input->hasOption('target-url') && !is_null($input->getOption('target-url'))) {
            $params['target_url'] = $input->getOption('target-url');
        }
        if ($input->hasOption('description') && !is_null($input->getOption('description'))) {
            $params['description'] = $input->getOption('description');
        }
        if ($input->hasOption('context') && !is_null($input->getOption('context'))) {
            $params['context'] = $input->getOption('context');
        }
        $repo->statuses()->create(
            $username,
            $repository,
            $input->getArgument('sha'),
            $params
        );
        $output->writeln('Status set.');
    }
}
