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

use Github\Api\CurrentUser;
use Github\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractGithubCommand extends ContainerAwareCommand
{
    /**
     * Returns an authenticated GiHub client instance.
     *
     * @return Client
     */
    protected function getAuthenticatedClient()
    {
        $client = new Client();
        $client->authenticate(
            $this->getContainer()->getParameter('github_token'),
            null,
            Client::AUTH_HTTP_TOKEN
        );
        return $client;
    }

    /**
     * Returns the authenticated user's username.
     *
     * @param $client
     * @return mixed
     */
    protected function getAuthenticatedUsername(Client $client)
    {
        /** @var CurrentUser $currentUser */
        $currentUser = $client->api('me');
        $username = $currentUser->show()['login'];
        return $username;
    }

    /**
     * Adds the 'repository' command argument.
     *
     * @return Command The current instance
     */
    protected function addArgumentRepository()
    {
        return $this->addArgument(
            'repository',
            InputArgument::REQUIRED,
            'The full repository name, e.g. octocat/Hello-World.'
        );
    }

    /**
     * Returns the repository argument parts.
     *
     * @param InputInterface $input
     * @return array
     */
    protected function getRepositoryArgumentParts(InputInterface $input)
    {
        $parts = explode('/', $input->getArgument('repository'), 2);
        if (count($parts) !== 2) {
            throw new \InvalidArgumentException(
                'The repository has to be given in the username/repository format, e.g. octocat/Hello-World.'
            );
        }
        list($username, $repository) = $parts;
        return array($username, $repository);
    }
}