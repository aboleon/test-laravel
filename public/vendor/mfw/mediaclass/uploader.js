/*jshint esversion: 11 */

/**
 * depends on /vendor/mfw/js/commons.js
 */

const MediaclassUploader = {
    // Cache common jQuery selectors
    template() {
        return $('#mediaclass-file-upload');
    },
    uploadable(selector) {
        return selector.closest('.mediaclass-uploadable');
    },
    uploadableContainer(selector) {
        return this.uploadable(selector).find('.mediaclass-upload-container').first();
    },
    fileupload(uploadContainer) {
        return uploadContainer.find('.mediaclass-fileupload').first();
    },
    messages() {
        return $('.mediaclass-messages');
    },
    progress() {
        return $('.mediaclass-progress');
    },
    deleteCropForm() {
        return $('#mediaclass-delete-crop-form');
    },
    confirmDeleteModal() {
        return $('#mediaclass-confirm-delete');
    },
    confirmDeleteBtn() {
        return $('#confirm-delete-btn');
    },
    alerts() {
        return $('.mediaclass-alerts');
    },
    // Constants
    defaultFileSize: 16000000,
    positions_tags: ['left', 'up', 'down', 'right'],

    // Helper methods
    calculateMaxFileSize(size) {
        if (!size || (!size.includes('KB') && !size.includes('MB'))) {
            return this.defaultFileSize;
        }

        const value = Number(size.replace(/\D+/g, ''));

        if (size.includes('KB')) {
            return value * 1024;
        }
        if (size.includes('MB')) {
            return value * 1024 * 1024;
        }

        return this.defaultFileSize;
    },

    // Check if uploader limit has been reached
    isLimitReached(uploadable) {
        const limit = Number(uploadable.data('limit'));
        if (limit <= 0) {
            return false; // No limit defined
        }

        const currentCount = uploadable.find('.uploaded > div.mediaclass.unlinkable').length;
        return currentCount >= limit;
    },

    // Delete media
    unlinkable() {
        // Use event delegation to avoid re-binding issues
        $(document).off('click.unlink').on('click.unlink', '.unlink', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const $unlinkBtn = $(this);
            const selector = $unlinkBtn.closest('.unlinkable');
            const uploadable = MediaclassUploader.uploadable($unlinkBtn);
            const container = selector.closest('.uploaded');

            // Store the delete data for use in the modal
            const deleteData = {
                selector: selector,
                container: container,
                uploadable: uploadable,
                formData: `action=delete&id=${selector.attr('data-id')}&model=${uploadable.attr('data-model')}`
            };

            // Show the confirmation modal
            MediaclassUploader.confirmDeleteModal().modal('show');

            // Handle confirm button click
            MediaclassUploader.confirmDeleteBtn().off('click').on('click', function () {
                // Hide the modal first
                MediaclassUploader.confirmDeleteModal().modal('hide');

                // Perform the deletion
                ajax(deleteData.formData, MediaclassUploader.template());

                $(document).off('ajaxSuccess.mediaclassDelete').on('ajaxSuccess.mediaclassDelete', function () {
                    deleteData.selector.remove();

                    if (deleteData.container.find('.unlinkable').length < 1) {
                        // Find the alerts container specific to this uploadable
                        const alertsContainer = deleteData.uploadable.find('.mediaclass-alerts');
                        alertsContainer.html(`<div class="alert alert-info">${alertsContainer.data('msg')}</div>`);
                    }

                    // Re-enable uploader button if we're now below the limit
                    if (!MediaclassUploader.isLimitReached(deleteData.uploadable)) {
                        deleteData.uploadable.find('span.mediaclass-uploader').removeClass('disabled');
                    }

                    // Remove this specific success handler
                    $(document).off('ajaxSuccess.mediaclassDelete');
                });
            });
        });
    },

    uploaderCall() {
        $('span.mediaclass-uploader').off().on('click', function () {
            const instantiator = $(this).closest('.mediaclass-uploadable');
            const uploadContainer = MediaclassUploader.uploadableContainer($(this));

            // Check if we've reached the upload limit
            if (MediaclassUploader.isLimitReached(instantiator)) {
                // Optional: Show a message that the limit has been reached
                const limit = Number(instantiator.data('limit'));
                MediaclassUploader.alerts().html(`<div class="alert alert-warning">Limite de ${limit} fichier(s) atteinte</div>`);
                return; // Don't show the uploader
            }

            if (uploadContainer.find('.fileupload-container').length < 1) {
                uploadContainer.html(MediaclassUploader.template().html())
                    .attr('data-description', instantiator.data('description'));

                MediaclassUploader.initFileupload(uploadContainer);
                MediaclassUploader.uploaderOptions(uploadContainer);
            } else {
                uploadContainer.html('');
            }
        });

        // Immediately disable uploader buttons where limit is already reached
        $('.mediaclass-uploadable').each(function () {
            const $this = $(this);
            if (MediaclassUploader.isLimitReached($this)) {
                $this.find('span.mediaclass-uploader').addClass('disabled');
            }
        });
    },// Add this to the uploaderCall() function after uploadContainer.html(...) line:
    uploaderCall() {
        $('span.mediaclass-uploader').off().on('click', function () {
            const instantiator = $(this).closest('.mediaclass-uploadable');
            const uploadContainer = MediaclassUploader.uploadableContainer($(this));

            // Check if we've reached the upload limit
            if (MediaclassUploader.isLimitReached(instantiator)) {
                // Optional: Show a message that the limit has been reached
                const limit = Number(instantiator.data('limit'));
                MediaclassUploader.alerts().html(`<div class="alert alert-warning">Limite de ${limit} fichier(s) atteinte</div>`);
                return; // Don't show the uploader
            }

            if (uploadContainer.find('.fileupload-container').length < 1) {
                uploadContainer.html(MediaclassUploader.template().html())
                    .attr('data-description', instantiator.data('description'));

                // Add dimension hint if requirements exist
                const requiredWidth = instantiator.data('required-width');
                const requiredHeight = instantiator.data('required-height');

                if (requiredWidth && requiredHeight) {
                    const fileuploadBar = uploadContainer.find('.fileupload-buttonbar');
                    const dimensionHint = `<div class="dimension-requirements text-center mb-3">
            <i class="bi bi-info-circle"></i>
            <strong>Dimensions requises :</strong> ${requiredWidth} × ${requiredHeight} px minimum
          </div>`;
                    fileuploadBar.prepend(dimensionHint);
                }

                MediaclassUploader.initFileupload(uploadContainer);
                MediaclassUploader.uploaderOptions(uploadContainer);
            } else {
                uploadContainer.html('');
            }
        });

        // Immediately disable uploader buttons where limit is already reached
        $('.mediaclass-uploadable').each(function () {
            const $this = $(this);
            if (MediaclassUploader.isLimitReached($this)) {
                $this.find('span.mediaclass-uploader').addClass('disabled');
            }
        });
    },

// Also update the uploaderOptions function to pass dimension requirements:
    uploaderOptions(uploadContainer) {
        const fileuploadContainer = this.fileupload(uploadContainer);
        const uploadable = this.uploadable(uploadContainer);
        const limit = Number(uploadable.data('limit'));
        const inputFileSize = uploadable.data('maxfilesize');
        const maxFileSize = this.calculateMaxFileSize(inputFileSize);
        const messagesUI = uploadable.find('.ui-messages');

        // Get dimensions from data attributes
        const requiredWidth = uploadable.data('required-width');
        const requiredHeight = uploadable.data('required-height');

        const options = {
            previewMaxWidth: 220,
            previewMaxHeight: 220,
            acceptFileTypes: /(\.|\/)(jpe?g|png|svg|pdf)$/i,
            maxFileSize: maxFileSize,
            autoUpload: false,
            maxNumberOfFiles: limit > 0 ? limit : null,
            messages: {
                maxNumberOfFiles: `${messagesUI.find('.maxNumberOfFiles').first().text()} ${limit}`,
                acceptFileTypes: 'Type de fichier non autorisé',
                maxFileSize: `${messagesUI.find('.maxFileSize').first().text()} ${inputFileSize || ((this.defaultFileSize / 1024 / 1024) + 'MB')}`,
            },
        };

        // Add dimension validation messages if requirements exist
        if (requiredWidth && requiredHeight) {
            options.messages.minImageWidth = `Largeur minimale requise : ${requiredWidth}px`;
            options.messages.minImageHeight = `Hauteur minimale requise : ${requiredHeight}px`;
            options.messages.imageDimensions = `Dimensions minimales requises : ${requiredWidth} × ${requiredHeight} px`;
        }

        fileuploadContainer.fileupload('option', options);
    },

    positions(uploadable) {
        uploadable.find('.positions i').off().on('click', function () {
            const $this = $(this);
            const positionsContainer = $this.closest('.positions');

            positionsContainer.find('i').removeClass('active');
            $this.addClass('active');
            positionsContainer.find('input').val($this.data('position'));
        });
    },

    initFileupload(uploadContainer) {
        const fileuploadContainer = this.fileupload(uploadContainer);
        const uploadable = this.uploadable(fileuploadContainer);
        const hideDescription = Number(uploadable.attr('data-has-description')) !== 1;

        // Only destroy existing fileupload instance if it exists to prevent conflicts
        if (fileuploadContainer.data('blueimp-fileupload') || fileuploadContainer.data('fileupload')) {
            fileuploadContainer.fileupload('destroy');
        }

        // Your original event handler for fileuploadadd
        fileuploadContainer.off('fileuploadadd fileuploadsubmit');

        fileuploadContainer.on('fileuploadadd', function () {
            fileuploadContainer.find('.uploadables').removeClass('d-none');

            setTimeout(() => {
                if (uploadable.data('positions') !== 1) {
                    uploadable.find('.positions').addClass('d-none');
                }
                if (hideDescription) {
                    uploadable.find('.description').addClass('d-none');
                }
                MediaclassUploader.positions(uploadable);
            }, 1);
        }).fileupload({
            url: MediaclassUploader.template().data('ajax'),
            dataType: 'json',
            context: fileuploadContainer[0],
            sequentialUploads: true,
            type: 'POST',
            done: () => {
                MediaclassUploader.progress().hide();
            },
            success: (data) => {
                MediaclassUploader.alerts().html('');

                // Check for errors FIRST before doing anything else
                if (data.hasOwnProperty('errors') || data.hasOwnProperty('error')) {
                    const errorData = data.mfw_ajax_messages ?? data.messages;
                    notificator(200, errorData, MediaclassUploader.messages(), false, {isDismissable: true});

                    uploadable.find('.files .template-upload').fadeOut(function() {
                        $(this).remove();
                        if (uploadable.find('.files .template-upload').length === 0) {
                            uploadable.find('.uploadables').addClass('d-none');
                        }
                    });
                    return;
                }

                if (!data.uploaded) {
                    console.error('No uploaded data in response', data);
                    notificator('Erreur lors du téléchargement', 'danger', MediaclassUploader.messages());
                    return;
                }

                // Hide the upload queue first, then add the new content after it's hidden
                uploadable.find('.files').fadeOut(300, function () {
                    $(this).html('').show();

                    // Now that the upload queue is cleared, add the new content
                    const html = MediaclassUploader.buildUploadedFileHTML(data, hideDescription);

                    // Find or create the lightgallery container
                    let lightGalleryContainer = uploadable.find('.lightgallery-container');
                    if (lightGalleryContainer.length === 0) {
                        uploadable.find('.uploaded').wrapInner(`<div id="lightgallery-${uploadable.data('group')}-${uploadable.data('model-id')}" class="lightgallery-container"></div>`);
                        lightGalleryContainer = uploadable.find('.lightgallery-container');
                    }

                    // Add the new content to the lightgallery container
                    lightGalleryContainer.append(html);

                    // Initialize events
                    MediaclassUploader.unlinkable();

                    // Initialize LightGallery for this specific container
                    setTimeout(() => {
                        // Destroy existing instance if any
                        const lgInstance = lightGalleryContainer.data('lightGallery');
                        if (lgInstance) {
                            lgInstance.destroy();
                        }

                        // Only initialize if there are image items
                        const imageItems = lightGalleryContainer.find('.lightgallery-item');
                        if (imageItems.length > 0) {
                            lightGallery(lightGalleryContainer[0], {
                                selector: '.lightgallery-item',
                                speed: 500,
                                download: true,
                                counter: true,
                                zoom: true,
                                thumbnail: imageItems.length > 1,
                                plugins: [lgZoom, lgThumbnail],
                                mobileSettings: {
                                    controls: true,
                                    showCloseIcon: true,
                                    download: true
                                }
                            });
                        }
                    }, 100);

                    // Check limits and close uploader if needed
                    if (MediaclassUploader.isLimitReached(uploadable)) {
                        uploadable.find('span.mediaclass-uploader').addClass('disabled');
                        MediaclassUploader.uploadableContainer(uploadable).html('');
                    } else if (uploadable.find('.uploaded > div.mediaclass.unlinkable').length === Number(data.count_files)) {
                        MediaclassUploader.uploadableContainer(uploadable).html('');
                    }

                    MediaclassUploader.modalCrop();
                });
            },
            error: (xhr, ajaxOptions, thrownError) => {
                console.error('Upload error:', xhr, thrownError);

                // Check if it's a dimension error from the response
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    // Format the error for notificator
                    const errorData = {
                        danger: [xhr.responseJSON.errors]
                    };
                    notificator(200, errorData, MediaclassUploader.messages(), false, {isDismissable: true});
                } else {
                    // Generic error message
                    const errorData = {
                        danger: ['Une erreur est survenue lors du téléchargement de votre fichier']
                    };
                    notificator(200, errorData, MediaclassUploader.messages(), false, {isDismissable: true});
                }

                // Clean up the upload UI
                uploadable.find('.files .template-upload').fadeOut(function () {
                    $(this).remove();

                    // If no more files in queue, hide the uploadables section
                    if (uploadable.find('.files .template-upload').length === 0) {
                        uploadable.find('.uploadables').addClass('d-none');
                    }
                });
            },
            start: () => {
                MediaclassUploader.messages().html('');
                MediaclassUploader.progress().show();
            },
        });

        fileuploadContainer.bind('fileuploadsubmit', (e, data) => {
            MediaclassUploader.messages().html('');

            // Count valid files
            let validFiles = 0;
            uploadable.find('.files > div').each(function () {
                if ($(this).find('.error').first().text().length < 1) {
                    validFiles += 1;
                }
            });

            // Get cropable data
            let cropableData = uploadable.data('cropable');

            // If it's already a string (JSON), use it as is
            // If it's an object, stringify it
            if (typeof cropableData === 'object' && cropableData !== null) {
                cropableData = JSON.stringify(cropableData);
            }

            // Set form data
            data.formData = [
                {name: '_token', value: token()},
                {name: 'action', value: 'upload'},
                {name: 'group', value: uploadable.data('group')},
                {name: 'subgroup', value: uploadable.data('subgroup')},
                {name: 'positions', value: uploadable.data('positions')},
                {name: 'model', value: uploadable.data('model')},
                {name: 'model_id', value: uploadable.data('model-id')},
                {name: 'mediaclass_temp_id', value: $('input[name="mediaclass_temp_id"]').first().val() ?? ''},
                {name: 'count_files', value: validFiles},
                {name: 'ghost', value: uploadable.data('ghost') || '0'},
                {name: 'cropable', value: cropableData || ''}
            ];

            // Add form fields
            data.context.find('textarea, input').each(function () {
                data.formData.push({
                    name: $(this).attr('name'),
                    value: $(this).val()
                });
            });
        });
    },

    buildUploadedFileHTML(data, hideDescription) {
        const {uploaded, filetype, preview, link, cropable_links, has_positions} = data;

        // For images, we need to get the full size URL for LightGallery
        const fullSizeUrl = filetype === 'image' ? (data.urls && data.urls.xl ? data.urls.xl : link) : link;

        let html = `
<div class="mediaclass unlinkable uploaded-image my-2" data-id="${uploaded.id}" id="mediaclass-${uploaded.id}">
    <span class="unlink"><i class="bi bi-x-circle-fill"></i></span>
    <div class="row m-0">
        <div class="col-xl-3 pe-xl-4 col-12 impImg position-relative preview ${filetype}">
            <div class="w-100 h-100" style="background-image: url(${preview}); background-size: contain;background-repeat: no-repeat;background-position: center;">
                <div class="actions">`;

        if (filetype === 'image') {
            // For images, add the lightgallery-item class and data-sub-html
            html += `
                    <a href="${fullSizeUrl}"
                       class="lightgallery-item zoom"
                       data-sub-html="<h4>${uploaded.original_filename}</h4><p>${uploaded.description ? (uploaded.description[document.documentElement.lang] || '') : ''}</p>">
                        <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                    </a>`;
        } else {
            // For non-images, just open in new tab
            html += `
                    <a target="_blank" href="${link}" class="zoom">
                        <i class="fa-sharp fa-solid fa-magnifying-glass"></i>
                    </a>`;
        }

        html += `
                </div>
            </div>
        </div>
        <div class="col-xl-9 col-12 impFileName">
            <div class="row infos">
                <div class="col-sm-12">
                    <p class="name">
                        <span class="rounded-1 py-1 px-2 text-bg-secondary">${uploaded.original_filename}</span>
                        <span class="rounded-1 py-1 px-2 bg-light-subtle text-dark opacity-75">
                            Uploadé le ${new Date(uploaded.created_at).toLocaleDateString('fr-FR')} à ${new Date(uploaded.created_at).toLocaleTimeString('fr-FR', {
            hour: '2-digit',
            minute: '2-digit'
        })}
                        </span>
                    </p>
                </div>
            </div>
            ${filetype === 'image' && cropable_links ? cropable_links : ''}
            <div class="row params mt-3">
                <div class="col-12 positions text-center ps-2${has_positions === true ? '' : ' d-none'}">
                    <b>Positions par rapport au contenu</b>
                    <div class="choices pt-2">`;

        // Add position buttons
        for (const position of this.positions_tags) {
            const isActive = uploaded.position === position ? ' active' : '';
            html += `<i class="bi bi-arrow-${position}-square-fill${isActive}" data-position="${position}"></i>`;
        }

        html += `
                        <input type="hidden" name="mediaclass[${uploaded.id}][position]" value="${uploaded.position || 'left'}">
                    </div>
                </div>`;

        // Add descriptions
        const descriptions = uploaded.description || {};
        for (const [key, value] of Object.entries(descriptions)) {
            html += `
                <div class="col-lg-6 col-12 description ${hideDescription ? ' d-none' : ''}">
                    <div class="mt-2">
                        <label class="form-label">Description (${key})</label>
                        <textarea name="mediaclass[${uploaded.id}][description][${key}]"
                                class="form-control description"
                                rows="3">${value || ''}</textarea>
                    </div>
                </div>`;
        }

        html += `
            </div>
        </div>
    </div>
</div>`;

        return html;
    },

    modalCrop() {
        const $modal = $('#mediaclass-crop');
        const ajaxUrl = this.template().data('ajax');

        // Clean up any existing event handlers
        $modal.off('shown.bs.modal');
        $modal.off('hidden.bs.modal');
        $(document).off('ajaxSuccess.mediaclassCrop');

        // Handle crop button clicks
        $(document).on('click', '.crop-actions-bar .crop', function (e) {
            e.preventDefault();
            const $btn = $(this);
            const isView = $btn.hasClass('cropped');

            // Clear previous modal content
            $modal.find('.modal-body').empty();

            if (isView) {
                // Clone the template content
                const template = $('#mediaclass-crop-view-template').html();
                $modal.find('.modal-body').html(template);

                // Set timeout to ensure DOM is ready
                setTimeout(() => {
                    // Populate modal content
                    const cropLabel = $btn.data('crop-label') || $btn.data('crop-key');

                    $modal.find('.crop-key-title').text(cropLabel);
                    $modal.find('.crop-key-label').text($btn.data('crop-key'));
                    $modal.find('.crop-dimensions-text, .crop-dimensions-label')
                        .text($btn.data('crop-w') + ' x ' + $btn.data('crop-h'));
                    $modal.find('.crop-preview-image')
                        .attr('src', $btn.data('preview-url'));

                    // Get filename from parent element
                    const filename = $btn.closest('.mediaclass').find('.name span:first').text();
                    $modal.find('.crop-filename').text($btn.data('crop-key') + '_' + filename);

                    // Set form values
                    const $form = $modal.find('#mediaclass-delete-crop-form');
                    $form.attr('data-ajax', ajaxUrl);
                    $form.attr('data-media-id', $btn.data('media-id'));
                    $form.attr('data-crop-key', $btn.data('crop-key'));
                }, 50);

                $modal.modal('show');
            } else {
                // Load crop editor
                $modal.find('.modal-body').load($btn.attr('href'), function () {
                    $modal.modal('show');
                });
            }
        });

        // Handle delete button click
        $modal.on('click', '#mediaclass-delete-crop-btn', function () {
            let c = MediaclassUploader.deleteCropForm();
            ajax('action=deleteCrop&media_id=' + c.attr('data-media-id') + '&crop_key=' + c.attr('data-crop-key'), $(c));
        });

        // Handle AJAX success
        $(document).on('ajaxSuccess.mediaclassCrop', function (_e, xhr) {
            const ct = (xhr.getResponseHeader('Content-Type') || '').toLowerCase();
            if (!ct.includes('application/json')) return;

            try {
                const res = JSON.parse(xhr.responseText);
                if (res.action === 'delete_crop') {
                    MediaclassUploader.deletedCrop(res);
                }
            } catch (e) {
                console.error('Error parsing JSON response', e);
            }
        });

        // Clean up on modal close
        $modal.on('hidden.bs.modal', function () {
            $(this).find('.modal-body').empty();
        });
    },


    hideModal() {
        setTimeout(() => {
            const $modalCrop = $('#mediaclass-crop');
            $modalCrop.modal('hide');

            $('body').on('hidden.bs.modal', '.modal', function () {
                $modalCrop.find('.modal-body').html('');
            });
        }, 1500);
    },
    cropped: function (result) {
        // Update the UI after cropping
        if (result.uploaded && result.uploaded.id) {
            var $mediaElement = $('#mediaclass-' + result.uploaded.id);

            // If we have a crop_key in the result, update that specific button
            if (result.crop_key) {
                const $cropButton = $mediaElement.find(`.crop[data-crop-key="${result.crop_key}"]`);

                if ($cropButton.length) {
                    // Add the 'cropped' class to indicate it's now cropped
                    $cropButton.addClass('cropped');

                    // Change the icon to the filled crop icon
                    $cropButton.find('i').first()
                        .removeClass('fa-solid fa-crop')
                        .addClass('fa-solid fa-crop-simple');

                    // Add the check mark icon if it doesn't exist
                    if (!$cropButton.find('.fa-circle-check').length) {
                        $cropButton.append(' <i class="fa-solid fa-circle-check check-icon"></i>');
                    }

                    // Update the preview URL data attribute if we have the new URL
                    if (result.urls && result.urls.xl) {
                        $cropButton.attr('data-preview-url', result.urls.xl);
                    }
                }
            }

            // Original code for updating other elements
            if (result.cropable_links) {
                // Replace the entire crop actions bar with the updated one
                $mediaElement.find('.crop-actions-bar').replaceWith(result.cropable_links);

                // Re-initialize the crop actions for the new buttons
                this.initCropActions();
            }

            // Update the preview image if new URL provided
            if (result.urls && result.urls.xl) {
                $mediaElement.find('.preview').css('background-image', `url(${result.urls.xl})`);
                $mediaElement.find('.zoom').attr('href', result.urls.xl);
            }
        }

        MediaclassUploader.hideModal();

    },

    // Method called after deleting a crop
    deletedCrop: function (result) {
        if (result.success && result.media_id && result.crop_key) {
            const $mediaElement = $('#mediaclass-' + result.media_id);

            // Find the specific crop button for this crop_key
            const $cropButton = $mediaElement.find(`.crop[data-crop-key="${result.crop_key}"]`);

            if ($cropButton.length) {
                // Remove the 'cropped' class to reset to uncropped state
                $cropButton.removeClass('cropped');

                // Change the icon from filled to regular crop icon
                $cropButton.find('i').first()
                    .removeClass('fa-solid fa-crop-simple')
                    .addClass('fa-solid fa-crop');

                // Remove the check mark icon if it exists
                $cropButton.find('.fa-circle-check').remove();

                // Clear the preview URL data attribute
                $cropButton.attr('data-preview-url', '');

                // Update the href to point to the crop editor instead of just modal
                const baseHref = $cropButton.attr('href');
                if (baseHref && !baseHref.includes('?')) {
                    const width = $cropButton.attr('data-crop-w');
                    const height = $cropButton.attr('data-crop-h');
                    $cropButton.attr('href', `${baseHref}?w=${width}&h=${height}&crop_key=${result.crop_key}`);
                }
            }
        }

        MediaclassUploader.hideModal();

    },

    initCropActions: function () {
        // Modal already handles the loading via href, just ensure it's properly initialized
        var $modalCrop = $('#mediaclass-crop');

        // Ensure modal content is cleared when hidden
        $modalCrop.off('hidden.bs.modal').on('hidden.bs.modal', function () {
            $(this).find('.modal-body').empty();
        });
    },


    init() {
        // Initialize positions for all uploadable elements
        $('.mediaclass-uploadable').each(function () {
            MediaclassUploader.positions($(this));
        });

        // Setup event handlers
        this.uploaderCall();
        this.unlinkable();
        this.modalCrop();
        this.initCropActions();
    },
};

// Initialize the module
MediaclassUploader.init();

// Callbacks
function mediaclassDeletedCrop(result) {
    MediaclassUploader.deletedCrop(result);
}

function mediaclassCropped(result) {
    MediaclassUploader.cropped(result);
}
