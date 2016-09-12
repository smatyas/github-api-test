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

use Github\Api\Repo;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class WebhookAddCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:webhook:add')
            ->setDescription('Add a new webhook to the given repository.')
            ->addArgumentRepository()
            ->addArgument('config', InputArgument::REQUIRED, 'Key/value pairs to provide settings for this hook.')
            ->addOption(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'The name of a valid GitHub service.',
                'web'
            )
            ->addOption(
                'event',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Determines what events the hook is triggered for.',
                ['push']
            )
            ->addOption(
                'inactive',
                null,
                InputOption::VALUE_NONE,
                'Create the hook as inactive. (It won\'t be triggered.)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        $params = [
            'name' => $input->getOption('name'),
            'config' => json_decode($input->getArgument('config')),
            'active' => !$input->getOption('inactive'),
        ];

        if (!is_null($input->getOption('event'))) {
            $params['events'] = $input->getOption('event');
        }

        /** @var Repo $repo */
        $repo = $client->api('repo');
        $repo->hooks()->create($username, $repository, $params);

        $output->writeln('Hook created.');
    }
}