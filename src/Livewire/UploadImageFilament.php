<?php

namespace Danir\MediaLib\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Danir\MediaLib\Models\Picture;
use Danir\MediaLib\Models\ResizedImage;
use Danir\MediaLib\Models\Tag;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;




class UploadImageFilament extends Component
{
    use WithFileUploads, WithPagination;

    public $photo;
    public $tags = [];
    public $fileTitle;
    public $altText;
    public $selectedPictureId;
    public $selectedPictures = [];
    public $selectedPicture = null;
    public $itemToDelete = null;


    protected $listeners = ['refreshComponent' => '$refresh', 'itemDeleted' => 'updateAfterDeletion', 'deleteItemConfirmed' => 'deleteItem'];

    public function save()
    {
        $this->validate(
            [
            'photo' => 'image|max:2048',
            'fileTitle' => 'required|string|max:255',
            ]
        );

        if (!extension_loaded('gd')) {
            $this->dispatch('notifytop', 'GD extension not installed in PHP.');
            return;
        }

        $filenameWithoutExt = Str::slug($this->fileTitle, '-') . '.webp';

        // Maak een WebP-versie van de originele afbeelding
        $tempFile = $this->createWebpImage($this->photo->getRealPath(), 30);
        $compressedSize = filesize($tempFile);

        $picture = $this->createPicture($filenameWithoutExt, $compressedSize);

        // The createBatchDirectory function is called with the id of the picture as an argument
        $batchDirectory = $this->createBatchDirectory($picture->id);

        // De WebP-versie van de originele afbeelding wordt toegevoegd aan de media-collectie
        $picture->addMedia($tempFile)
            ->usingFileName($filenameWithoutExt)
            ->toMediaCollection('images');

        $imageSizes = json_decode(file_get_contents(config_path('image_sizes.json')), true)['sizes'];
        $batchDirectory = $this->createBatchDirectory($picture->id);

        foreach ($imageSizes as $size) {
            // Check if the resized image should be created with crop or without crop
            $createWithCrop = $size['create_with_crop'];

            // Create the resized image with or without Crop accordingly
            if ($createWithCrop) {
                $resizedFilePath = $this->createResizedImage($size, $filenameWithoutExt, $batchDirectory, $this->photo->getRealPath());
            } else {
                $resizedFilePath = $this->createResizedImageWithoutCrop($size, $filenameWithoutExt, $batchDirectory, $this->photo->getRealPath());
            }

            $picture->resizedImages()->create(['path' => $resizedFilePath]);
        }

        if (!empty($this->tags)) {
            $picture->tags()->attach($this->tags);
        }

        // Check if the temporary file exists before attempting to unlink it
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }

        $this->dispatch('notifytop', 'WebP image successfully saved!');
        $this->reset(['photo', 'tags', 'fileTitle']);
    }

    private function createBatchDirectory($id)
    {
        $batchDirectory = storage_path('app/public/' . $id . '/resized_images');
    
        if (!File::isDirectory($batchDirectory)) {
            File::makeDirectory($batchDirectory, 0755, true);
        }
    
        return $batchDirectory;
    }
    
    private function createWebpImage($imagePath, $quality)
    {
        $image = imagecreatefromstring(file_get_contents($imagePath));
        $tempFile = tempnam(sys_get_temp_dir(), 'webp') . '.webp';
        imagewebp($image, $tempFile, $quality);
        return $tempFile;
    }
    
    private function createResizedImage($size, $filenameWithoutExt, $batchDirectory, $imagePath)
    {
        $image = imagecreatefromstring(file_get_contents($imagePath));
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);
        $aspectRatio = $originalWidth / $originalHeight;

        if ($originalWidth > $originalHeight) {
            $newWidth = $size['width'];
            $newHeight = intval($size['width'] / $aspectRatio);
        } else {
            $newWidth = intval($size['height'] * $aspectRatio);
            $newHeight = $size['height'];
        }

        $newImage = imagecreatetruecolor($size['width'], $size['height']);

        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefilledrectangle($newImage, 0, 0, $size['width'], $size['height'], $transparent);

        $xOffset = ($size['width'] - $newWidth) / 2;
        $yOffset = ($size['height'] - $newHeight) / 2;

        imagecopyresampled($newImage, $image, $xOffset, $yOffset, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        $filenameWithoutExt = pathinfo($filenameWithoutExt, PATHINFO_FILENAME);

        $resizedFileName = $filenameWithoutExt . '_' . $size['width'] . 'x' . $size['height'] . '.webp';
        $resizedFilePath = $batchDirectory . '/' . $resizedFileName;

        imagewebp($newImage, $resizedFilePath, 30);
        imagedestroy($newImage);

        return $resizedFilePath;
    }

    private function createResizedImageWithoutCrop($size, $filenameWithoutExt, $batchDirectory, $imagePath)
    {
        $image = imagecreatefromstring(file_get_contents($imagePath));
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);
        $aspectRatio = $originalWidth / $originalHeight;

        if ($originalWidth > $originalHeight) {
            $newWidth = $size['width'];
            $newHeight = intval($size['width'] / $aspectRatio);
        } else {
            $newWidth = intval($size['height'] * $aspectRatio);
            $newHeight = $size['height'];
        }

        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

        $filenameWithoutExt = pathinfo($filenameWithoutExt, PATHINFO_FILENAME);

        $resizedFileName = $filenameWithoutExt . '_' . $size['width'] . 'x' . $size['height'] . '_no_crop.webp';
        $resizedFilePath = $batchDirectory . '/' . $resizedFileName;

        imagewebp($newImage, $resizedFilePath, 30);
        imagedestroy($newImage);

        return $resizedFilePath;
    }
    
    
    private function createPicture($filename, $compressedSize)
    {
        // Gebruik de bestandsnaam zoals ingevuld in de blade
        $filenameWithoutExt = Str::slug($this->fileTitle, '-') . '.' . pathinfo($filename, PATHINFO_EXTENSION);
    
        // Maak en retourneer een nieuw Picture-object
        return Picture::create(
            [
            'fileTitle' => $this->fileTitle,
            'fileName' => $filenameWithoutExt,
            'originalName' => $this->photo->getClientOriginalName(),
            'fileSize' => $compressedSize,
            'compressionRatio' => 0, // Geen compressie aangezien we de originele afbeelding niet opslaan
            'altText' => $this->altText,
            ]
        );
        
    }
    
    public function refreshComponent()
    {
        // Dispatch the $refresh event
        $this->dispatch('$refresh');
    }

    public function updateFileTitle()
    {
        $this->validate(['fileTitle' => 'required|string|max:255']);
    
        if ($this->selectedPicture) {
            $this->selectedPicture->update(['fileTitle' => $this->fileTitle]);
            $this->selectedPicture = $this->selectedPicture->fresh(); // Refresh selected picture
            $this->fileTitle = $this->selectedPicture->fileTitle; // Update fileTitle property
    
            // Dispatch a browser event
            $this->dispatch('notify', 'Title updated successfully!');
        }
    }
    
    public function updateAltText()
    {
        $this->validate(['altText' => 'required|string|max:255']);
    
        if ($this->selectedPicture) {
            $this->selectedPicture->update(['altText' => $this->altText]);
            $this->selectedPicture = $this->selectedPicture->fresh(); // Refresh selected picture
            $this->altText = $this->selectedPicture->altText; // Update altText property
    
            // Dispatch a browser event
            $this->dispatch('notify', 'Alt text updated successfully!');
        }
    }

    public function confirmDeleteSelected()
    {
        // Controleren of er geselecteerde afbeeldingen zijn om te verwijderen
        if (count($this->selectedPictures) > 0) {
            // Dispatch een event om de bevestigingsmodaliteit te openen
            $this->dispatch('showDeleteConfirmationModal');
        } else {
            // Melding weergeven als er geen geselecteerde afbeeldingen zijn
            $this->dispatch('notifytop', 'Selecteer minstens één afbeelding om te verwijderen.');
        }
    }

    public function confirmDeleteSelectedPictures()
    {
        // Controleren of er geselecteerde afbeeldingen zijn om te verwijderen
        if (count($this->selectedPictures) > 0) {
            // Dispatch een event om de bevestigingsmodaliteit te openen
            $this->dispatch('showDeleteConfirmationModal', $this->selectedPictures);
        } else {
            // Melding weergeven als er geen geselecteerde afbeeldingen zijn
            $this->dispatch('notifytop', 'Selecteer minstens één afbeelding om te verwijderen.');
        }
    }
    
    
    
    
    public function confirmDelete($itemId)
    {
        $itemExists = Picture::where('id', $itemId)->exists();
        \Log::info("Item exists: {$itemExists}"); // Log if the item exists

        $this->dispatch('showDeleteModal', $itemId);
    }
    
    
    
    public function deleteItem($itemId)
    {
        // Check if $itemId is an array and extract the ID if necessary
        $pictureId = is_array($itemId) ? $itemId['itemId'] : $itemId;
    
        if ($pictureId) {
            $this->deletePicture($pictureId);
            $this->itemToDelete = null;
        }
    }
    
    
    public function deletePicture($pictureId)
    {
        \Log::info('Picture ID: ' . print_r($pictureId, true)); // Log the value of $pictureId
    
        // Find the picture by its ID
        $picture = Picture::find($pictureId);
    
        // Check if the picture exists
        if ($picture) {
            // Get the directory path associated with the picture
            $directoryPath = 'storage/' . $pictureId;
    
            // Check if the directory exists
            if (Storage::exists($directoryPath)) {
                // Delete the directory and all files inside
                Storage::deleteDirectory($directoryPath);
            }
    
            // Delete the picture from the database
            $picture->delete();
    
            // Notify about successful deletion
            $this->dispatch('notifytop', 'Afbeelding succesvol verwijderd!');
        } else {
            // Notify if the picture is not found
            $this->dispatch('notifytop', 'Afbeelding niet gevonden om te verwijderen.');
        }
    }
    
    
    



    
    
    public function deleteResizedImage($resizedImageId)
    {
        $resizedImage = ResizedImage::find($resizedImageId);
    
        if ($resizedImage) {
            // Delete the resized image file
            if (File::exists($resizedImage->path)) {
                File::delete($resizedImage->path);
            }
            $resizedImage->delete();
    
            $this->dispatch('notifytop', 'Resized image successfully deleted!');
    
            // Refresh the selected picture if it contains the deleted resized image
            if ($this->selectedPicture && $this->selectedPicture->resizedImages()->where('id', $resizedImageId)->exists()) {
                $this->selectedPicture = $this->selectedPicture->fresh();
            }
        }
    }
    
    public function deleteSelectedPictures()
    {
        // Check if there are selected pictures to delete
        if (count($this->selectedPictures) > 0) {
            // Loop through each selected picture and delete it
            foreach ($this->selectedPictures as $pictureId) {
                $this->deletePicture($pictureId);
            }
            // Clear the selectedPictures array
            $this->selectedPictures = [];
            // Close the delete confirmation modal if it's open
            $this->dispatch('closeDeleteConfirmationModal');
        } else {
            // Notify the user if no pictures are selected
            $this->dispatch('notifytop', 'Selecteer minstens één afbeelding om te verwijderen.');
        }
    }
    

    public function generateMissingResizedImages()
    {
        // Load image sizes from configuration file
        $imageSizes = json_decode(file_get_contents(config_path('image_sizes.json')), true)['sizes'];

        // Fetch all pictures
        $pictures = Picture::all();

        foreach ($pictures as $picture) {
            // Get the path of the original image
            $originalImagePath = $picture->getFirstMedia('images')->getPath();
            // Extract the directory and filename information from the path
            $originalDirectory = pathinfo($originalImagePath, PATHINFO_DIRNAME);
            $originalFilename = pathinfo($originalImagePath, PATHINFO_FILENAME);

            // Create the batch directory if it doesn't exist
            $batchDirectory = $this->createBatchDirectory($picture->id);

            // Iterate over each image size from the configuration file
            foreach ($imageSizes as $size) {
                $width = $size['width'];
                $height = $size['height'];
                $createWithCrop = $size['create_with_crop']; // New flag

                // Check if the resized image exists
                $resizedFileName = $originalFilename . '_' . $width . 'x' . $height . ($createWithCrop ? '' : '_no_crop') . '.webp';
                $resizedFilePath = $batchDirectory . '/' . $resizedFileName;

                if (!File::exists($resizedFilePath)) {
                    // Create the resized image if it doesn't exist
                    if ($createWithCrop) {
                        $resizedFilePath = $this->createResizedImage($size, $originalFilename, $batchDirectory, $originalImagePath);
                    } else {
                        $resizedFilePath = $this->createResizedImageWithoutCrop($size, $originalFilename, $batchDirectory, $originalImagePath);
                    }

                    // Create a record in the resized_images table with picture_id
                    $resizedImage = new ResizedImage(
                        [
                        'picture_id' => $picture->id,
                        'path' => $resizedFilePath,
                        ]
                    );
                    $picture->resizedImages()->save($resizedImage);
                }
            }
        }

        // Notify successful generation of resized images
        $this->dispatch('notifytop', 'Resized images successfully generated and associated with pictures!');
    }

    public function generateMissingResizedImagesForSelected()
    {
        $imageSizes = json_decode(file_get_contents(config_path('image_sizes.json')), true)['sizes'];

        $picture = $this->selectedPicture;
        $originalImagePath = $picture->getFirstMedia('images')->getPath();
        $originalDirectory = pathinfo($originalImagePath, PATHINFO_DIRNAME);
        $originalFilename = pathinfo($originalImagePath, PATHINFO_FILENAME);

        $batchDirectory = $this->createBatchDirectory($picture->id);

        foreach ($imageSizes as $size) {
            $width = $size['width'];
            $height = $size['height'];
            $createWithCrop = $size['create_with_crop']; // New flag

            $resizedFileName = $originalFilename . '_' . $width . 'x' . $height . ($createWithCrop ? '' : '_no_crop') . '.webp';
            $resizedFilePath = $batchDirectory . '/' . $resizedFileName;

            if (!File::exists($resizedFilePath)) {
                if ($createWithCrop) {
                    $resizedFilePath = $this->createResizedImage($size, $originalFilename, $batchDirectory, $originalImagePath);
                } else {
                    $resizedFilePath = $this->createResizedImageWithoutCrop($size, $originalFilename, $batchDirectory, $originalImagePath);
                }

                $resizedImage = new ResizedImage(
                    [
                    'picture_id' => $picture->id,
                    'path' => $resizedFilePath,
                    ]
                );
                $picture->resizedImages()->save($resizedImage);
            }
        }

        $this->dispatch('notifytop', 'Resized images successfully generated and associated with the selected picture!');
    }

    
    public function toggleAddTags($pictureId)
    {
        if ($this->selectedPictureId === $pictureId) {
            $this->selectedPictureId = null;
            $this->tags = [];
            
            
        } else {
            $this->selectedPictureId = $pictureId;
            $this->tags = Picture::findOrFail($pictureId)->tags->pluck('id')->toArray();
        }
    }    
    
    public function saveTags()
    {
        if ($this->selectedPictureId) {
            $picture = Picture::find($this->selectedPictureId);
            if ($picture) {
                $picture->tags()->syncWithoutDetaching($this->tags);
                $this->dispatch('notify', 'Tags zijn toegevoegd!');
            } else {
                $this->dispatch('notify', 'Geen afbeelding gevonden!');
            }
            $this->selectedPictureId = null;
            $this->tags = []; // Reset the tags array
        } else {
            $this->dispatch('notify', 'Geen afbeelding gevonden!');
        }
    }
    
    
    public function removeTag($pictureId, $tagId)
    {
        $picture = Picture::find($pictureId);
        if ($picture) {
            $picture->tags()->detach($tagId);
            // Refresh the selected picture to reflect the updated tags
            $this->selectedPicture = $picture->fresh();
            // Update the tags array
            $this->tags = $this->selectedPicture->tags->pluck('id')->toArray();
        }
    }


    public function cancel()
    {
        $this->selectedPicture = null;
    }
 
    public function selectPicture($pictureId)
    {
        $this->selectedPicture = Picture::with('tags')->findOrFail($pictureId);
        $this->selectedPictureId = $pictureId;
        $this->fileTitle = $this->selectedPicture->fileTitle; 
        $this->altText = $this->selectedPicture->altText;
        $this->tags = $this->selectedPicture->tags->pluck('id')->toArray();
    
        // Dispatch an event to force a component re-render
        $this->dispatch('refreshComponent');
    }
    

    // Voeg deze methode toe aan je component
    public function closeSelectedPictureDetails()
    {
        $this->selectedPicture = null;
        $this->selectedPictureId = null;
    }

    public function render()
    {
        $pictures = Picture::with('tags')->paginate(30);
        $allTags = Tag::all();

        // Controleer of de geselecteerde afbeelding bestaat voordat je deze toont in de detailssectie
        if ($this->selectedPicture && !$pictures->contains($this->selectedPicture)) {
            $this->closeSelectedPictureDetails();
        }

        return view(
            'livewire.upload-image-filament', [
            'pictures' => $pictures,
            'allTags' => $allTags,
            ]
        )->layout('layouts.appzonder');
    }
}
