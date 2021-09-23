<?php

namespace App\VQL;

use App\Models\UserTables;
use App\VQL\QueryBuilder;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VQLParser {

    protected $aActions = [];

    protected $aErrors = [];
    protected $aMessages = [];

    protected $aFunctions = [
        'create',
        'drop',
        'insert',
        'read',
        'update',
        'delete'
    ];
    protected $aValidationRules = [
        'create' => [
            "table" => "required",
            "fields" => "array",
            "fields.*.name" => "required",
            "fields.*.type" => "required|in:int,integer,string,varchar,double,decimal,enum,array,date,time,datetime,timestamp",
            "fields.*.nullable" => "sometimes|boolean",
            "fields.*.autoinc" => "sometimes|boolean",
            "fields.*.values" => "sometimes|array",
            "fields.*.default" => "sometimes",
            "fields.*.precision" => "sometimes",
            "key" => "required"
        ],
        'drop' => [
            "table" => "required"
        ],
        'insert' => [
            "table" => "required",
            "fields" => "required|array",
            "values" => "required|array",
            "values.*.*" => "required"
        ],
        'read' => [
            "table" => "required",
            "fields" => "required|array",
            "selector" => "required",
            "value" => "required"
        ],
        'update' => [
            "table" => "required",
            "fields" => "required|array",
            "values" => "required|array",
            "values.*.*" => "required",
            "selector" => "required",
            "value" => "required"
        ],
        'delete' => [
            "table" => "required",
            "selector" => "required",
            "value" => "required"
        ]
    ];

    protected $iUserId = "";
    protected $sQuery = "";
    protected $sCurrentTable = "";

    protected Request $oRequest;
    protected QueryBuilder $oQueryBuilder;

    public function __construct() {
        $this->oQueryBuilder = new QueryBuilder;
    }

    public function setRequest( Request $oRequest ) {
        $this->oRequest = $oRequest;
    }

    public function parse() {
        // Split the actions and validate each request.
        $this->splitActions();

        if( !empty($this->aErrors) )
            return response()->json( [ 'errors' => $this->aErrors], 400 );

        $oUser = $this->oRequest->user();
        $this->oQueryBuilder->setUserId( $oUser->id );
        $this->iUserId = $oUser->id;
        // Iterate over the actions, and create SQL for each action
        foreach( $this->aActions as $aAction ) {
            $sActionType = $aAction['action'];
            $this->oQueryBuilder->setParams( $aAction );
            switch( $sActionType ) {
                case 'create':
                    $this->sCurrentTable = "user_" . $oUser->id . "_" . $aAction['table'];
                    $this->sQuery = $this->oQueryBuilder->buildTableSQL();
                    $this->executeQueryResult('create');
                    break;
                case 'drop':
                    $this->sCurrentTable = "user_" . $oUser->id . "_" . $aAction['table'];
                    $this->sQuery = $this->oQueryBuilder->buildDropTableSQL();
                    $this->executeQueryResult('drop');
                    break;
                case 'insert':
                    $this->sCurrentTable = "user_" . $oUser->id . "_" . $aAction['table'];
                    $this->sQuery = $this->oQueryBuilder->buildInsertSQL();
                    $this->executeQueryResult('insert');
                    break;
                case 'read':
                    $this->sCurrentTable = "user_" . $oUser->id . "_" . $aAction['table'];
                    $this->sQuery = $this->oQueryBuilder->buildReadSQL();
                    $this->executeQueryResult('read');
                    break;
                case 'update':
                    $this->sCurrentTable = "user_" . $oUser->id . "_" . $aAction['table'];
                    $this->sQuery = $this->oQueryBuilder->buildUpdateSQL();
                    $this->executeQueryResult('update');
                    break;
                case 'delete':
                    $this->sCurrentTable = "user_" . $oUser->id . "_" . $aAction['table'];
                    $this->sQuery = $this->oQueryBuilder->buildDeleteSQL();
                    $this->executeQueryResult('delete');
                    break;
                default:
                    $this->aErrors[] = "Unknown action, how did this get past validation?";
                    break;
            }
        }

        if( !empty($this->aErrors) ) {
            return response()->json( [ 'errors' => $this->aErrors], 400 );
        } else {
            return response()->json( [ 'message' => $this->aMessages], 200 );
        }
    }

    private function splitActions() {
        $aRequestActions = $this->oRequest->actions;
        foreach( $aRequestActions as $aAction ) {
            // We should validate each action based on the action-type
            $sActionType = $aAction['action'];
            if( !in_array( $sActionType, $this->aFunctions ) ) {
                $this->aErrors[] = 'Invalid action specified: ' . $sActionType;
                return;
            }
            $oValidator = Validator::make( $aAction, $this->aValidationRules[$sActionType] );
            if( $oValidator->fails() ) {
                $this->aErrors[] = $oValidator->errors();
                return;
            }
            $this->aActions[] = $aAction;
        }
    }

    public function executeQueryResult( $sAction = "" ) {
        // Check if the table exists
        $bTableExists = false;
        $oExists = DB::select( DB::raw('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.`TABLES` WHERE TABLE_SCHEMA = "' . Config('flexidb.base_table') . '" AND TABLE_NAME = "' . $this->sCurrentTable . '"') );
        if( !empty($oExists) ) {
            $oExists = current($oExists);
            if( $oExists->TABLE_NAME == $this->sCurrentTable )
                $bTableExists = true;
        }
        if( $sAction == "create" && !$bTableExists ) {
            // Check if the table already exists
            DB::beginTransaction();
            $oResult = DB::statement( $this->sQuery );
            DB::commit();
            $this->aMessages[] = [ 'create' => true ];
            $oTable = UserTables::create( [
                'user_id' => $this->iUserId,
                'table_name' => $this->sCurrentTable
            ] );
            return;
        } else if( $sAction == "create" && $bTableExists ) {
            return;
        }
        if( $sAction == "drop" && $bTableExists ) {
            $oTable = UserTables::where( [
                'user_id' => $this->iUserId,
                'table_name' => $this->sCurrentTable
            ] )->first();
            $oTable->delete();
            DB::beginTransaction();
            $oResult = DB::statement( $this->sQuery );
            DB::commit();
            $this->aMessages[] = [ 'drop' => true ];
            return;
        }

        if( !$bTableExists ) {
            $this->aErrors[] = "Base-table ({$this->sCurrentTable}) doesn't exist";
            return;
        }
        if( $sAction == "insert" ) {
            DB::beginTransaction();
            $oResult = DB::statement( $this->sQuery );
            DB::commit();
            if( $oResult ) {
                $this->aMessages[] = [ 'insert' => true ];
            }
            return;
        }
        if( $sAction == "read" ) {
            $oData = DB::select( DB::raw($this->sQuery) );
            $this->aMessages[] = [ 'read' => json_decode( json_encode( $oData ), true ) ];
            return;
        }
        if( $sAction == "update" ) {
            DB::beginTransaction();
            $oResult = DB::statement( $this->sQuery );
            DB::commit();
            if( $oResult ) {
                $this->aMessages[] = [ 'update' => true ];
            }
            return;
        }
        if( $sAction == "delete" ) {
            DB::beginTransaction();
            $oResult = DB::statement( $this->sQuery );
            DB::commit();
            if( $oResult ) {
                $this->aMessages[] = [ 'delete' => true ];
            }
            return;
        }

        return;
    }

}
