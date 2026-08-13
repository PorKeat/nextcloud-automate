<?php
namespace OCA\UnityDocs\AppInfo;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;

class Application extends App implements IBootstrap {
    public function __construct(array $urlParams = []) {
        parent::__construct('unitydocs', $urlParams);
    }

    public function register(\OCP\AppFramework\Bootstrap\IRegistrationContext $context): void {
    }

    public function boot(IBootContext $context): void {
    }
}
