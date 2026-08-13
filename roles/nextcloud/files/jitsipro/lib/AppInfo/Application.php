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
    }
}
