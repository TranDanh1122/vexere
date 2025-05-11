<?php
$namespace = 'DreamTeam\JobStatus\Http\Controllers';
Route::namespace($namespace)->name('admin.')->prefix(config('app.admin_dir'))->middleware(['web', 'auth-admin', '2fa'])->group(function() {
    Route::post('job_statuses/delete', 'ProgressController@deleteWithRequest')->name('job_statuses.deleteWithRequest');
    Route::resource('job_statuses', 'ProgressController');
    Route::post('job_statuses/re-run/{jobUuid}', 'ProgressController@reRunjob')->name('job_statuses.reRunJob');
});
