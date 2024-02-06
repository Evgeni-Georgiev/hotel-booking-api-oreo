<?php

namespace App\Providers;

use App\Exceptions\BookingNotFoundException;
use App\Exceptions\CustomerNotFoundException;
use App\Exceptions\PaymentNotFoundException;
use App\Exceptions\RoomNotFoundException;
use App\Exceptions\UnavailableRoomException;
use App\Exceptions\UnavailableRoomForSpecifiedRangeException;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });

        Route::model('room', Room::class, function () {
            throw new RoomNotFoundException;
        });

        Route::model('booking', Booking::class, function () {
            throw new UnavailableRoomException();
        });

        Route::model('booking', Booking::class, function () {
            throw new UnavailableRoomForSpecifiedRangeException();
        });

        Route::model('payment', Payment::class, function () {
            throw new PaymentNotFoundException();
        });

        Route::model('customer', Customer::class, function () {
            throw new CustomerNotFoundException();
        });

        Route::model('booking', Booking::class, function () {
            throw new BookingNotFoundException();
        });
    }
}
