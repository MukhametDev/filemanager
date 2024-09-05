<?php

namespace App\Controllers;

use App\DB\Database;
use App\Repository\DirectoryRepository;
use App\Repository\FileRepository;
use App\View\View;

class HomeController extends Controller
{
    private $fileRepository;
    private $directoryRepository;
    public function __construct(Database $db)
    {
        $this->directoryRepository = new DirectoryRepository($db);
        $this->fileRepository = new FileRepository($db);
    }
    public function index()
    {
        $directories = $this->directoryRepository->getAllDirectories();
        $files = $this->fileRepository->getAllFiles();
        $treeHtml = $this->buildTreeHtml($directories, $files);

        $data = [
            'title' => 'Файловый менеджер',
            'directories' => $treeHtml
        ];

        View::render('home', $data);
    }

    protected function buildTreeHtml($directories, $files, $parentId = null)
    {
        $html = '<ul>';

        foreach ($directories as $directory) {
            if ($directory->parent_id == $parentId) {
                $html .= '<li class="sidebar__directory" data-id="' . $directory->id . '">'
                    . htmlspecialchars($directory->name);

                // Рекурсивный вызов для дочерних директорий
                $html .= $this->buildTreeHtml($directories, $files, $directory->id);

                // Добавление файлов в текущую директорию
                $html .= '<ul>';
                foreach ($files as $file) {
                    if ($file->directory_id == $directory->id) {
                        $html .= '<li class="sidebar__file" data-id="' . $file->id . '"> '
                            . htmlspecialchars($file->name) .
                            '</li>';
                    }
                }
                $html .= '</ul>';

                $html .= '</li>';
            }
        }

        $html .= '</ul>';

        return $html;
    }

}
