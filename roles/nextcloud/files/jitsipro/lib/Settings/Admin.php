<?php
namespace OCA\JitsiPro\Settings;

use OCP\Settings\ISettings;
use OCP\IConfig;
use OCP\IURLGenerator;
use OCP\AppFramework\Http\TemplateResponse;

class Admin implements ISettings {
    private $config;
    private $urlGenerator;

    public function __construct(IConfig $config, IURLGenerator $urlGenerator) {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    public function getForm() {
        $logPath = '/var/www/html/data/nextcloud.log';
        $serverLogs = 'Log file not found or unreadable.';
        if (file_exists($logPath) && is_readable($logPath)) {
            $file = fopen($logPath, 'r');
            fseek($file, -15000, SEEK_END);
            $serverLogs = fread($file, 15000);
            fclose($file);
            $lines = explode("\n", $serverLogs);
            $serverLogs = implode("\n", array_slice($lines, -50));
        }

        $jitsiUrl = $this->config->getAppValue('jitsipro', 'jitsi_url', 'https://meet.jit.si');
        $appName = $this->config->getAppValue('jitsipro', 'app_name', 'Video Call');
        $appIcon = $this->config->getAppValue('jitsipro', 'app_icon', $this->urlGenerator->imagePath('jitsipro', 'app.svg'));
        $jitsiWatermark = $this->config->getAppValue('jitsipro', 'jitsi_watermark', '');

        $response = new TemplateResponse('jitsipro', 'admin', [
            'jitsi_url' => $jitsiUrl,
            'app_name' => $appName,
            'app_icon' => $appIcon,
            'jitsi_watermark' => $jitsiWatermark,
            'server_logs' => $serverLogs,
        ]);
        return $response;
    }

    public function getSection() {
        return 'jitsipro';
    }

    public function getPriority() {
        return 10;
    }
}
