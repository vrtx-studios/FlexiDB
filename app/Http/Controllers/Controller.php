<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private $sControllerName = "";
    public function getControllerName() {
        return $this->sControllerName;
    }
    public function setControllerName( $sControllerName ) {
        $this->sControllerName = $sControllerName;
    }

    /**
     * checkPrivilege
     *
     * Check if the current token can perform the selected method.
     *
     * @param Request $oRequest
     * @param string $sMethod
     * @return void
     */
    public function checkPrivilege( Request $oRequest, $sMethod = '' ) {
        $sControllerPrefix = strtolower( $this->sControllerName );
        if( !$oRequest->user()->tokenCan( sprintf( "%s-%s", $sControllerPrefix, $sMethod ) ) ) {
            return false;
        } else {
            return true;
        }
        return false; // Should never reach this, but just in case the code actually reaches this statement, return false.
    }

}
