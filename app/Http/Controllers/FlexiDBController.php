<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FlexiDB\Parser;

class FlexiDBController extends Controller {

    public function parseRequest( Request $oRequest ) {
        if( !$oRequest->has( "actions" ) ) {
            return response()->json( [
                'message' => 'Invalid structure'
            ], 400 );
        }
        $aActions = request( 'actions' );
        if( !empty($aActions) && is_array($aActions) ) {
            $oQueryBuilder = new \App\FlexiDB\QueryBuilder();
            foreach( $aActions as $aAction ) {
                $oQueryBuilder->setParams( $aAction );
                $oQueryBuilder->buildTableSQL( 1 );
            }
        }
    }


}
