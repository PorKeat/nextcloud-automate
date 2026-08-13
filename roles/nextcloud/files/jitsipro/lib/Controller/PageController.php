<?php
namespace OCA\JitsiPro\Controller;

use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\FeaturePolicy;
use OCP\IConfig;
use OCP\IUserSession;
use OCP\IURLGenerator;

class PageController extends Controller {
    private $config;
    private $userSession;
    private $urlGenerator;

    public function __construct(
        $AppName,
        IRequest $request,
        IConfig $config,
        IUserSession $userSession,
        IURLGenerator $urlGenerator
    ) {
        parent::__construct($AppName, $request);
        $this->config = $config;
        $this->userSession = $userSession;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        $url = $this->config->getAppValue('jitsipro', 'jitsi_url', 'https://jetsi.sengporkeat.com');
        $userId = $this->userSession->getUser()->getUID();
        $displayName = $this->userSession->getUser()->getDisplayName();
        $jitsiUrl = $this->config->getAppValue('jitsipro', 'jitsi_url', 'https://meet.jit.si');
        $jitsiWatermark = $this->config->getAppValue('jitsipro', 'jitsi_watermark', '');
        
        $avatarUrl = $this->urlGenerator->getAbsoluteURL('/index.php/avatar/' . urlencode($userId) . '/512');

        $response = new TemplateResponse('jitsipro', 'main', [
            'display_name' => $displayName,
            'jitsi_url' => $jitsiUrl,
            'jitsi_watermark' => $jitsiWatermark,
            'avatar_url' => $avatarUrl
        ]);
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedScriptDomain($url);
        $csp->addAllowedFrameDomain($url);
        $response->setContentSecurityPolicy($csp);

        $fp = new FeaturePolicy();
        $fp->addAllowedCameraDomain('*');
        $fp->addAllowedMicrophoneDomain('*');
        $fp->addAllowedFullScreenDomain('*');
        $response->setFeaturePolicy($fp);
        
        return $response;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function saveSettings() {
        try {
            $jitsi_url = $this->request->getParam('jitsi_url', '');
            $app_name = $this->request->getParam('app_name', '');
            $app_icon = $this->request->getParam('app_icon', '');
            $jitsi_watermark = $this->request->getParam('jitsi_watermark', '');

            if (!empty($jitsi_url)) {
                $this->config->setAppValue('jitsipro', 'jitsi_url', $jitsi_url);
            }
            
            if (!empty($app_name)) {
                $this->config->setAppValue('jitsipro', 'app_name', $app_name);
            }
            
            if (!empty($app_icon)) {
                $this->config->setAppValue('jitsipro', 'app_icon', $app_icon);
            }
            
            if ($jitsi_watermark !== null) {
                $this->config->setAppValue('jitsipro', 'jitsi_watermark', $jitsi_watermark);
            }

            return new JSONResponse(['status' => 'success']);
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        } catch (\Throwable $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }
}
