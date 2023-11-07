<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\AbstractUser;
use Laravel\Socialite\Facades\Socialite;

class SocialController extends Controller {
    public function redirect( $provider ) {
        return Socialite::driver( $provider )->redirect();
    }

    public function callback( $provider ) {
        /** @var AbstractUser $userSocial */
        $userSocial = Socialite::driver( $provider )->stateless()->user();
        $user      = User::where( [ 'email' => $userSocial->getEmail() ] )->first();
        if ( !$user ) {
            $user = User::create( [
                'name'               => $userSocial->getName(),
                'email'              => $userSocial->getEmail(),
                'password'           => $userSocial->token,
                'profile_photo_path' => $userSocial->getAvatar(),
                'provider_id'        => $userSocial->getId(),
                'provider'           => $provider,
            ] );
        }



        if ( $user ) {
            Auth::login( $user );

            return redirect( '/' );
        }

        return redirect()->route( 'home' );
    }
}
