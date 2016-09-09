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
use Github\ResultPager;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PRCommentsCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:pr:comments')
            ->setAliases(['github:issue:comments'])
            ->setDescription('List the comments of the given issue/PR and repository. (Not the review comments.)')
            ->addArgumentRepository()
            ->addArgument('number', InputArgument::REQUIRED, 'The issue/PR number.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var Issue $issue */
        $issue = $client->api('issue');
        $paginator = new ResultPager($client);
        $comments = $paginator->fetchAll(
            $issue->comments(),
            'all',
            [$username, $repository, $input->getArgument('number')]
        );

        $table = new Table($output);
        $table->setHeaders(['id', 'user', 'created at', 'body']);
        $rows = 0;
        foreach ($comments as $item) {
            $table->addRow([
                $item['id'],
                $item['user']['login'],
                $item['created_at'],
                $item['body']
            ]);
            $rows++;
        }
        $table->render();
        $output->writeln(sprintf('Listed %d comments.', $rows));
    }
}
