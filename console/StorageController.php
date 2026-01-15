<?php

namespace app\console;

use app\components\Storage;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use League\Flysystem\FileAttributes;
use League\Flysystem\DirectoryAttributes;

/**
 * Console controller for managing and testing storage.
 */
class StorageController extends Controller
{
    /**
     * Test the storage by writing, reading, and deleting a small file.
     */
    public function actionTest()
    {
        $storage = new Storage();
        $filename = 'test_' . time() . '.txt';
        $content = 'Storage test content generated at ' . date('Y-m-d H:i:s');

        $this->stdout("Testing storage...\n", Console::BOLD);

        try {
            $this->stdout("Writing file '$filename'... ");
            $storage->put($filename, $content);
            $this->stdout("OK\n", Console::FG_GREEN);

            $this->stdout("Checking if file exists... ");
            if ($storage->fileExists($filename)) {
                $this->stdout("OK\n", Console::FG_GREEN);
            } else {
                $this->stdout("FAILED\n", Console::FG_RED);
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->stdout("Reading file... ");
            $readContent = $storage->read($filename);
            if ($readContent === $content) {
                $this->stdout("OK\n", Console::FG_GREEN);
            } else {
                $this->stdout("FAILED (Content mismatch)\n", Console::FG_RED);
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->stdout("Deleting file... ");
            $storage->delete($filename);
            $this->stdout("OK\n", Console::FG_GREEN);

            $this->stdout("Verifying deletion... ");
            if (!$storage->fileExists($filename)) {
                $this->stdout("OK\n", Console::FG_GREEN);
            } else {
                $this->stdout("FAILED (File still exists)\n", Console::FG_RED);
                return ExitCode::UNSPECIFIED_ERROR;
            }

            $this->stdout("\nStorage test passed successfully!\n", Console::FG_GREEN, Console::BOLD);
            return ExitCode::OK;

        } catch (\Exception $e) {
            $this->stderr("\nAn error occurred during storage test:\n", Console::FG_RED);
            $this->stderr($e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * List files in the storage.
     * @param string $path The directory to list.
     * @param int $recursive Whether to list files recursively (0 or 1).
     */
    public function actionList($path = '', $recursive = 0)
    {
        $storage = new Storage();

        try {
            $this->stdout("Listing contents of '$path':\n", Console::BOLD);
            $contents = $storage->listContents($path, (bool)$recursive);

            if (empty($contents)) {
                $this->stdout("Directory is empty.\n", Console::FG_YELLOW);
                return ExitCode::OK;
            }

            foreach ($contents as $item) {
                $type = $item instanceof DirectoryAttributes ? 'DIR ' : 'FILE';
                $size = $item instanceof FileAttributes ? $this->formatBytes($item->fileSize()) : '    ';
                $itemPath = $item->path();

                $this->stdout(sprintf("[%s] %10s %s\n", $type, $size, $itemPath));
            }

            return ExitCode::OK;
        } catch (\Exception $e) {
            $this->stderr("\nAn error occurred while listing storage contents:\n", Console::FG_RED);
            $this->stderr($e->getMessage() . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Formats bytes into a human-readable string.
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
