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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WebhookListCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:webhook:list')
            ->setDescription('List the webhooks of the given repository.')
            ->addArgumentRepository();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var Repo $repo */
        $repo = $client->api('repo');
        $hooks = $repo->hooks()->all($username, $repository);

        $table = new Table($output);
        $table->setHeaders(['id', 'name', 'active', 'events', 'config']);
        $rows = 0;
        foreach ($hooks as $item) {
            $table->addRow([
                $item['id'],
                $item['name'],
                $item['active'] ? 'Yes' : 'No',
                json_encode($item['events']),
                json_encode($item['config'])
            ]);
            $rows++;
        }
        $table->render();
        $output->writeln(sprintf('Listed %d webhooks.', $rows));
    }
}