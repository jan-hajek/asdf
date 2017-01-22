<?php

namespace Asdf\Diagnostics\Panel;

use Tracy\IBarPanel;

class GitBranchPanel implements IBarPanel
{

	public function getPanel()
	{
		return '';
	}

	protected function getCurrentBranchName()
	{
		$scriptPath = $_SERVER['SCRIPT_FILENAME'];

		$dir = realpath(dirname($scriptPath));
		while ($dir !== false && !is_dir($dir . '/.git')) {
			flush();
			$currentDir = $dir;
			$dir .= '/..';
			$dir = realpath($dir);

			// Stop recursion to parent on root directory
			if ($dir == $currentDir) {
				break;
			}
		}

		$head = $dir . '/.git/HEAD';
		if ($dir && is_readable($head)) {
			$branch = file_get_contents($head);
			if (strpos($branch, 'ref:') === 0) {
				$parts = explode('/', $branch);
				return $parts[2];
			}
			return '(' . substr($branch, 0, 7) . '&hellip;)';
		}

		return 'not versioned';
	}

	public function getTab()
	{
		ob_start();
		require __DIR__ . '/Tab.phtml';
		return ob_get_clean();
	}

}
