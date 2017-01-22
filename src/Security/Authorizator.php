<?php
namespace Asdf\Security;

use Nette\Security\Permission;

class Authorizator extends Permission implements IAuthorizator
{

	/**
	 * @inheritdoc
	 */
	public function __construct (array $roles, array $resources, array $access)
	{
		$this->setRoles($roles);
		$this->setResources($resources);
		$this->setAccess($access);
	}

	/**
	 * @inheritdoc
	 */
	protected function setRoles (array $roles)
	{
		foreach ($roles as $role) {
			$parent = isset($role['parent']) ? $role['parent'] : NULL;
			$this->addRole($role['name'], $parent);
		}
	}

	/**
     * @inheritdoc
	 */
	protected function setResources (array $resources)
	{
		foreach ($resources as $resource) {
			$parent = isset($resource['parent']) ? $resource['parent'] : NULL;
			$this->addResource($resource['name'], $parent);
		}

	}

	/**
	 * @inheritdoc
	 */
	protected function setAccess (array $access)
	{
		foreach ($access as $roleName => $rights) {
			if (isset($rights['allow'])) {
				foreach ($rights['allow'] as $right) {
					$resourceName = isset($right['resource']) ? $right['resource'] : NULL;
					$privilege = isset($right['privilege']) ? $right['privilege'] : NULL;
					$this->allow($roleName, $resourceName, $privilege);
				}
			}

			if (isset($rights['deny'])) {
				foreach ($rights['deny'] as $right) {
					$resourceName = isset($right['resource']) ? $right['resource'] : NULL;
					$privilege = isset($right['privilege']) ? $right['privilege'] : NULL;
					$this->deny($roleName, $resourceName, $privilege);
				}
			}
		}
	}
}
