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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PRMergeCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:pr:merge')
            ->setDescription('Merge a pull request.')
            ->addArgumentRepository()
            ->addArgument('number', InputArgument::REQUIRED, 'The PR number.')
            ->addArgument(
                'sha',
                InputArgument::REQUIRED,
                'SHA that pull request head must match to allow merge.'
            )
            ->addArgument(
                'commit-message',
                InputArgument::REQUIRED,
                'Extra detail to append to automatic commit message.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var PullRequest $pr */
        $pr = $client->api('pull_request');
        $pr->merge(
            $username,
            $repository,
            $input->getArgument('number'),
            $input->getArgument('commit-message'),
            $input->getArgument('sha')
        );
        $output->writeln('PR merged.');
    }
}
