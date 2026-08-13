<?php
namespace OCA\JitsiPro\Settings;

use OCP\Settings\IIconSection;
use OCP\IURLGenerator;

class AdminSection implements IIconSection {
    private $urlGenerator;

    public function __construct(IURLGenerator $urlGenerator) {
        $this->urlGenerator = $urlGenerator;
    }

    public function getID() {
        return 'jitsipro';
    }

    public function getName() {
        return 'Jitsi';
    }

    public function getPriority() {
        return 50;
    }

    public function getIcon() {
        return $this->urlGenerator->imagePath('jitsipro', 'app.svg');
    }
}
