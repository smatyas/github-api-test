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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RepoListDeployKeysCommand extends AbstractGithubCommand
{
    protected function configure()
    {
        $this->setName('github:repo:list-deploy-keys')
            ->setDescription('List deploy keys for the given repository.')
            ->addArgumentRepository();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        list($username, $repository) = $this->getRepositoryArgumentParts($input);

        $client = $this->getAuthenticatedClient();

        /** @var Repo $repo */
        $repo = $client->api('repo');
        $keys = $repo->keys()->all($username, $repository);

        $table = new Table($output);
        $table->setHeaders(['id', 'title', 'created at', 'read-only?']);
        $rows = 0;
        foreach ($keys as $item) {
            $table->addRow([
                $item['id'],
                $item['title'],
                $item['created_at'],
                $item['read_only'] ? 'Yes' : 'No',
            ]);
            $rows++;
        }
        $table->render();
        $output->writeln(sprintf('Listed %d keys.', $rows));
    }
}