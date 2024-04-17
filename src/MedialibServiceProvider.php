<?php
namespace Danir\MediaLib;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Danir\MediaLib\Livewire\DeleteConfirmation;
use Danir\MediaLib\Livewire\UploadImageFilament;


class MedialibServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'medialib');

        // // Publish Livewire components to the app/Livewire directory
        // $this->publishes(
        //     [
        //         __DIR__.'/Livewire/DeleteConfirmation.php' => app_path('Livewire/DeleteConfirmation.php'),
        //         __DIR__.'/Livewire/UploadImageFilament.php' => app_path('Livewire/UploadImageFilament.php'),
        //     ], 
        //     'medialib-livewire-components'
        // );
        
        // Check if the Livewire directory exists, if not create it
        if (!File::exists(app_path('Livewire'))) {
            File::makeDirectory(app_path('Livewire'), 0755, true);
        }

        // Publish models
        $this->publishes(
            [
                __DIR__.'/Models/Picture.php' => app_path('Models/Picture.php'),
                __DIR__.'/Models/ResizedImage.php' => app_path('Models/ResizedImage.php'),
                __DIR__.'/Models/Tag.php' => app_path('Models/Tag.php'),
            ],
            'medialib-models'
        );

        // // Publish migrations
        // $this->publishes(
        //     [
        //         __DIR__.'/migrations/2024_02_19_121419_create_images_table.php' => database_path('migrations/2024_02_19_121419_create_images_table.php'),
        //         __DIR__.'/migrations/2024_02_26_121531_create_media_table.php' => database_path('migrations/2024_02_26_121531_create_media_table.php'),
        //         __DIR__.'/migrations/2024_02_27_114321_create_tags_table.php' => database_path('migrations/2024_02_27_114321_create_tags_table.php'),
        //         __DIR__.'/migrations/2024_02_27_114405_create_picture_tag_table.php' => database_path('migrations/2024_02_27_114405_create_picture_tag_table.php'),
        //         __DIR__.'/migrations/2024_03_01_093235_create_upload_image_filaments_table.php' => database_path('migrations/2024_03_01_093235_create_upload_image_filaments_table.php'),
        //         __DIR__.'/migrations/2024_03_08_100059_create_resized_images_table.php' => database_path('migrations/2024_03_08_100059_create_resized_images_table.php'),
        //     ],
        //     'medialib-migrations'
        // );


        // Publish views
        $this->publishes(
            [
            __DIR__.'/resources/views' => resource_path('views/vendor/medialib'),
            __DIR__.'/resources/views/layouts' => resource_path('views/layouts'),
            __DIR__.'/resources/views/livewire' => resource_path('views/livewire'),
            ], 'medialib-views'
        );

        // // Publish assets
        // $this->publishes(
        //     [
        //     __DIR__.'/resources' => public_path('vendor/medialib'),
        //     __DIR__.'/resources/css' => resource_path('css'),
        //     __DIR__.'/resources/js' => resource_path('js'),
        //     ], 'medialib-assets'
        // );

        // Publish config
        $this->publishes(
            [
            __DIR__.'/config/image_sizes.json' => config_path('image_sizes.json'),
            ], 'medialib-config'
        );

        // // Load routes
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Register Livewire components
        Livewire::component('delete-confirmation', DeleteConfirmation::class);
        Livewire::component('upload-image-filament', UploadImageFilament::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // No configuration file to merge
    }
}
