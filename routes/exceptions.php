<?php

Route::prefix('exceptions')->name('exceptions.')->group(function() {
    Route::get('post_too_large', function () {
        return view('errors.exception')->with('error_msg', "Le fichier que vous avez essayé de télécharger est trop volumineux, le serveur ne peut pas le traiter. Réduisez sa taille.");
    })->name('post_too_large');

    Route::get('generic', function () {
        return view('errors.exception');
    })->name('generic');
});