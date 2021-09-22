<?php

use App\Models\User;
use Illuminate\Http\Request;

function generateAbilities( User $oUser ) {
    $mAbilities = Config( 'permissions.abilities' );
    $aAbilities = $mAbilities[$oUser->role];

    // $aOverrides = $aAbilities['overrides'];
    // unset( $aAbilities['overrides'] );
    $aOverrides = [];

    $aInheritance = $aAbilities['inherits'];
    unset( $aAbilities['inherits'] );

    $aTokenAbilities = [];

    $aAbilityTree = Config( 'permissions.abilities' );
    if( !empty($aInheritance) ) {
        foreach( $aInheritance as $sKey => $mValue ) {
            $aChildren = $aAbilityTree[$mValue];

            foreach( $aChildren as $sChildKey => $bChildValue ) {
                if( $sChildKey == 'inherits' ) continue;
                if( $bChildValue == true ) {
                    if( !in_array( $sChildKey, $aTokenAbilities ) ) {
                        array_push( $aTokenAbilities, $sChildKey );
                    } else {
                        continue;
                    }

                } else {
                    continue;
                }
            }
        }
    }
    foreach( $aAbilities as $sKey => $mValue ) {
        if( $mValue == true ) {
            array_push( $aTokenAbilities, $sKey );
        }
    }

    if( !empty($aOverrides) ) {
        if( array_key_exists( $oUser->email, $aOverrides ) ) {
            // TODO: override the initial tokenabilities and unset/set abilities.
            $aOverridesAbilities = $aOverrides[$oUser->email];
            foreach( $aOverridesAbilities as $sKey => $mValue ) {
                if( $mValue == true ) {
                    if( !in_array($sKey, $aTokenAbilities) ) {
                        array_push( $aTokenAbilities, $sKey );
                    } else {
                        continue;
                    }
                } else if( $mValue == false ) {
                    if( in_array($sKey, $aTokenAbilities) ) {
                        if( ($key = array_search($sKey, $aTokenAbilities)) !== false ) {
                            unset( $aTokenAbilities[$key]);
                        }
                    }
                }
            }
        }
    }
    return $aTokenAbilities;
}

function checkModulePrivilege( Request $oRequest, $sModule = '', $sMethod = '' ) {
    $sControllerPrefix = strtolower( $sModule );
    if( !$oRequest->user()->tokenCan( sprintf( "%s-%s", $sControllerPrefix, $sMethod ) ) ) {
        return false;
    } else {
        return true;
    }
    return false; // Should never reach this, but just in case the code actually reaches this statement, return false.
}
