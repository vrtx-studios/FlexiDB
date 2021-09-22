<?php

namespace App\FlexiDB;

class QueryBuilder {

    protected $aParams = [];

    public function setParams( $aParams = [] ) {
        $this->aParams = $aParams;
    }


    public function buildTableSQL( $iUserId ) {
        $sTableName = "user_" . $iUserId . "_" . $this->aParams['table'];
        $aQuery = [];
        $aQuery[] = "CREATE TABLE $sTableName (";
        $aFields = $this->aParams['fields'];
        $aQueryFields = [];
        foreach( $aFields as $aField ) {
            $aQueryFields[] = $this->getFieldSQL( $aField['name'], $aField['type'], $aField );
        }
        $aQuery[] = implode( ",", $aQueryFields );
        if( isset($this->aParams['key']) ) {
            $aQuery[] = "PRIMARY KEY ( " . $this->aParams['key'] . " )";
        }
        $aQuery[] = ")";
        dd( implode( "\n", $aQuery ) );
    }

    public function buildDropTableSQL() {

    }

    public function buildInsertSQL() {

    }

    public function buildReadSQL() {

    }

    public function buildUpdateSQL() {

    }

    public function buildDeleteSQL() {

    }

    private function getParameter( $sParam ) {
        if( isset($this->aParams[$sParam]) ) {
            return $this->aParams[$sParam];
        } else {
            return null;
        }
    }

    private function getFieldSQL( $sFieldName, $sFieldType, $aFieldParams = [] ) {
        switch( $sFieldType ) {
            case 'int':
            case 'integer':
                return $this->intField( $sFieldName, $aFieldParams );
            case 'enum':
            case 'array':
                return $this->enumField( $sFieldName, $aFieldParams );
                break;
            case 'string':
            case 'varchar':
            default:
                return $this->textField( $sFieldName, $aFieldParams );

        }
    }

    private function textField( $sFieldName, $aFieldParams ) {
        $aReturn = [];
        if( isset($aFieldParams['length']) && $aFieldParams['length'] > 1 ) {
            $aReturn[] = "$sFieldName VARCHAR(" . $aFieldParams['length'] . ")";
        } else {
            $aReturn[] = "$sFieldName VARCHAR(100)";
        }
        if( isset($aFieldParams['nullable']) && $aFieldParams['nullable'] == true ) {
            $aReturn[] = "NOT NULL";
        }
        return implode( " ", $aReturn );
    }

    private function intField( $sFieldName, $aFieldParams ) {
        $aReturn = [];
        $aReturn[] = "$sFieldName INT";
        if( isset($aFieldParams['nullable']) && $aFieldParams['nullable'] == true ) {
            $aReturn[] = "NOT NULL";
        }
        if( isset($aFieldParams['autoinc']) && $aFieldParams['autoinc'] == true ) {
            $aReturn[] = "AUTO_INCREMENT";
        }
        return implode( " ", $aReturn );
    }

}
