<div class="flex p-4 bg-gray-600 shadow-md min-h-screen">
    <div class="{{ $selectedPicture ? 'w-2/3' : 'w-full' }} space-y-4 pb-5">

        <div x-data="{ open: false }" class="shadow-md p-8 rounded-md bg-white">
            <button x-show="!open" x-on:click="open = true"
                class="w-full py-2 px-4 bg-blue-500 hover:bg-blue-700 text-white rounded-md mt-4">
                Afbeelding toevoegen
            </button>

            <form x-show="open" wire:submit.prevent="save" class="space-y-4 pb-5">
                @csrf
                <input type="file" wire:model="photo" class="w-full py-2 px-3 border rounded-md">



                @error('photo')
                    <span class="error text-red-500">{{ $message }}</span>
                @enderror

                <!-- Tags Input -->
                <div class="flex flex-wrap">
                    @foreach ($allTags as $tag)
                        <div class="flex items-center space-x-2 mb-2 wire:key="tag_{{ $tag->id }}"">
                            <input type="checkbox" wire:model="tags" value="{{ $tag->id }}"
                                class="form-checkbox h-5 w-5 text-blue-600">
                            <label>{{ $tag->name }}</label>
                        </div>
                    @endforeach
                </div>

                <!-- Title Input -->
                <div class="mb-2 ">Title</div>
                <input type="text" wire:model="fileTitle" class="w-full py-2 px-3 border rounded-md">

                <!-- Alt Text Input -->
                <div class="mb-2 ">Alt text</div>
                <input type="text" wire:model="altText" class="w-full py-2 px-3 border rounded-md">

                <button type="submit" class="w-full py-2 px-4 bg-blue-500 hover:bg-blue-700 text-white rounded-md mt-4"
                    wire:loading.attr="disabled">
                    Foto opslaan
                </button>

                <button type="button" x-on:click="open = false"
                    class="w-full py-2 px-4 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md mt-4">
                    Annuleren
                </button>

                <span wire:loading wire:target="photo">Uploading...</span>
                <div x-data="{ open: false, message: '' }"
                    x-on:notifytop.window="message = $event.detail; open = true; setTimeout(() => open = false, 4000)"
                    x-show="open" class=" bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <p x-text="message"></p>
                </div>
            </form>
        </div>

        <button wire:click="generateMissingResizedImages"
            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:border-blue-300">Genereer
            missenden resized afbeeldingen voor alles</button>
        <!-- Delete Selected Button -->
        <form wire:submit.prevent="deleteSelectedPictures">
            @csrf
            <button type="submit"
                class="bg-red-500 text-white px-4 py-2 rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-opacity-50">
                Delete Selected
            </button>
        </form>
        @livewire('delete-confirmation')

        <!--ingeladeimages-->
        <div class="flex flex-wrap gap-4">
            @foreach ($pictures as $picture)
                <div class="relative overflow-hidden shadow-lg rounded-lg bg-white h-36 w-36"
                    wire:key="picture_{{ $picture->id }}">

                    <!-- Checkbox -->
                    <input type="checkbox" wire:model="selectedPictures" value="{{ $picture->id }}"
                        class="absolute top-2 right-2 z-10">

                    <img src="{{ $picture->image_url }}" alt="{{ $picture->fileTitle }}"
                        class="w-full h-full rounded-md object-cover cursor-pointer"
                        wire:click="selectPicture({{ $picture->id }})">

                </div>
            @endforeach
        </div>


        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $pictures->links() }}
        </div>
    </div>

    <!-- DETAILS -->

    @if ($selectedPicture)
        <div class="w-1/3 ml-4 rounded-md bg-white p-6" wire:key="selectedPicture_{{ $selectedPicture->id }}">

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold mb-2">{{ $selectedPicture->fileTitle }}</h2>
                <button wire:click="cancel"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-3 rounded-full text-xs">
                    X
                </button>
            </div>

            <div class="w-full h-64 overflow-hidden relative">
                <img src="{{ $selectedPicture->image_url }}" alt="{{ $selectedPicture->fileTitle }}"
                    class="w-full h-full object-contain absolute top-0 left-0 right-0 bottom-0 m-auto">
            </div>

            <h2 class="text-lg font-semibold mb-2">Tags</h2>
            <div class="flex flex-wrap mb-4">
                @forelse ($selectedPicture->tags as $tag)
                    <div class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full mr-2 mb-2"
                        wire:key="selectedPictureTag_{{ $tag->id }}">
                        <span>{{ $tag->name }}</span>
                        <!-- Button to remove tag -->
                        <button wire:click="removeTag({{ $selectedPicture->id }}, {{ $tag->id }})"
                            class="ml-1 focus:outline-none">

                        </button>
                    </div>
                @empty
                    <p>No tags associated with this picture.</p>
                @endforelse
            </div>

            <!-- Form for adding tags -->
            <form wire:submit.prevent="saveTags" class="mb-4">
                @csrf
                <div class="mb-4">
                    <label for="tag" class="block mb-2">Select Tags:</label>
                    <select wire:model="tags" multiple id="tag"
                        class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300">
                        @foreach ($allTags as $tag)
                            @unless ($selectedPicture->tags->contains($tag))
                                <option value="{{ $tag->id }}"wire:key="tagOption_{{ $tag->id }}">
                                    {{ $tag->name }}</option>
                            @endunless
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:border-blue-300">
                    Tags opslaan
                </button>
            </form>

            <div class="mb-4">
                <div class="font-bold">
                    Titel:
                </div>
                @if ($selectedPicture)
                    <input type="text" wire:model.defer="fileTitle" class="w-full py-2 px-3 border rounded-md">
                @endif
                <button wire:click="updateFileTitle"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:border-blue-300 mt-2">Wijzig</button>
            </div>

            <div class="mb-4">
                <div class="font-bold">
                    Alt Text:
                </div>
                @if ($selectedPicture)
                    <input type="text" wire:model.defer="altText" class="w-full py-2 px-3 border rounded-md mt-2">
                @endif
                <button
                    wire:click="updateAltText"class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:border-blue-300 mt-2">Wijzig</button>
            </div>



            <h2 class="text-lg font-semibold mb-2">Resized Images</h2>
            <div class="flex flex-wrap mb-4">
                @forelse ($selectedPicture->resizedImages as $resizedImage)
                    <div class="flex items-center bg-gray-200 text-gray-800 px-2 py-1 rounded-full mr-2 mb-2"
                        wire:key="selectedPictureResizedImage_{{ $resizedImage->id }}">
                        <span>{{ $resizedImage->path }}</span>
                        <button class="ml-2 text-red-600 hover:text-red-800"
                            wire:click="deleteResizedImage({{ $resizedImage->id }})">
                            X
                        </button>
                    </div>
                @empty
                    <p>Geen resized afbeeldingen gevonden</p>
                @endforelse
                <button wire:click="generateMissingResizedImagesForSelected"
                    class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 focus:outline-none focus:ring focus:border-blue-300 ">Genereer
                    missenden resized afbeeldingen</button>
            </div>





            <div>
                <p class="mb-2">File Size:
                    @if ($selectedPicture->fileSize < 1024 * 1024)
                        {{ round($selectedPicture->fileSize / 1024, 1) }} KB
                    @else
                        {{ round($selectedPicture->fileSize / 1024 / 1024, 1) }} MB
                    @endif
                </p>
            </div>

            <div x-data="{ open: false, message: '' }"
                x-on:notify.window="message = $event.detail; open = true; setTimeout(() => open = false, 5000)"
                x-show="open"
                class="fixed bottom-5 right-5 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                <p x-text="message"></p>
            </div>

            <div class="flex space-x-4 mt-4">
                <button wire:click="cancel"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-l">
                    Cancel
                </button>
                <button wire:click="confirmDelete({{ $selectedPicture->id }})"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-r">
                    Delete
                </button>
            </div>



        </div>

    @endif

</div>
