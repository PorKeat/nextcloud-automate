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
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.oasis.opendocument.text',
                'application/vnd.oasis.opendocument.spreadsheet',
                'application/vnd.oasis.opendocument.presentation',
                'text/markdown',
                'text/plain',
                'application/msword',
                'application/vnd.ms-excel',
                'application/vnd.ms-powerpoint'
            ];

            $qb->select('f.fileid', 'f.path', 'f.name', 'f.mtime', 'f.size', 'm.mimetype')
               ->from('filecache', 'f')
               ->join('f', 'storages', 's', $qb->expr()->eq('f.storage', 's.numeric_id'))
               ->join('f', 'mimetypes', 'm', $qb->expr()->eq('f.mimetype', 'm.id'))
               ->where($qb->expr()->eq('s.id', $qb->createNamedParameter('home::' . $userId)))
               ->andWhere($qb->expr()->like('f.path', $qb->createNamedParameter('files/%')))
               ->andWhere($qb->expr()->in('m.mimetype', $qb->createNamedParameter($mimetypes, \OCP\DB\QueryBuilder\IQueryBuilder::PARAM_STR_ARRAY)))
               ->orderBy('f.mtime', 'DESC')
               ->setMaxResults(30);

            $result = $qb->executeQuery();
            $rows = method_exists($result, 'fetchAllAssociative') ? $result->fetchAllAssociative() : $result->fetchAll();
            $docs = [];

            foreach ($rows as $row) {
                $path = preg_replace('/^files\//', '', $row['path']);
                // Determine type for icon
                $type = 'document';
                if (strpos($row['mimetype'], 'spreadsheet') !== false || strpos($row['mimetype'], 'excel') !== false) {
                    $type = 'spreadsheet';
                } elseif (strpos($row['mimetype'], 'presentation') !== false || strpos($row['mimetype'], 'powerpoint') !== false) {
                    $type = 'presentation';
                }

                $openUrl = $this->urlGenerator->linkToRouteAbsolute('richdocuments.document.index', ['fileId' => $row['fileid']]);
                
                $docs[] = [
                    'fileid' => $row['fileid'],
                    'name' => $row['name'],
                    'path' => $path,
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
