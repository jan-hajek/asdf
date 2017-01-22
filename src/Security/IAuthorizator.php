<?php
namespace Asdf\Security;

interface IAuthorizator extends \Nette\Security\IAuthorizator
{
    /**
     * @param array $roles pole roli
     * @param array $resources pole zdroju
     * @param array $access opravneni
     */
    public function __construct(array $roles, array $resources, array $access);

    /**
     * @param  string $resource
     * @return bool
     */
    public function hasResource($resource);
}
