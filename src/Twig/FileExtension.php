<?php

declare(strict_types=1);

namespace TravelOrganizer\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FileExtension extends AbstractExtension
{
    private $projectDirectory;

    public function __construct(string $projectDirectory)
    {
        $this->projectDirectory = $projectDirectory;
    }

    /**
     * @uses FileExtension::getFileLines()
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('file_coverage', [$this, 'getFileCoverage'], ['is_safe' => ['html']]),
        ];
    }

    public function getFileCoverage(string $file, array $highlightLines): string
    {
        $file = $this->projectDirectory.DIRECTORY_SEPARATOR.$file;

        if (!is_file($file) || !is_readable($file)) {
            return '';
        }

        $lineStart = array_key_first($highlightLines);
        $lineEnd = array_key_last($highlightLines);

        // highlight_file could throw warnings
        // see https://bugs.php.net/25725
        $code = @highlight_file($file, true);
        // remove main code/span tags
        $code = preg_replace('#^<code.*?>\s*<span.*?>(.*)</span>\s*</code>#s', '\\1', $code);
        // split multiline spans
        $code = preg_replace_callback('#<span ([^>]++)>((?:[^<]*+<br \/>)++[^<]*+)</span>#', function ($m) {
            return "<span $m[1]>".str_replace('<br />', "</span><br /><span $m[1]>", $m[2]).'</span>';
        }, $code);
        $content = explode('<br />', $code);

        $lines = [];
        $start = max($lineStart - 5, 1);
        $end = min($lineEnd + 5, count($content));
        for ($i = $start; $i <= $end; ++$i) {
            $bgHighlight = array_key_exists($i, $highlightLines) && 1 === $highlightLines[$i] ? ' style="background: #EEEE88"' : '';
            $lines[] = '<li><a class="anchor" name="line'.$i.'"></a><code '.$bgHighlight.'>'.$this->fixCodeMarkup($content[$i - 1]).'</code></li>';
        }

        return '<ol start="'.$start.'">'.implode("\n", $lines).'</ol>';
    }

    private function fixCodeMarkup($line)
    {
        // </span> ending tag from previous line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $closing && (false === $opening || $closing < $opening)) {
            $line = substr_replace($line, '', $closing, 7);
        }

        // missing </span> tag at the end of line
        $opening = strpos($line, '<span');
        $closing = strpos($line, '</span>');
        if (false !== $opening && (false === $closing || $closing > $opening)) {
            $line .= '</span>';
        }

        return trim($line);
    }
}
