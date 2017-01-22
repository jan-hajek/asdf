<?php
namespace Asdf\Diagnostics\Panel\Profiling;

use Nette\Http\Request;
use Nette\Http\Session;
use Tracy\IBarPanel;

class Profiling implements IBarPanel
{
    private $enabled;

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

        if ($this->enabled) {
            tideways_enable(TIDEWAYS_FLAGS_NO_SPANS);

            register_shutdown_function(
                function () {
                    $data = tideways_disable();
                    file_put_contents(
                        sys_get_temp_dir() . "/profiling/homer/" . uniqid() . ".xhprof",
                        serialize($data)
                    );
                }
            );
        }
    }

    public function getTab()
    {
        ob_start();
        require __DIR__ . '/templates/tab.phtml';

        return ob_get_clean();
    }

    public function getId()
    {
        return __CLASS__;
    }

    public function getPanel()
    {
        ob_start();
        require __DIR__ . '/templates/panel.phtml';

        return ob_get_clean();
    }
}

