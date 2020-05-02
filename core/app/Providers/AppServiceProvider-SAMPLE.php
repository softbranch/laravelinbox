<!-- Start Copy Code -->
<?php

namespace App\Providers;

use App\Album;
use App\InboxwebmailAccount;
use App\Contact;
use App\Language;
use App\Setting;
use App\SocialIcon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        $general = array('title'=>'WEBMAIL-INBOX');
        $inboxwebmailMenu = InboxwebmailAccount::where('active',1 )->get();

        view()->share(compact('general', 'inboxwebmailMenu'));

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
///*End Copy Code/**/

