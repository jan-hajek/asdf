<?php
namespace Asdf\Diagnostics\Panel\Profiling;

use Nette\Http\Request;
use Nette\Http\Session;
use Tracy\IBarPanel;

class Profiling implements IBarPanel
{
    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string[]
     */
    private $errorMessages = [];

    /**
     * @param Request $request
     * @param Session $session
     */
    public function __construct(Request $request, Session $session)
    {
        $sessionSection = $session->getSection('__profiling');
        if ($request->getPost('action') === 'switch') {
            $sessionSection['enabled'] = (bool)!$sessionSection['enabled'];
            header("Location: " . $request->getReferer());
            exit();
        }
        $this->enabled = $sessionSection['enabled'];

        $tidewaysExists = function_exists('tideways_enable');
        if (!$tidewaysExists) {
            $this->errorMessages[] = 'tideways_enable not exists';
        }

        if ($this->enabled && $tidewaysExists) {
            \tideways_enable(TIDEWAYS_FLAGS_NO_SPANS);

            register_shutdown_function(
                function () {
                    $data = \tideways_disable();
                    file_put_contents(
                        sys_get_temp_dir() . "/" . uniqid() . ".run.xhprof",
                        serialize($data)
                    );
                }
            );
        }
    }

    /**
     * @return string
     */
    public function getTab()
    {
        ob_start();
        require __DIR__ . '/templates/tab.phtml';

        return ob_get_clean();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return __CLASS__;
    }

    /**
     * @return string
     */
    public function getPanel()
    {
        ob_start();
        require __DIR__ . '/templates/panel.phtml';

        return ob_get_clean();
    }
}
