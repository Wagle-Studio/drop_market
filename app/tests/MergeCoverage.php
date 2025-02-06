<?php

require "vendor/autoload.php";

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Report\Html\Facade as HtmlReport;

const TEMP_COVERAGES_FOLDER = "build/temp";
const FINAL_COVERAGES_FOLDER = "build/coverage-html";

function loadCoverageFile(string $file): CodeCoverage
{
    $coverage = @include $file;

    if (!$coverage instanceof CodeCoverage) {
        throw new Exception("Le fichier '$file' n'est pas un fichier de couverture valide.");
    }

    return $coverage;
}

function mergeCoverageReports(string $tempDir, string $finalDir): void
{
    if (!is_dir($tempDir)) {
        echo "[Erreur] Le dossier '$tempDir' n'existe pas.\n";
        exit(1);
    }

    $files = glob("$tempDir/*.cov");

    if (empty($files)) {
        echo "[Erreur] Aucun fichier de couverture trouvé dans '$tempDir'.\n";
        exit(1);
    }

    echo "[Info] Fichiers trouvés :\n" . implode("\n", $files) . "\n";

    try {
        $mergedCoverage = null;

        foreach ($files as $file) {
            echo "[Info] Chargement du fichier : $file\n";
            $currentCoverage = loadCoverageFile($file);

            if ($mergedCoverage === null) {
                $mergedCoverage = $currentCoverage;
            } else {
                $mergedCoverage->merge($currentCoverage);
            }
        }

        echo "[Info] Génération du rapport HTML\n";
        $report = new HtmlReport();
        $report->process($mergedCoverage, "$finalDir");

        echo "[Info] Rapport HTML généré dans '$tempDir/coverage-html'.\n";
    } catch (Exception $e) {
        echo "[Erreur] Une Erreur s'est produite : " . $e->getMessage() . "\n";
        exit(1);
    }
}

mergeCoverageReports(TEMP_COVERAGES_FOLDER, FINAL_COVERAGES_FOLDER);
