<?php

return array(
    /*
    |--------------------------------------------------------------------------
    | Upload dir
    |--------------------------------------------------------------------------
    |
    | The dir where to store the images (relative from public)
    |
    */
    'dir' => ['assets/media/images/uploads'],//'assets/media'
    'startPathHash' => 'assets/media/images/',
    /*
    |--------------------------------------------------------------------------
    | Filesystem disks (Flysytem)
    |--------------------------------------------------------------------------
    |
    | Define an array of Filesystem disks, which use Flysystem.
    | You can set extra options, example:
    |
    | 'my-disk' => [
    |        'URL' => url('to/disk'),
    |        'alias' => 'Local storage',
    |    ]
    */
    'disks' => [

    ],

    /*
    |--------------------------------------------------------------------------
    | Routes group config
    |--------------------------------------------------------------------------
    |
    | The default group settings for the elFinder routes.
    |
    */

    'route' => [
        'prefix' => 'admin/elfinder',
        'middleware' => ['web'], //Set to null to disable middleware filter ['web', 'App\Http\Middleware\FileBrowser']
    ],

    /*
    |--------------------------------------------------------------------------
    | Access filter
    |--------------------------------------------------------------------------
    |
    | Filter callback to check the files
    |
    */

    'access' => 'Barryvdh\Elfinder\Elfinder::checkAccess',

    /*
    |--------------------------------------------------------------------------
    | Roots
    |--------------------------------------------------------------------------
    |
    | By default, the roots file is LocalFileSystem, with the above public dir.
    | If you want custom options, you can set your own roots below.
    |
    */

    'roots' => array(

    ),

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    |
    | These options are merged, together with 'roots' and passed to the Connector.
    | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1
    |
    */

    'options' => [

        'driver' => 'LocalFileSystem',
        // 'path'   => '/assets/media/image/',
        // 'URL'    => 'http://vicloud.dev/assets/media/image/',
        'bind12' => array(
            'upload.pre mkdir.pre mkfile.pre rename.pre archive.pre ls.pre' => array(
                'Plugin.Sanitizer.cmdPreprocess'
            ),
            'ls' => array(
                'Plugin.Sanitizer.cmdPostprocess'
            ),
            'upload.presave' => array(
                'Plugin.Sanitizer.onUpLoadPreSave',
                'Plugin.AutoResize.onUpLoadPreSave',
                // 'Plugin.Watermark.onUpLoadPreSave',
                'Plugin.AutoRotate.onUpLoadPreSave',
            )
        ),
        'plugin' => array(
            // 'Watermark' => array(
            //     'enable'         => false,       // For control by volume driver
            //     'source'         => 'logo.png', // Path to Water mark image
            //     'marginRight'    => 15,          // Margin right pixel
            //     'marginBottom'   => 15,          // Margin bottom pixel
            //     'quality'        => 85,         // JPEG image save quality
            //     'transparency'   => 10,         // Water mark image transparency ( other than PNG )
            //     'targetType'     => IMG_GIF|IMG_JPG|IMG_PNG|IMG_WBMP, // Target image formats ( bit-field )
            //     'targetMinPixel' => 500         // Target image minimum pixel size
            // )
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Root Options
    |--------------------------------------------------------------------------
    |
    | These options are merged, together with every root by default.
    | See https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#root-options
    |
    */
    'root_options' => [

    ],

);
