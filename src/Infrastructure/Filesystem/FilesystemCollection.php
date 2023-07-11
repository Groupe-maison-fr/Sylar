<?php

declare(strict_types=1);

namespace App\Infrastructure\Filesystem;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @extends ArrayCollection<int, FilesystemDTO>
 */
final class FilesystemCollection extends ArrayCollection
{
}
