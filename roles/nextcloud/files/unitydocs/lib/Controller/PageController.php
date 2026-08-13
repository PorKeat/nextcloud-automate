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
     * @NoCSRFRequired
     */
    public function recentDocs() {
        try {
            $user = $this->userSession->getUser();
            if ($user === null) {
                return new JSONResponse(['error' => 'Not authenticated'], 401);
            }
            $userId = $user->getUID();

            $qb = $this->db->getQueryBuilder();
            
            $mimetypes = [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.text',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.presentation',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ];
            
            $orX = $qb->expr()->orX();
            foreach ($mimetypes as $mime) {
                $orX->add($qb->expr()->eq('m.mimetype', $qb->createNamedParameter($mime)));
            }
            $qb->andWhere($orX)
               ->orderBy('f.mtime', 'DESC')
               ->setMaxResults(20);

            $result = $qb->executeQuery()->fetchAllAssociative();

            $docs = [];
            foreach ($result as $row) {
                $type = 'document';
                if (strpos($row['mimetype'], 'spreadsheet') !== false || strpos($row['mimetype'], 'sheet') !== false) {
                    $type = 'spreadsheet';
                } elseif (strpos($row['mimetype'], 'presentation') !== false || strpos($row['mimetype'], 'powerpoint') !== false) {
                    $type = 'presentation';
                }

                $openUrl = $this->urlGenerator->linkToRouteAbsolute('richdocuments.document.index', ['fileId' => $row['fileid']]);
                
                $docs[] = [
                    'fileid' => $row['fileid'],
                    'name' => $row['name'],
                    'path' => $row['path'],
                    'type' => $type,
                    'mtime' => $row['mtime'],
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

            $openUrl = $this->urlGenerator->linkToRouteAbsolute('richdocuments.document.index', ['fileId' => $newFile->getId()]);

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
