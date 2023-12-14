<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit596cd05a3a1818d90018bb480fe0a296
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'JsonSchema\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'JsonSchema\\' => 
        array (
            0 => __DIR__ . '/..' . '/justinrainbow/json-schema/src/JsonSchema',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit596cd05a3a1818d90018bb480fe0a296::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit596cd05a3a1818d90018bb480fe0a296::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit596cd05a3a1818d90018bb480fe0a296::$classMap;

        }, null, ClassLoader::class);
    }
}
