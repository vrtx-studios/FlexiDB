<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\VQL\VQLParser;

class FlexiDBController extends Controller {

    public function parseRequest( Request $oRequest ) {
        if( !$oRequest->has( "actions" ) ) {
            return response()->json( [
                'message' => 'Invalid structure'
            ], 400 );
        }
        $oParser = new VQLParser();
        $oParser->setRequest( $oRequest );
        return $oParser->parse();
    }


}
