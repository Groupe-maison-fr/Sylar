<?php

declare(strict_types=1);

namespace App\UserInterface\Web\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class ViteAssetExtension extends AbstractExtension
{
    /**
     * @var array<string, array{
     *     file: string,
     *     css: string,
     *     imports: string[]
     * }> $manifestData
     */
    private ?array $manifestData = null;

    public function __construct(
        private readonly string $manifest = '/app/public/build/manifest.json',
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_asset', $this->viteAsset(...), ['is_safe' => ['html']]),
        ];
    }

    public function viteAsset(string $entry): string
    {
        if ($this->manifestData === null) {
            $this->manifestData = json_decode(file_get_contents($this->manifest), true);
        }
        $file = $this->manifestData[$entry]['file'];
        $css = $this->manifestData[$entry]['css'] ?? [];
        $imports = $this->manifestData[$entry]['imports'] ?? [];
        $html = sprintf('<script type="module" src="/%s" defer></script>', $file);
        foreach ($css as $cssFile) {
            $html .= sprintf('<link rel="stylesheet" media="screen" href="/%s"/>', $cssFile);
        }

        foreach ($imports as $import) {
            $html .= sprintf('<link rel="modulepreload" href="/%s"/>', $import);
        }

        return $html;
    }
}
