<?php
namespace OCA\UnityDocs\Controller;

use OCP\IRequest;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IUserSession;
use OCP\IURLGenerator;
use OCP\IDBConnection;
use OCP\Files\IRootFolder;
use OCP\Files\NotFoundException;
use OCP\Files\NotPermittedException;

class PageController extends Controller {
    private $userSession;
    private $urlGenerator;
    private $db;
    private $rootFolder;

    public function __construct(
        string $appName,
        IRequest $request,
        IUserSession $userSession,
        IURLGenerator $urlGenerator,
        IDBConnection $db,
        IRootFolder $rootFolder
    ) {
        parent::__construct($appName, $request);
        $this->userSession = $userSession;
        $this->urlGenerator = $urlGenerator;
        $this->db = $db;
        $this->rootFolder = $rootFolder;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {
        return new TemplateResponse('unitydocs', 'index');
    }

    /**
     * @NoAdminRequired
     * @UseSession
     */
    public function recentDocs() {
        try {
            $user = $this->userSession->getUser();
            if ($user === null) {
                return new JSONResponse(['error' => 'Not authenticated'], 401);
            }

            $userFolder = $this->rootFolder->getUserFolder($user->getUID());

            $mimetypes = [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.oasis.opendocument.text',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.presentation'
            ];
            
            $allFiles = [];
            foreach ($mimetypes as $mime) {
                $files = $userFolder->searchByMime($mime);
                foreach ($files as $file) {
                    $allFiles[] = $file;
                }
            }

            usort($allFiles, function($a, $b) {
                return $b->getMTime() - $a->getMTime();
            });

            $allFiles = array_slice($allFiles, 0, 20);

            $docs = [];
            foreach ($allFiles as $file) {
                $mime = $file->getMimetype();
                $type = 'document';
                if (strpos($mime, 'spreadsheet') !== false || strpos($mime, 'sheet') !== false) {
                    $type = 'spreadsheet';
                } elseif (strpos($mime, 'presentation') !== false || strpos($mime, 'powerpoint') !== false) {
                    $type = 'presentation';
                }

                $openUrl = $this->urlGenerator->getBaseUrl() . '/index.php/f/' . $file->getId();
                
                $docs[] = [
                    'fileid' => $file->getId(),
                    'name' => $file->getName(),
                    'path' => $file->getInternalPath(),
                    'type' => $type,
                    'mtime' => $file->getMTime(),
                    'url' => $openUrl
                ];
            }

            return new JSONResponse([
                'status' => 'success',
                'documents' => $docs
            ]);
            
        } catch (\Exception $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        } catch (\Throwable $e) {
            return new JSONResponse([
                'status' => 'error',
                'message' => 'Critical error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @NoAdminRequired
     */
    public function createDoc() {
        try {
            $user = $this->userSession->getUser();
            if ($user === null) {
                return new JSONResponse(['error' => 'Not authenticated'], 401);
            }

            $type = $this->request->getParam('type', 'document');
            $ext = 'docx';
            $prefix = 'Untitled Document';
            
            if ($type === 'spreadsheet') {
                $ext = 'xlsx';
                $prefix = 'Untitled Spreadsheet';
            } elseif ($type === 'presentation') {
                $ext = 'pptx';
                $prefix = 'Untitled Presentation';
            }

            $templatePath = '/var/www/html/custom_apps/richdocuments/emptyTemplates/template.' . $ext;
            if (!file_exists($templatePath)) {
                return new JSONResponse(['status' => 'error', 'message' => 'Template engine not installed.'], 500);
            }

            $content = file_get_contents($templatePath);
            $userFolder = $this->rootFolder->getUserFolder($user->getUID());
            
            // Find a unique name
            $filename = $prefix . '.' . $ext;
            $counter = 1;
            while ($userFolder->nodeExists($filename)) {
                $filename = $prefix . ' (' . $counter . ').' . $ext;
                $counter++;
            }

            $newFile = $userFolder->newFile($filename);
            $newFile->putContent($content);

            $openUrl = $this->urlGenerator->getBaseUrl() . '/index.php/f/' . $newFile->getId();

            return new JSONResponse([
                'status' => 'success',
                'url' => $openUrl
            ]);
            
        } catch (\Exception $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (\Throwable $e) {
            return new JSONResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
