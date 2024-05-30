<?php
namespace Danir\MediaLib;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Danir\MediaLib\Livewire\Medialibrary\DeleteConfirmation;
use Danir\MediaLib\Livewire\Medialibrary\UploadImageFilament;
use Danir\MediaLib\Livewire\Modalselect\Modal;
use Danir\MediaLib\Livewire\Modalselect\ModalSelect;

class MedialibServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish Livewire components to the app/Livewire directory
        $this->publishes(
            [
                __DIR__.'/Livewire/Medialibrary/DeleteConfirmation.php' => app_path('Livewire/Medialibrary/DeleteConfirmation.php'),
                __DIR__.'/Livewire/Medialibrary/UploadImageFilament.php' => app_path('Livewire/Medialibrary/UploadImageFilament.php'),
                __DIR__.'/Livewire/Modalselect/Modal.php' => app_path('Livewire/Modalselect/Modal.php'),
                __DIR__.'/Livewire/Modalselect/ModalSelect.php' => app_path('Livewire/Modalselect/ModalSelect.php'),
            ], 
            'medialib-livewire-components'
        );

        // Check if the Livewire directory exists, if not create it
        if (!File::exists(app_path('Livewire/Medialibrary'))) {
            File::makeDirectory(app_path('Livewire/Medialibrary'), 0755, true, true);
        }
        if (!File::exists(app_path('Livewire/Modalselect'))) {
            File::makeDirectory(app_path('Livewire/Modalselect'), 0755, true, true);
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

        // Publish migrations
        $this->publishes(
            [
                __DIR__.'/migrations/2024_02_19_121419_create_images_table.php' => database_path('migrations/2024_02_19_121419_create_images_table.php'),
                __DIR__.'/migrations/2024_02_26_121531_create_media_table.php' => database_path('migrations/2024_02_26_121531_create_media_table.php'),
                __DIR__.'/migrations/2024_02_27_114321_create_tags_table.php' => database_path('migrations/2024_02_27_114321_create_tags_table.php'),
                __DIR__.'/migrations/2024_02_27_114405_create_picture_tag_table.php' => database_path('migrations/2024_02_27_114405_create_picture_tag_table.php'),
                __DIR__.'/migrations/2024_03_01_093235_create_upload_image_filaments_table.php' => database_path('migrations/2024_03_01_093235_create_upload_image_filaments_table.php'),
                __DIR__.'/migrations/2024_03_08_100059_create_resized_images_table.php' => database_path('migrations/2024_03_08_100059_create_resized_images_table.php'),
            ],
            'medialib-migrations'
        );

        // Publish views
        $this->publishes(
            [
                __DIR__.'/resources/views' => resource_path('views/vendor/medialib'),
                __DIR__.'/resources/views/layouts' => resource_path('views/layouts'),
                __DIR__.'/resources/views/livewire' => resource_path('views/livewire'),
            ], 'medialib-views'
        );

        // Publish assets
        $this->publishes(
            [
                __DIR__.'/resources' => public_path('vendor/medialib'),
                __DIR__.'/resources/css' => resource_path('css'),
                __DIR__.'/resources/js' => resource_path('js'),
            ], 'medialib-assets'
        );

        // Publish config
        $this->publishes(
            [
                __DIR__.'/config/image_sizes.json' => config_path('image_sizes.json'),
            ], 'medialib-config'
        );

        // Publish routes
        $this->publishes(
            [
                __DIR__.'/routes/medialibweb.php' => base_path('routes/medialibweb.php'),
            ],
            'medialib-routes'
        );

        // Register Livewire components
        Livewire::component('delete-confirmation', DeleteConfirmation::class);
        Livewire::component('upload-image-filament', UploadImageFilament::class);
        Livewire::component('modal', Modal::class);
        Livewire::component('modal-select', ModalSelect::class);
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
