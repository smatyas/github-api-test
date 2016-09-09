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

use Github\Api\Issue;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PRAddCommentCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:pr:comment-add')
            ->setAliases(['github:issue:comment-add'])
            ->setDescription('Add a new comment of the given issue/PR and repository. (Not review comment.)')
            ->addArgumentRepository()
            ->addArgument('number', InputArgument::REQUIRED, 'The issue/PR number.')
            ->addArgument('comment', InputArgument::REQUIRED, 'The comment text.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var Issue $issue */
        $issue = $client->api('issue');
        $issue->comments()->create(
            $username,
            $repository,
            $input->getArgument('number'),
            ['body' => $input->getArgument('comment')]
        );
        $output->writeln('Comment added.');
    }
}
