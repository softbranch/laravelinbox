<!-- Start Copy Code -->
<?php

Route::get('/', 'HomeController@homePage')->name('homePage');
Route::get('/inboxwebmail/parse', 'InboxwebmailController@inboxwebmailParse')->name('inboxwebmail.parse');

Route::group(['prefix' => 'admin', 'middleware' => 'guest:admin', 'as' => 'admin.'], function () {
    Route::get('/', 'AdminDashboardController@loginForm')->name('login');
    Route::post('/', 'AdminDashboardController@login')->name('login.post');
});

Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin', 'as' => 'admin.'], function () {
    Route::get('dashboard', 'AdminDashboardController@dashboard')->name('dashboard');
    Route::get('profile', 'AdminDashboardController@profile')->name('profile');
    Route::post('profile', 'AdminDashboardController@profilePost')->name('profile.post');

    Route::get('change/password', 'AdminDashboardController@ChangePass')->name('change.password');
    Route::post('change/password', 'AdminDashboardController@ChangePassPost')->name('change.password.post');

    Route::post ('logout', 'AdminDashboardController@logout')->name('logout');;

    //Inboxwebmail
    Route::get('inboxwebmails', 'InboxwebmailController@index')->name('inboxwebmails');
    Route::get('add-new/inboxwebmail', 'InboxwebmailController@inboxwebmailAdd')->name('inboxwebmail.add');
    Route::post('add-new/inboxwebmail', 'InboxwebmailController@inboxwebmailPost')->name('inboxwebmail.post');
    Route::get('inboxwebmail/edit/{id}', 'InboxwebmailController@inboxwebmailEdit')->name('inboxwebmail.edit');
    Route::get('inboxwebmail/delete/{id}', 'InboxwebmailController@inboxwebmailDelete')->name('inboxwebmail.delete');
    Route::post('inboxwebmail/update/{id}', 'InboxwebmailController@inboxwebmailUpdate')->name('inboxwebmail.update');
    Route::post('inboxwebmail/labels/{id}', 'InboxwebmailController@inboxwebmailLabels')->name('inboxwebmail.labels');
    Route::post('inboxwebmail/label/delete', 'InboxwebmailController@inboxwebmailLabelDelete')->name('inboxwebmail.label.delete');
    Route::get('inboxwebmail/view/{uid}', 'InboxwebmailController@inboxwebmailView')->name('inboxwebmail.view');
    Route::post('inboxwebmail/view/{uid}', 'InboxwebmailController@inboxwebmailView')->name('inboxwebmail.view');
    Route::get('inboxwebmail/compose/{uid}', 'InboxwebmailController@inboxwebmailCompose')->name('inboxwebmail.compose');
    Route::get('inboxwebmail/refdata/{uid}', 'InboxwebmailController@inboxwebmailRefdata')->name('inboxwebmail.refdata');
    Route::post('inboxwebmail/composesend/{uid}', 'InboxwebmailController@inboxwebmailComposesend')->name('inboxwebmail.composesend');

});

///*admin auth end/**/
///*End Copy Code/**/













