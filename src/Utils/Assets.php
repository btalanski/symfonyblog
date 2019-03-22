<?php

namespace App\Utils;

use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\UrlPackage;
use Symfony\Component\Asset\PathPackage;
use Symfony\Component\Asset\VersionStrategy\JsonManifestVersionStrategy;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class Assets {

    public $frontend_assets;

    public function __construct() {
        $this->frontend_assets = new Package(new JsonManifestVersionStrategy('assets/frontend/manifest.json'));
        $this->uploads = new Package(new EmptyVersionStrategy());
    }
}