<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller {

    public function createUser( Request $oRequest ) {
        if( !$oRequest->user()->tokenCan('user-create') ) {
            return response()->json( [
                'message' => 'Your account lacks the permission needed for this action'
            ], 401 );
        }

        try {
            $oValidator = Validator::make( $oRequest->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required|min:6',
                'role' => 'sometimes'
            ] );

            if( $oValidator->fails() ) {
                return response()->json( [
                    'message' => $oValidator->errors()
                ], 401 );
            }

            $bCheck = User::where( 'email', $oRequest->email )->first();
            // return print_r( $bCheck, true );
            if( !empty($bCheck) )
            return response()->json( [ 'message' => 'This e-mail already exists in the database' ], 400 );

            $oUser = new User();
            $sPassword = Hash::make( $oRequest->password );
            $bResult = $oUser->create( [
                'name' => $oRequest->name,
                'email' => $oRequest->email,
                'password' => $sPassword,
                'role' => request( 'role', 'user' )
            ] );
            if( !empty($bResult) ) {
                return response()->json( [
                    'message' => 'user created',
                    'user' => $bResult
                ], 200 );
            }


        } catch( \Exception $ex ) {
            return response()->json( [
                'message' => 'An internal error occured'
            ], 500 );
        }

    }

    public function login( Request $oRequest ) {
        $oValidator = Validator::make( $oRequest->only( ['email', 'password'] ), [
            'email' => 'email|required',
            'password' => 'required'
        ] );
        if( $oValidator->fails() ) {
            return response()->json( [
                'message' => $oValidator->errors()
            ], 401 );
        }

        if( !\Auth::attempt( $oRequest->all() ) ) {
            return response()->json( [
                'message' => 'Invalid credentials'
            ], 401 );
        }

        $oUser = User::where( 'email', $oRequest->email )->first();
        if( !Hash::check( $oRequest->password, $oUser->password, [] ) ) {
            return response()->json( [
                'message' => 'Invalid credentials'
            ], 401 );
        }

        $aTokenAbilities = generateAbilities( $oUser );
        $mToken = $oUser->createToken( 'authToken', $aTokenAbilities )->plainTextToken;

        return response()->json( [
            'access_token' => $mToken,
            'token_type' => 'Bearar',
            'user' => $oUser
        ], 200 );
    }

    public function logout( Request $oRequest ) {
        $oUser = $oRequest->user();
        $bResult = $oUser->tokens()->delete();
        return response()->json( [
            'message' => ""
        ], ( $bResult ? 200 : 400 ) );
    }

    public function userTables( Request $oRequest ) {
        $oUser = $oRequest->user();
        $aTables = UserTables::where( 'user_id', $oUser->id )->get();
        return response()->json( [
            'message' => $aTables
        ], 200 );
    }

}
