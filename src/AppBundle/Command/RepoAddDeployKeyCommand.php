<?php
/**
 * This file is part of the github-api-test package.
 *
 * (c) Mátyás Somfai <somfai.matyas@gmail.com>
 * Created at 2016.09.09.
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

class RepoAddDeployKeyCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:repo:add-deploy-key')
            ->setDescription('Add a deploy key for the given repository.')
            ->addArgumentRepository()
            ->addArgument('key-title', InputArgument::REQUIRED, 'The key title.')
            ->addArgument('key-value', InputArgument::REQUIRED, 'The key value.')
            ->addOption('read-only', null, InputOption::VALUE_NONE, 'The deploy key will be read-only.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var Repo $repo */
        $repo = $client->api('repo');
        $key = $repo->keys()->create(
            $username,
            $repository,
            [
                'title' => $input->getArgument('key-title'),
                'key' => $input->getArgument('key-value'),
                'read_only' => $input->getOption('read-only')
            ]
        );

        $output->writeln(sprintf('Deploy key "%s" added.', $key['title']));
    }
}