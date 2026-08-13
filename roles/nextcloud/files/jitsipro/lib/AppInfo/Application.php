<?php
namespace OCA\JitsiPro\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;

class Application extends App implements IBootstrap {
    public function __construct(array $urlParams = []) {
        parent::__construct('jitsipro', $urlParams);
    }

    public function register(IRegistrationContext $context): void {
        // Nothing to register
    }

    public function boot(IBootContext $context): void {
        $context->injectFn(\Closure::fromCallable([$this, 'registerNavigation']));
    }

    protected function registerNavigation(\OCP\Navigation\IManager $navigationManager, \OCP\IURLGenerator $urlGenerator, \OCP\IConfig $config): void {
        $appName = $config->getAppValue('jitsipro', 'app_name', 'Video Call');
        $appIcon = $config->getAppValue('jitsipro', 'app_icon', '');
        
        if (empty($appIcon)) {
            $appIcon = $urlGenerator->imagePath('jitsipro', 'app.svg');
        }

        $navigationManager->add(function() use ($urlGenerator, $appName, $appIcon) {
            return [
                'id' => 'jitsipro',
                'order' => 10,
                'href' => $urlGenerator->linkToRoute('jitsipro.page.index'),
                'icon' => $appIcon,
                'name' => $appName,
            ];
        });
    }
}
