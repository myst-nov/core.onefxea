<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit92b875ef27a6f56ad3ebb647b72054da
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MystNov\\Core\\' => 36,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MystNov\\Core\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit92b875ef27a6f56ad3ebb647b72054da::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit92b875ef27a6f56ad3ebb647b72054da::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit92b875ef27a6f56ad3ebb647b72054da::$classMap;

        }, null, ClassLoader::class);
    }
}
