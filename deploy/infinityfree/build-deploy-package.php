<?php

/**
 * Build a deployable ZIP package for InfinityFree / Byet.host (shared hosting
 * without SSH or Composer). Run this on your own machine:
 *
 *   php deploy/infinityfree/build-deploy-package.php
 *
 * Options:
 *   --no-build    skip `npm run build` (use existing public/build)
 *   --out=path    output ZIP path (default: build/itams-deploy.zip)
 *
 * What it does:
 *   1. composer install --no-dev --optimize-autoloader
 *   2. npm build frontend assets (unless --no-build)
 *   3. generate APP_KEY, CRON_TOKEN and APP_SETUP_TOKEN
 *   4. stage the whole app (minus dev/runtime junk) into build/itams-deploy/
 *   5. add htdocs/.htaccess (rewrite -> public/) and setup.php
 *   6. write .env for production (MySQL + database drivers)
 *   7. zip everything into build/itams-deploy.zip
 */

declare(strict_types=1);

const ROOT = __DIR__.'/../..';
const STAGE = ROOT.'/build/itams-deploy';
const ZIP = ROOT.'/build/itams-deploy.zip';

$skipAssets = in_array('--no-build', $argv, true);
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--out=')) {
        define('ZIP_OVERRIDE', substr($arg, 6));
    }
}
$zipPath = defined('ZIP_OVERRIDE') ? ZIP_OVERRIDE : ZIP;

function sh(string $cmd, string $cwd): int
{
    fwrite(STDOUT, "\n\$ [".str_replace(ROOT, '.', $cwd)."] $cmd\n");
    $prev = getcwd();
    chdir($cwd);
    passthru($cmd, $code);
    chdir($prev);
    if ($code !== 0) {
        fwrite(STDERR, "Command failed (exit $code): $cmd\n");
        exit($code);
    }

    return 0;
}

function removeDir(string $dir): void
{
    if (! is_dir($dir)) {
        return;
    }
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($it as $file) {
        $file->isDir() ? @rmdir($file->getPathname()) : @unlink($file->getPathname());
    }
    @rmdir($dir);
}

function copyApp(string $src, string $dst, array $excludes): void
{
    $filter = new RecursiveCallbackFilterIterator(
        new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS),
        static function (SplFileInfo $item) use ($src, $excludes): bool {
            $rel = ltrim(str_replace('\\', '/', substr($item->getPathname(), strlen($src))), '/');

            foreach ($excludes as $exclude) {
                if ($rel === $exclude || str_starts_with($rel, $exclude.'/')) {
                    return false; // prune this directory (and its whole subtree)
                }
            }

            $name = $item->getBasename();

            return $name !== '.git'
                && ! in_array($name, ['.github', '.dockerignore', '.editorconfig', '.gitattributes'], true)
                && ! str_starts_with($name, '.env');
        }
    );

    $it = new RecursiveIteratorIterator($filter, RecursiveIteratorIterator::SELF_FIRST);
    foreach ($it as $item) {
        $rel = ltrim(str_replace('\\', '/', substr($item->getPathname(), strlen($src))), '/');
        if ($item->isDir()) {
            @mkdir($dst.'/'.$rel, 0777, true);
        } elseif ($item->isFile()) {
            $target = $dst.'/'.$rel;
            @mkdir(dirname($target), 0777, true);
            copy($item->getPathname(), $target);
        }
    }
}

function randomToken(int $bytes = 24): string
{
    return rtrim(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), '=');
}

// ---------------------------------------------------------------- 1. notes
fwrite(STDOUT, "[1/7] Checking PHP/Composer/Node...\n");
sh('php -v', ROOT);

// ---------------------------------------------------------------- 2. assets
if ($skipAssets) {
    fwrite(STDOUT, "[2/7] Skipping asset build (--no-build).\n");
} elseif (! is_file(ROOT.'/public/build/manifest.json')) {
    fwrite(STDOUT, "[2/7] Building frontend assets...\n");
    sh('npm ci --no-audit --no-fund && npm run build', ROOT);
} else {
    fwrite(STDOUT, "[2/7] public/build already present (run --no-build to force skip).\n");
}

// ---------------------------------------------------------------- 3. keys
fwrite(STDOUT, "[3/7] Generating secrets...\n");
exec('php artisan key:generate --show 2>&1', $keyOut);
$appKey = trim(implode("\n", $keyOut));
if (! str_starts_with($appKey, 'base64:')) {
    $appKey = 'base64:'.base64_encode(random_bytes(32));
}
$setupToken = randomToken();
$cronToken = randomToken();

// ---------------------------------------------------------------- 4. stage
fwrite(STDOUT, "[4/7] Staging application files...\n");
removeDir(STAGE);
@mkdir(STAGE, 0777, true);

copyApp(ROOT, STAGE, [
    'build',          // local staging/output folder itself
    'node_modules',
    'vendor',         // installed inside the staging folder below
    'tests',
    'docs',
    'deploy',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'storage/app/public/qrcodes',
    'public/storage', // symlink -> recreated by setup.php (storage:link)
    'public/hot',
    'bootstrap/cache',
]);

// Install production dependencies inside the staging folder so the working
// tree's vendor/ (with dev tools) is left untouched.
fwrite(STDOUT, "[4/7] composer install --no-dev in staging...\n");
sh('composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts', STAGE);

// storage skeleton + .gitignore placeholders
$skeleton = [
    'storage/framework/cache/data',
    'storage/framework/cache',
    'storage/framework/sessions',
    'storage/framework/views',
    'storage/logs',
    'storage/app/public/qrcodes',
    'storage/app',
    'bootstrap/cache',
];
foreach ($skeleton as $dir) {
    @mkdir(STAGE.'/'.$dir, 0777, true);
    @file_put_contents(STAGE.'/'.$dir.'/.gitignore', "*\n!/.gitignore\n");
}

// ---------------------------------------------------------------- 5. helpers
fwrite(STDOUT, "[5/7] Adding .htaccess and setup.php...\n");
copy(__DIR__.'/templates/htdocs.htaccess', STAGE.'/.htaccess');
copy(__DIR__.'/templates/setup.php', STAGE.'/public/setup.php');

// ---------------------------------------------------------------- 6. .env
fwrite(STDOUT, "[6/7] Writing .env...\n");
$envTemplate = file_get_contents(__DIR__.'/templates/env.production');
$env = str_replace(
    ['{{APP_KEY}}', '{{SETUP_TOKEN}}', '{{CRON_TOKEN}}'],
    [$appKey, $setupToken, $cronToken],
    $envTemplate
);
file_put_contents(STAGE.'/.env', $env);

// ---------------------------------------------------------------- 7. zip
fwrite(STDOUT, "[7/7] Creating ZIP package...\n");
if (file_exists($zipPath)) {
    unlink($zipPath);
}
@mkdir(dirname($zipPath), 0777, true);

// ZipArchive is the reliable cross-platform option (bsdtar on Windows
// cannot write zip archives). May take a minute or two for the vendor dir.
$zip = new ZipArchive;
if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
    fwrite(STDERR, "Cannot create ZIP at $zipPath\n");
    exit(1);
}
$it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator(STAGE, FilesystemIterator::SKIP_DOTS),
    RecursiveIteratorIterator::SELF_FIRST
);
foreach ($it as $item) {
    $rel = ltrim(str_replace('\\', '/', substr($item->getPathname(), strlen(STAGE))), '/');
    if ($item->isDir()) {
        $zip->addEmptyDir($rel);
    } else {
        $zip->addFile($item->getPathname(), $rel);
    }
}
$zip->close();

if (! file_exists($zipPath)) {
    fwrite(STDERR, "ZIP creation failed.\n");
    exit(1);
}

removeDir(STAGE);

$size = round(filesize($zipPath) / 1048576, 1);
$site = 'YOUR-USERNAME.infinityfreeapp.com';

fwrite(STDOUT, "\n=============================================================\n");
fwrite(STDOUT, "Done! Package created: build/itams-deploy.zip ({$size} MB)\n");
fwrite(STDOUT, "=============================================================\n");
fwrite(STDOUT, "\nNext steps:\n");
fwrite(STDOUT, "  1. Sign up / log in at https://www.infinityfree.com\n");
fwrite(STDOUT, "  2. Create a new account/website -> note the free subdomain, e.g. {$site}\n");
fwrite(STDOUT, "  3. Control Panel -> MySQL Databases -> New Database -> note:\n");
fwrite(STDOUT, "       DB host (e.g. sql301.epizy.com), DB name, DB user, DB password\n");
fwrite(STDOUT, "  4. Control Panel -> PHP Settings -> set PHP 8.2 or 8.3\n");
fwrite(STDOUT, "  5. File Manager -> go to htdocs/ -> Upload itams-deploy.zip -> Extract (then delete the zip)\n");
fwrite(STDOUT, "  6. Edit htdocs/.env and fill in:\n");
fwrite(STDOUT, "       APP_URL        => https://{$site}\n");
fwrite(STDOUT, "       DB_HOST        => <from panel>\n");
fwrite(STDOUT, "       DB_DATABASE    => <from panel>\n");
fwrite(STDOUT, "       DB_USERNAME    => <from panel>\n");
fwrite(STDOUT, "       DB_PASSWORD    => <from panel>\n");
fwrite(STDOUT, "  7. In your browser open:  https://{$site}/setup.php?token={$setupToken}\n");
fwrite(STDOUT, "       -> runs migrations + seed (admin@itams.local / password)\n");
fwrite(STDOUT, "  8. DELETE htdocs/setup.php from the File Manager\n");
fwrite(STDOUT, "  9. Cron (reminders): https://cron-job.org -> every 5 min ->\n");
fwrite(STDOUT, "       https://{$site}/cron/{$cronToken}\n");
fwrite(STDOUT, "\nKeep these tokens secret:\n");
fwrite(STDOUT, "  APP_SETUP_TOKEN = {$setupToken}\n");
fwrite(STDOUT, "  CRON_TOKEN      = {$cronToken}\n");
