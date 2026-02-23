<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return redirect()->route('admin.signin');
});

Route::middleware(['locale'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('', function () {
        return redirect()->route('admin.signin');
    });
    Route::livewire("sign-in", 'pages::admin.sign-in')->name('signin');
    Route::get('sign-out', function () {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.signin');
    })->name('signout');
    Route::middleware(['admin'])->group(function () {
        Route::livewire('dashboard', 'pages::admin.dashboard.index')->name('dashboard');
        Route::livewire('profile', 'pages::admin.profile.info')->name('profile');
        Route::livewire('change-password', 'pages::admin.profile.changepassword')->name('changepassword');

        // Route::prefix('docs')->name('doc.')->group(function () {
        //     Route::livewire("", 'pages::admin.doc.index')->name('index');
        //     Route::livewire("follow-up", 'pages::admin.doc.follow-up')->name('followup');
        //     Route::livewire("create", 'pages::admin.doc.create')->name('create');
        //     Route::livewire("{id}", 'pages::admin.doc.view')->name('view');
        //     Route::livewire("{id}/edit", 'pages::admin.doc.edit')->name('edit');
        //     Route::livewire("{id}/return", 'pages::admin.doc.return')->name('return');
        // });
        Route::prefix('note-documents')->name('note-document.')->group(function () {
            Route::livewire("", 'pages::admin.note_document.index')->name('index');
            Route::livewire("follow-up", 'pages::admin.note_document.follow-up')->name('followup');
            Route::livewire("create", 'pages::admin.note_document.create')->name('create');
            // Route::livewire("{id}", 'pages::admin.note_document.view')->name('view');
            Route::livewire("{id}/edit", 'pages::admin.note_document.edit')->name('edit');
            Route::livewire("{id}/return", 'pages::admin.note_document.return')->name('return');
            Route::name('send-to.')->group(function () {
                Route::livewire("{id}/send-to/{send_to_id}", 'pages::admin.note_document.send_to.view')->name('view');
                Route::livewire("{id}/send-to/{send_to_id}/edit", 'pages::admin.note_document.send_to.edit')->name('edit');
            });
        });
        Route::prefix('be-documents')->name('be-document.')->group(function () {
            Route::livewire("", 'pages::admin.be_document.index')->name('index');
            Route::livewire("follow-up", 'pages::admin.be_document.follow-up')->name('followup');
            Route::livewire("create", 'pages::admin.be_document.create')->name('create');
            // Route::livewire("{id}", 'pages::admin.be_document.view')->name('view');
            Route::livewire("{id}/edit", 'pages::admin.be_document.edit')->name('edit');
            Route::livewire("{id}/return", 'pages::admin.be_document.return')->name('return');
            Route::name('send-to.')->group(function () {
                Route::livewire("{id}/send-to/{send_to_id}", 'pages::admin.be_document.send_to.view')->name('view');
                Route::livewire("{id}/send-to/{send_to_id}/edit", 'pages::admin.be_document.send_to.edit')->name('edit');
            });
        });
        Route::prefix('users')->name('user.')->group(function () {
            Route::livewire("", 'pages::admin.user.index')->name('index');
            Route::livewire("create", 'pages::admin.user.create')->name('create');
            Route::livewire("{id}/edit", 'pages::admin.user.edit')->name('edit');
        });
        Route::prefix('departments')->name('department.')->group(function () {
            Route::livewire("", 'pages::admin.department.index')->name('index');
            Route::livewire("create", 'pages::admin.department.create')->name('create');
            Route::livewire("{id}/edit", 'pages::admin.department.edit')->name('edit');
        });
        Route::prefix('personels')->name('personel.')->group(function () {
            Route::livewire("", 'pages::admin.personel.index')->name('index');
            Route::livewire("create", 'pages::admin.personel.create')->name('create');
            Route::livewire("{id}/edit", 'pages::admin.personel.edit')->name('edit');
        });
        Route::prefix('genderal-departments')->name('gd.')->group(function () {
            Route::livewire("", 'pages::admin.gd.index')->name('index');
            Route::livewire("create", 'pages::admin.gd.create')->name('create');
            Route::livewire("{id}/edit", 'pages::admin.gd.edit')->name('edit');
        });
        Route::middleware(['is_system'])->group(function () {
            Route::prefix('settings')->name('setting.')->group(function () {
                Route::prefix('roles')->name('role.')->group(function () {
                    Route::livewire("", 'pages::admin.settings.role.index')->name('index');
                    Route::livewire("create", 'pages::admin.settings.role.create')->name('create');
                    Route::livewire("{id}/edit", 'pages::admin.settings.role.edit')->name('edit');
                });
                Route::prefix('users')->name('users.')->group(function () {
                    Route::livewire("", 'pages::admin.settings.users.index')->name('index');
                    Route::livewire("create", 'pages::admin.settings.users.create')->name('create');
                    Route::livewire("{id}/edit", 'pages::admin.settings.users.edit')->name('edit');
                });
            });
        });
    });
});
Route::get('/view-pdf/{filename}', function ($filename) {
    $path = storage_path('app/public/files/' . $filename);
    if (!Storage::disk('public')->exists('files/' . $filename)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'inline; filename="' . $filename . '"'
    ]);
})->name('view-pdf');

Route::fallback(function () {
    // return view('pages::errors.⚡404');
    abort(404);
});
